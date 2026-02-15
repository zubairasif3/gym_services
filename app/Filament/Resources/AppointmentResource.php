<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationGroup = 'Appointments';
    
    protected static ?int $navigationSort = 2;

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
                    
                Forms\Components\Section::make('Appointment Details')
                    ->schema([
                        Forms\Components\DatePicker::make('appointment_date')
                            ->label('Date')
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\TimePicker::make('appointment_time')
                            ->label('Time')
                            ->required()
                            ->seconds(false)
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('confirmed')
                            ->disabled(fn ($record) => $record && $record->status === 'cancelled'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Client Information')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_surname')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('client_date_of_birth')
                            ->label('Date of Birth')
                            ->required()
                            ->maxDate(now()->subDay()),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('External Appointment Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_external')
                            ->label('External Appointment (Not visible to clients)')
                            ->default(false),
                        Forms\Components\ColorPicker::make('external_color')
                            ->label('Calendar Color')
                            ->default('#00b3f1')
                            ->visible(fn ($get) => $get('is_external')),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Cancellation Details')
                    ->schema([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('Reason')
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
                    ->label('Service')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client Name')
                    ->formatStateUsing(fn ($record) => $record->client_name . ' ' . $record->client_surname)
                    ->searchable(['client_name', 'client_surname']),
                Tables\Columns\TextColumn::make('client_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('appointment_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment_time')
                    ->label('Time')
                    ->time('h:i A'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'info' => 'completed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested At')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\Filter::make('appointment_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Date From'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Date Until'),
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
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'confirmed']);
                        $record->client->notify(new \App\Notifications\AppointmentConfirmed($record));
                        
                        Notification::make()
                            ->title('Appointment confirmed successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'confirmed']))
                    ->form([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('Cancellation Reason')
                            ->placeholder('Optional reason for cancellation'),
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
                            ->title('Appointment cancelled successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
