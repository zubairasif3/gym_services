<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subcategory;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubCategoryResource\Pages;
use App\Filament\Resources\SubCategoryResource\RelationManagers;

class SubCategoryResource extends Resource
{
    protected static ?string $model = Subcategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Categories';

    protected static ?string $navigationLabel = 'Sub Categories';

    protected static ?string $label = 'Sub Category';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return  $user->user_type == 1;
    }


    protected static ?string $pluralLabel = 'Sub Categories';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(['default' => 2])
                            ->schema([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::all()->pluck('name', 'id'))
                                    ->required(),
                                TextInput::make('name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                // ->unique(SubCategory::class, 'slug')
                                // ->slugify('name')
                                // ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state)))
                                ,

                            Textarea::make('description')
                                ->maxLength(1000),

                            IconPicker::make('icon')
                                ->label('Icon')
                                ->sets(['heroicons', 'fontawesome-solid'])
                                ->columns([
                                    'default' => 5,
                                    'lg' => 5,
                                    '2xl' => 5,
                                ])
                                ->nullable(),

                            FileUpload::make('image')
                                ->label('Category Image')
                                ->image()
                                ->directory('subcategories')
                                ->imageEditor()
                                ->maxSize(5120) // 5MB
                                ->nullable()
                                ->columnSpanFull(),

                            // TextInput::make('display_order')
                            //     ->numeric()
                            //     ->default(0)
                            //     ->minValue(0),

                            Toggle::make('is_active')
                                ->default(true),
                        ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ImageColumn::make('image')
                //     ->label('Image')
                //     ->circular()
                //     ->size(50)
                //     ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                // BadgeColumn::make('display_order')
                //     ->sortable()
                //     ->color('primary')
                //     ->default('0'),
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
            'index' => Pages\ListSubCategories::route('/'),
            'create' => Pages\CreateSubCategory::route('/create'),
            'edit' => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }
}
