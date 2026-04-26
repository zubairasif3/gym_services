<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.appointments');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.appointment.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.appointment.plural');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show appointments for the logged-in professional
        return parent::getEloquentQuery()
            ->where('professional_id', Filament::auth()->id())
            ->where('is_external', false); // Exclude external appointments from list
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'title', fn (Builder $query) => $query->where('user_id', Filament::auth()->id()))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->disabled(fn ($record) => $record !== null),
                    
                Forms\Components\Section::make(__('admin.sections.appointment_details'))
                    ->schema([
                        Forms\Components\DatePicker::make('appointment_date')
                            ->label(__('admin.fields.date'))
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\TimePicker::make('appointment_time')
                            ->label(__('admin.fields.time'))
                            ->required()
                            ->seconds(false)
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => __('admin.status.pending'),
                                'confirmed' => __('admin.status.confirmed'),
                                'cancelled' => __('admin.status.cancelled'),
                                'completed' => __('admin.status.completed'),
                            ])
                            ->required()
                            ->default('confirmed')
                            ->disabled(fn ($record) => $record && $record->status === 'cancelled'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make(__('admin.sections.client_information'))
                    ->description(fn ($record) => $record ? __('admin.messages.client_read_only') : __('admin.messages.enter_client_details'))
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label(__('admin.fields.first_name'))
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\TextInput::make('client_surname')
                            ->label(__('admin.fields.last_name'))
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\TextInput::make('client_email')
                            ->label(__('admin.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\TextInput::make('client_phone')
                            ->label(__('admin.fields.phone'))
                            ->tel()
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\DatePicker::make('client_date_of_birth')
                            ->label(__('admin.fields.date_of_birth'))
                            ->required()
                            ->maxDate(now()->subDay())
                            ->disabled(fn ($record) => $record !== null),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('admin.sections.external_appointment_settings'))
                    ->schema([
                        Forms\Components\Toggle::make('is_external')
                            ->label(__('admin.fields.external_appointment'))
                            ->default(false),
                        Forms\Components\ColorPicker::make('external_color')
                            ->label(__('admin.fields.calendar_color'))
                            ->default('#00b3f1')
                            ->visible(fn ($get) => $get('is_external')),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('admin.sections.cancellation_details'))
                    ->schema([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label(__('admin.fields.reason'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record && $record->status === 'cancelled')
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.title')
                    ->label(__('admin.fields.service'))
                    ->size(TextColumnSize::ExtraSmall)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label(__('admin.fields.client_name'))
                    ->size(TextColumnSize::ExtraSmall)
                    ->formatStateUsing(fn ($record) => $record->client_name . ' ' . $record->client_surname)
                    ->searchable(['client_name', 'client_surname']),
                Tables\Columns\TextColumn::make('client_email')
                    ->label(__('admin.fields.email'))
                    ->size(TextColumnSize::ExtraSmall)
                    ->searchable(),
                Tables\Columns\TextColumn::make('appointment_date')
                    ->label(__('admin.fields.date'))
                    ->size(TextColumnSize::ExtraSmall)
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment_time')
                    ->label(__('admin.fields.time'))
                    ->size(TextColumnSize::ExtraSmall)
                    ->time('h:i A'),
                Tables\Columns\BadgeColumn::make('status')
                    ->size(TextColumnSize::ExtraSmall)
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'info' => 'completed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.fields.requested_at'))
                    ->size(TextColumnSize::ExtraSmall)
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => __('admin.status.pending'),
                        'confirmed' => __('admin.status.confirmed'),
                        'cancelled' => __('admin.status.cancelled'),
                        'completed' => __('admin.status.completed'),
                    ]),
                Tables\Filters\Filter::make('appointment_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(__('admin.fields.date_from')),
                        Forms\Components\DatePicker::make('date_until')
                            ->label(__('admin.fields.date_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label(__('admin.actions.confirm'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size(ActionSize::Small)
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'confirmed']);
                        $record->client->notify(new \App\Notifications\AppointmentConfirmed($record));
                        app(\App\Services\ChatService::class)->sendAppointmentConfirmationMessage($record);
                        
                        Notification::make()
                            ->title(__('admin.messages.appointment_confirmed'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label(__('admin.actions.cancel'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->size(ActionSize::Small)
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'confirmed']))
                    ->form([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label(__('admin.fields.cancellation_reason'))
                            ->placeholder(__('admin.messages.optional_cancellation_reason')),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'cancelled',
                            'cancelled_by' => 'professional',
                            'cancelled_at' => now(),
                            'cancellation_reason' => $data['cancellation_reason'] ?? null,
                        ]);
                        
                        $record->client->notify(new \App\Notifications\AppointmentCancelledByProfessional($record));
                        
                        Notification::make()
                            ->title(__('admin.messages.appointment_cancelled'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make()
                    ->size(ActionSize::Small),
                Tables\Actions\EditAction::make()
                    ->size(ActionSize::Small),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
