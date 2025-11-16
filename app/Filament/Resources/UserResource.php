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
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Relationship;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Category;


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
                                    ->maxLength(255)
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),

                                TextInput::make('surname')
                                    ->maxLength(255)
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),

                                TextInput::make('username')
                                    ->maxLength(255)
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),

                                TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),

                                TextInput::make('password')
                                    ->required(
                                        fn(string $context): bool => $context === 'create'
                                    )
                                    ->dehydrated(fn($state) => filled($state))
                                    ->string()
                                    ->minLength(6)
                                    ->password()
                                    ->visible(fn ($record) => !$record || $record->user_type == 1)
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),

                                Select::make('user_type')
                                    ->options([
                                        1 => 'Admin',
                                        2 => 'Customer',
                                        3 => 'Professional',
                                    ])
                                    ->required()
                                    ->default(1)
                                    ->label('User Type')
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),

                                TextInput::make('business_name')
                                    ->label('Business Name')
                                    ->maxLength(255)
                                    ->visible(fn ($record) => $record && $record->user_type == 3)
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),
                            ])
                    ]),
                
                // Customer-specific fields
                Section::make('Customer Information')
                    ->visible(fn ($record) => $record && $record->user_type == 2)
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                DatePicker::make('profile.date_of_birth')
                                    ->label('Date of Birth')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->date_of_birth);
                                        }
                                    }),

                                TextInput::make('profile.country')
                                    ->label('Country')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->country);
                                        }
                                    }),

                                TextInput::make('profile.city')
                                    ->label('City')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->city);
                                        }
                                    }),

                                TextInput::make('profile.cap')
                                    ->label('Zip Code')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->cap);
                                        }
                                    }),
                            ])
                    ]),

                // Professional-specific fields
                Section::make('Professional Information')
                    ->visible(fn ($record) => $record && $record->user_type == 3)
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                TextInput::make('profile.country')
                                    ->label('Country')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->country);
                                        }
                                    }),

                                TextInput::make('profile.city')
                                    ->label('City')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->city);
                                        }
                                    }),

                                TextInput::make('profile.address')
                                    ->label('Address')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->address);
                                        }
                                    }),

                                TextInput::make('profile.cap')
                                    ->label('Zip Code')
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->cap);
                                        }
                                    }),
                            ])
                    ]),

                // Professional Subcategories
                Section::make('Professional Categories')
                    ->visible(fn ($record) => $record && $record->user_type == 3)
                    ->schema([
                        Forms\Components\Placeholder::make('subcategories_display')
                            ->label('Subcategories')
                            ->content(function ($record) {
                                if (!$record) {
                                    return 'No subcategories assigned';
                                }
                                
                                $userSubcategories = $record->userSubcategories()->with('subcategory')->orderBy('priority')->get();
                                
                                if ($userSubcategories->isEmpty()) {
                                    return 'No subcategories assigned';
                                }
                                
                                $subcategoryNames = $userSubcategories->map(function ($userSubcategory) {
                                    return $userSubcategory->subcategory->name ?? 'Unknown';
                                })->filter()->implode(', ');
                                
                                return $subcategoryNames ?: 'No subcategories assigned';
                            }),
                    ]),

                // Admin/General Profile Information (for Admin users only)
                Section::make('Profile Information')
                    ->visible(fn ($record) => !$record || $record->user_type == 1)
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
                                    }),
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

                TextColumn::make('surname')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('profile.phone')
                    ->label('Phone')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('business_name')
                    ->label('Business Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user_type_label')
                    ->label('User Type')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->user_type == 1), // Only allow edit for Admin users
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->user_type == 1), // Only allow delete for Admin users
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
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
