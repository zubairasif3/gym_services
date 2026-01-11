<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\Category;
use App\Models\Subcategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $navigationGroup = 'Marketplace';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Professional')
                            ->relationship('user', 'name', function(Builder $query){
                                $user = auth()->user();
                                // If not admin, only show current user
                                if ($user && $user->user_type != 1) {
                                    $query->where('id', $user->id);
                                }
                                // Only show professional users
                                $query->where('user_type', 3);
                                return $query;
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(function() {
                                $user = auth()->user();
                                return ($user && $user->user_type == 3) ? $user->id : null;
                            }),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(Category::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('sub_category_id', null)),

                        Forms\Components\Select::make('sub_category_id')
                            ->label('Subcategory')
                            ->options(function (callable $get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) {
                                    return [];
                                }
                                return Subcategory::where('category_id', $categoryId)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (callable $get) => !$get('category_id')),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Describe your service in detail'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing & Delivery')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Service price'),

                        Forms\Components\TextInput::make('delivery')
                            ->label('Delivery Time')
                            ->required()
                            ->numeric()
                            ->suffix('days')
                            ->minValue(1)
                            ->helperText('Expected delivery time in days'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Set whether this service is active and visible'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Professional')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->user->name . ' ' . $record->user->surname),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                // Tables\Columns\TextColumn::make('subcategory.name')
                //     ->label('Subcategory')
                //     ->sortable()
                //     ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivery')
                    ->label('Delivery')
                    ->sortable()
                    ->suffix(' days'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                // Tables\Filters\SelectFilter::make('sub_category_id')
                //     ->label('Subcategory')
                //     ->relationship('subcategory', 'name')
                //     ->searchable()
                //     ->preload(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Professional')
                    ->relationship('user', 'name', function(Builder $query){
                        $query->where('user_type', 3);
                    })
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
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
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        
        // If not admin, only show services belonging to the current user
        if ($user && $user->user_type != 1) {
            $query->where('user_id', $user->id);
        }
        
        return $query;
    }
}
