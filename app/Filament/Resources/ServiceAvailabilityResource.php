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

    protected static ?string $navigationGroup = 'Appointments';

    protected static ?int $navigationSort = 3;

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
                    ->label('Date')
                    ->required()
                    ->native(false)
                    ->displayFormat('M d, Y'),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Start Time')
                    ->required()
                    ->seconds(false)
                    ->minutesStep(60)
                    ->default('07:00')
                    ->native(false)
                    ->displayFormat('g:i A')
                    ->helperText('Whole hours only (e.g. 7:00 AM)'),
                Forms\Components\TimePicker::make('end_time')
                    ->label('End Time')
                    ->required()
                    ->seconds(false)
                    ->minutesStep(60)
                    ->default('08:00')
                    ->native(false)
                    ->displayFormat('g:i A')
                    ->after('start_time')
                    ->helperText('Must be after start time (e.g. 8:00 AM)')
                    ->rules([
                        fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $start = $get('start_time');
                            if ($start && $value && \Carbon\Carbon::parse($value)->lte(\Carbon\Carbon::parse($start))) {
                                $fail('End time must be after start time.');
                            }
                        },
                    ]),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.title')
                    ->label('Service')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('availability_date')
                    ->label('Date')
                    ->date('M d, Y (l)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('h:i A'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('h:i A'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Service')
                    ->relationship('service', 'title', fn (Builder $query) => $query->where('user_id', Filament::auth()->id())),
                Tables\Filters\Filter::make('availability_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('availability_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('availability_date', '<=', $date));
                    }),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
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
