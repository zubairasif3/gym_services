<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceAvailabilityResource\Pages;
use App\Models\ServiceAvailability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class ServiceAvailabilityResource extends Resource
{
    protected static ?string $model = ServiceAvailability::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.appointments');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('service', function ($query) {
                $query->where('user_id', Filament::auth()->id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'title', fn (Builder $query) => $query->where('user_id', Filament::auth()->id())->where('is_active', true))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('availability_date')
                    ->label(__('admin.fields.date'))
                    ->required()
                    ->native(false)
                    ->displayFormat('M d, Y'),
                Forms\Components\Select::make('slot_duration_minutes')
                    ->label(__('admin.fields.slot_duration'))
                    ->options([
                        30 => '30 minutes',
                        60 => '1 hour',
                    ])
                    ->default(30)
                    ->required()
                    ->helperText(__('admin.messages.slot_duration_help')),
                Forms\Components\TimePicker::make('start_time')
                    ->label(__('admin.fields.start_time'))
                    ->required()
                    ->seconds(false)
                    ->minutesStep(30)
                    ->default('07:00')
                    ->native(false)
                    ->displayFormat('g:i A')
                    ->helperText(__('admin.messages.availability_start_help')),
                Forms\Components\TimePicker::make('end_time')
                    ->label(__('admin.fields.end_time'))
                    ->required()
                    ->seconds(false)
                    ->minutesStep(30)
                    ->default('18:00')
                    ->native(false)
                    ->displayFormat('g:i A')
                    ->helperText(__('admin.messages.availability_end_help'))
                    ->rules([
                        fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $start = $get('start_time');
                            if (! $start || ! $value) {
                                return;
                            }
                            $startC = \Carbon\Carbon::parse($start);
                            $endC = \Carbon\Carbon::parse($value);
                            if ($endC->lte($startC)) {
                                $fail('End time must be after start time.');
                            }
                        },
                    ]),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->default(true)
                    ->required(),
                Forms\Components\Section::make(__('admin.fields.repeat'))
                    ->description('Create the same slot on multiple dates (create only).')
                    ->schema([
                        Forms\Components\Select::make('repeat_type')
                            ->label(__('admin.fields.repeat'))
                            ->options([
                                'none' => 'No repeat',
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                            ])
                            ->default('none')
                            ->required()
                            ->native(false)
                            ->live(),
                        Forms\Components\DatePicker::make('repeat_end_date')
                            ->label(__('admin.fields.repeat_until_date'))
                            ->required(fn (Forms\Get $get) => in_array($get('repeat_type'), ['daily', 'weekly'], true))
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->minDate(fn (Forms\Get $get) => $get('availability_date') ? \Carbon\Carbon::parse($get('availability_date')) : null)
                            ->maxDate(now()->addMonths(6))
                            ->visible(fn (Forms\Get $get) => in_array($get('repeat_type'), ['daily', 'weekly'], true))
                            ->rules([
                                fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $repeatType = $get('repeat_type');
                                    if (! in_array($repeatType, ['daily', 'weekly'], true)) {
                                        return;
                                    }
                                    $start = $get('availability_date');
                                    if ($start && $value && \Carbon\Carbon::parse($value)->lt(\Carbon\Carbon::parse($start))) {
                                        $fail('Repeat until date must be on or after the start date.');
                                    }
                                },
                            ]),
                    ])
                    ->columns(2)
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.title')
                    ->label(__('admin.fields.service'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('availability_date')
                    ->label(__('admin.fields.date'))
                    ->date('M d, Y (l)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slot_duration_minutes')
                    ->label(__('admin.fields.slot'))
                    ->formatStateUsing(fn ($state) => $state == 60 ? '1 hour' : ($state == 30 ? '30 min' : '—')),
                Tables\Columns\TextColumn::make('start_time')
                    ->label(__('admin.fields.start_time'))
                    ->time('h:i A'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label(__('admin.fields.end_time'))
                    ->time('h:i A'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_id')
                    ->label(__('admin.fields.service'))
                    ->relationship('service', 'title', fn (Builder $query) => $query->where('user_id', Filament::auth()->id())),
                Tables\Filters\Filter::make('availability_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('admin.fields.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('admin.fields.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('availability_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('availability_date', '<=', $date));
                    }),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->placeholder(__('admin.status.all'))
                    ->trueLabel(__('admin.status.active_only'))
                    ->falseLabel(__('admin.status.inactive_only')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceAvailabilities::route('/'),
            'create' => Pages\CreateServiceAvailability::route('/create'),
            'edit' => Pages\EditServiceAvailability::route('/{record}/edit'),
        ];
    }
}
