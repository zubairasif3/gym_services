<?php

namespace App\Filament\Resources;

use App\Models\Gig;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GigResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GigResource\RelationManagers;

class GigResource extends Resource
{
    protected static ?string $model = Gig::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.marketplace');
    }
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make(__('admin.gig.service_details'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('admin.gig.basic_info'))
                            ->schema([
                                Forms\Components\Section::make(__('admin.sections.service_information'))
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->relationship('user', 'name', function(Builder $query){
                                                $user = auth()->user();
                                                if ($user && $user->user_type != 1) {
                                                    $query->where('id', $user->id);
                                                }
                                                return $query;
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Forms\Components\Select::make('subcategory_id')
                                            ->relationship('subcategory', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        // Hidden::make('subcategory_id')->default(1),

                                        Forms\Components\TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                if ($operation === 'create') {
                                                    $set('slug', Str::slug($state));
                                                }
                                            }),

                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        Forms\Components\RichEditor::make('description')
                                            ->required()
                                            ->columnSpanFull(),

                                        // Forms\Components\FileUpload::make('thumbnail')
                                        //     ->image()
                                        //     ->directory('gigs/thumbnails')
                                        //     ->imageEditor()
                                        //     ->maxSize(5120) // 5MB
                                        //     ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('admin.gig.pricing_delivery'))
                            ->schema([
                                Forms\Components\Section::make(__('admin.gig.pricing_details'))
                                    ->schema([
                                        Forms\Components\TextInput::make('starting_price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('€')
                                            ->minValue(1),

                                        Forms\Components\TextInput::make('delivery_time')
                                            ->required()
                                            ->numeric()
                                            ->suffix('days')
                                            ->minValue(1),

                                        // Forms\Components\Select::make('experience_level')
                                        //     ->options([
                                        //         'beginner' => 'Beginner',
                                        //         'intermediate' => 'Intermediate',
                                        //         'expert' => 'Expert',
                                        //     ])
                                        //     ->native(false),
                                    ])
                                    ->columns(3),

                                // Forms\Components\Section::make('Service Details')
                                //     ->schema([
                                //         Forms\Components\Repeater::make('what_included')
                                //             ->schema([
                                //                 Forms\Components\TextInput::make('feature')
                                //                     ->required(),
                                //             ])
                                //             ->columnSpanFull()
                                //             ->grid(2)
                                //             ->collapsed()
                                //             ->itemLabel(fn (array $state): ?string => $state['feature'] ?? null),

                                //         Forms\Components\Repeater::make('what_not_included')
                                //             ->schema([
                                //                 Forms\Components\TextInput::make('limitation')
                                //                     ->required(),
                                //             ])
                                //             ->columnSpanFull()
                                //             ->grid(2)
                                //             ->collapsed()
                                //             ->itemLabel(fn (array $state): ?string => $state['limitation'] ?? null),
                                //     ]),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('admin.gig.requirements_skills'))
                            ->schema([
                                Forms\Components\Section::make(__('admin.gig.requirements'))
                                    ->schema([
                                        Forms\Components\RichEditor::make('about')
                                            ->label(__('admin.fields.about_this_service'))
                                            ->columnSpanFull()
                                            ->helperText(__('admin.messages.about_service_help')),

                                        // Forms\Components\Repeater::make('requirements')
                                        //     ->schema([
                                        //         Forms\Components\TextInput::make('requirement')
                                        //             ->required(),
                                        //     ])
                                        //     ->columnSpanFull()
                                        //     ->collapsed()
                                        //     ->itemLabel(fn (array $state): ?string => $state['requirement'] ?? null),
                                    ]),

                                Forms\Components\Section::make(__('admin.gig.skills'))
                                    ->schema([
                                        // Forms\Components\TagsInput::make('skills')
                                        //     ->separator(',')
                                        //     ->columnSpanFull(),

                                        // Forms\Components\Select::make('tags')
                                        //     ->multiple()
                                        //     ->relationship('tags', 'name')
                                        //     ->createOptionForm([
                                        //         Forms\Components\TextInput::make('name')
                                        //             ->required()
                                        //             ->maxLength(255)
                                        //             ->live(onBlur: true)
                                        //             ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        //                 $set('slug', Str::slug($state));
                                        //             }),
                                        //         Forms\Components\TextInput::make('slug')
                                        //             ->required()
                                        //             ->maxLength(255)
                                        //             ->unique('gig_tags', 'slug', ignoreRecord: true),
                                        //     ])
                                        //     ->preload()
                                        //     ->searchable()
                                        //     ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('admin.gig.images'))
                            ->schema([
                                Forms\Components\Section::make(__('admin.gig.service_images'))
                                    ->schema([
                                        Forms\Components\FileUpload::make('gig_images')
                                            ->label(__('admin.fields.upload_images'))
                                            ->image()
                                            ->multiple()
                                            ->directory('gigs/images')
                                            ->imageEditor()
                                            ->maxFiles(10)
                                            ->reorderable()
                                            ->columnSpanFull()
                                            ->helperText(__('admin.messages.upload_service_images_help')),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('admin.gig.packages'))
                            ->schema([
                                Forms\Components\Section::make(__('admin.gig.service_packages'))
                                    ->schema([
                                        Forms\Components\Repeater::make('packages')
                                            ->relationship('packages')
                                            ->schema([
                                                // Forms\Components\Select::make('package_type')
                                                //     ->options([
                                                //         'basic' => 'Basic',
                                                //         'standard' => 'Standard',
                                                //         'premium' => 'Premium',
                                                //     ])
                                                //     ->required()
                                                //     ->native(false),
                                                Forms\Components\TextInput::make('title')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('description')
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Forms\Components\TextInput::make('price')
                                                    ->required()
                                                    ->numeric()
                                                    ->prefix('€'),
                                                Forms\Components\TextInput::make('delivery_time')
                                                    ->required()
                                                    ->numeric()
                                                    ->suffix('days'),
                                                Forms\Components\TextInput::make('revision_limit')
                                                    ->numeric()
                                                    ->suffix('revisions')
                                                    ->minValue(0)
                                                    ->required()
                                                    ->default(0),
                                                Forms\Components\TagsInput::make('features')
                                                    ->separator(',')
                                                    ->columnSpanFull(),
                                            ])
                                            ->defaultItems(3)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['package_type'] ?? '') . ': ' . ($state['title'] ?? '')
                                            )
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('admin.gig.stats_settings'))
                            ->schema([
                                Forms\Components\Section::make(__('admin.gig.visibility_settings'))
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->default(true)
                                            ->label(__('admin.fields.published'))
                                            ->helperText(__('admin.messages.published_help')),
                                        Forms\Components\Toggle::make('is_featured')
                                            ->default(false)
                                            ->label(__('admin.fields.featured'))
                                            ->helperText(__('admin.messages.featured_help')),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make(__('admin.gig.statistics'))
                                    ->schema([
                                        Forms\Components\Placeholder::make('impressions')
                                            ->content(fn (Gig $record): int => $record->impressions),
                                        Forms\Components\Placeholder::make('clicks')
                                            ->content(fn (Gig $record): int => $record->clicks),
                                        Forms\Components\Placeholder::make('rating')
                                            ->content(fn (Gig $record): string => $record->rating ? number_format($record->rating, 2) . '/5.00' : 'No ratings'),
                                        Forms\Components\Placeholder::make('ratings_count')
                                            ->content(fn (Gig $record): int => $record->ratings_count),
                                    ])
                                    ->columns(2)
                                    ->visibleOn('edit'),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // TextColumn::make('subcategory.name')
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('starting_price')
                    ->money('Euro')
                    ->sortable(),

                TextColumn::make('delivery_time')
                    ->suffix(' days')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->boolean()
                    ->label(__('admin.fields.featured'))
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('admin.fields.published'))
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
            'index' => Pages\ListGigs::route('/'),
            'create' => Pages\CreateGig::route('/create'),
            'edit' => Pages\EditGig::route('/{record}/edit'),
        ];
    }
}
