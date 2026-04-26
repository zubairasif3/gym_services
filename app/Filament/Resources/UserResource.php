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
    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.users');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.user.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.user.plural');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return  $user->user_type == 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin.sections.basic_information'))
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
                                        1 => __('admin.status.admin'),
                                        2 => __('admin.status.customer'),
                                        3 => __('admin.status.professional'),
                                    ])
                                    ->required()
                                    ->default(1)
                                    ->label(__('admin.fields.user_type'))
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),

                                TextInput::make('business_name')
                                    ->label(__('admin.fields.business_name'))
                                    ->maxLength(255)
                                    ->visible(fn ($record) => $record && $record->user_type == 3)
                                    ->disabled(fn ($record) => $record && in_array($record->user_type, [2, 3])),
                            ])
                    ]),
                
                // Customer-specific fields
                Section::make(__('admin.sections.customer_information'))
                    ->visible(fn ($record) => $record && $record->user_type == 2)
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                DatePicker::make('profile.date_of_birth')
                                    ->label(__('admin.fields.date_of_birth'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->date_of_birth);
                                        }
                                    }),

                                TextInput::make('profile.country')
                                    ->label(__('admin.fields.country'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->country);
                                        }
                                    }),

                                TextInput::make('profile.city')
                                    ->label(__('admin.fields.city'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->city);
                                        }
                                    }),

                                TextInput::make('profile.cap')
                                    ->label(__('admin.fields.zip_code'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->cap);
                                        }
                                    }),
                            ])
                    ]),

                // Professional-specific fields
                Section::make(__('admin.sections.professional_information'))
                    ->visible(fn ($record) => $record && $record->user_type == 3)
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                TextInput::make('profile.country')
                                    ->label(__('admin.fields.country'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->country);
                                        }
                                    }),

                                TextInput::make('profile.city')
                                    ->label(__('admin.fields.city'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->city);
                                        }
                                    }),

                                TextInput::make('profile.address')
                                    ->label(__('admin.fields.address'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->address);
                                        }
                                    }),

                                TextInput::make('profile.cap')
                                    ->label(__('admin.fields.zip_code'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->profile) {
                                            $component->state($record->profile->cap);
                                        }
                                    }),
                            ])
                    ]),

                // Professional Subcategories
                Section::make(__('admin.sections.professional_categories'))
                    ->visible(fn ($record) => $record && $record->user_type == 3)
                    ->schema([
                        Forms\Components\Placeholder::make('subcategories_display')
                            ->label(__('admin.fields.subcategories'))
                            ->content(function ($record) {
                                if (!$record) {
                                    return __('admin.messages.no_subcategories_assigned');
                                }
                                
                                $userSubcategories = $record->userSubcategories()->with('subcategory')->orderBy('priority')->get();
                                
                                if ($userSubcategories->isEmpty()) {
                                    return __('admin.messages.no_subcategories_assigned');
                                }
                                
                                $subcategoryNames = $userSubcategories->map(function ($userSubcategory) {
                                    return $userSubcategory->subcategory->name ?? __('admin.messages.unknown');
                                })->filter()->implode(', ');
                                
                                return $subcategoryNames ?: __('admin.messages.no_subcategories_assigned');
                            }),
                    ]),

                // Admin/General Profile Information (for Admin users only)
                Section::make(__('admin.sections.profile_information'))
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
                                    ->label(__('admin.fields.profile_pic'))
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
                    ->label(__('admin.fields.phone'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('business_name')
                    ->label(__('admin.fields.business_name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user_type_label')
                    ->label(__('admin.fields.user_type'))
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
