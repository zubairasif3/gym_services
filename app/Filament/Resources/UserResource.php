<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Relationship;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Users';
    protected static ?string $label = 'User';
    protected static ?string $pluralLabel = 'Users';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return  $user->user_type == 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->required()
                                    ->email()
                                    // ->unique(User::class, 'email')
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->required(
                                        fn(string $context): bool => $context === 'create'
                                    )
                                    ->dehydrated(fn($state) => filled($state))
                                    ->string()
                                    ->minLength(6)
                                    ->password(),

                                Select::make('user_type')
                                    ->options([
                                        1 => 'Admin',
                                        2 => 'Buyer',
                                        3 => 'Seller',
                                    ])
                                    ->required()
                                    ->default(1)
                                    ->label('User Type'),
                            ])
                    ]),
                Section::make('Profile Information')
                    ->label('Profile')
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                Textarea::make('profile.bio')
                                    ->nullable()
                                    ->maxLength(1000)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->bio);
                                        }
                                    }),

                                FileUpload::make('avatar_url')
                                    ->label('Profile Pic')
                                    ->nullable()
                                    ->image()
                                    ->directory('avartar'),
                                    // ->loadStateFromRelationshipsUsing(function ($component, $record) {
                                    //     if ($record?->profile?->profile_picture) {
                                    //         $component->state($record->profile->profile_picture);
                                    //     }
                                    // })
                                    // ->afterStateUpdated(function ($state, $record) {
                                    //     if ($record?->profile && $state) {
                                    //         $path = is_array($state) ? reset($state) : $state;
                                    //         $record->profile->update(['profile_picture' => $path]);
                                    //     }
                                    // }),

                                // Toggle::make('is_provider')
                                //     ->label('Is Provider')
                                //     ->default(false),

                                TextInput::make('profile.phone')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->phone);
                                        }
                                    }),

                                TextInput::make('profile.country')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->country);
                                        }
                                    }),

                                TextInput::make('profile.city')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->city);
                                        }
                                    }),

                                TextInput::make('profile.languages')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->languages);
                                        }
                                    }), // Can be a comma-separated list or JSON

                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('profile.phone')
                    ->label('Phone')
                    ->sortable(),

                TextColumn::make('user_type_label')
                    ->label('User Type')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
