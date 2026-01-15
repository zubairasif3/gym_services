<?php

namespace App\Filament\Resources;

use App\Models\Service;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Promotion;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;

use Filament\Forms\Components\Currency;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\NumberColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\PromotionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PromotionResource\RelationManagers;
use Filament\Forms\Components\Grid;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Promotion Fields
                                Select::make('service_id')
                                    ->label('Select Service')
                                    ->relationship('service', 'title')
                                    ->required(),

                                TextInput::make('rate_per_impression')
                                    ->label('Rate Per Impression')
                                    ->required()
                                    ->minValue(0.01)
                                    ->prefix('$')
                                    ->numeric(),

                                TextInput::make('impressions')
                                    ->label('Impressions')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Automatically tracked when the promoted service is viewed. This field is read-only.'),

                                Checkbox::make('is_active')
                                    ->label('Is Active')
                                    ->default(true),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Columns for the table to display promotion details
                TextColumn::make('service.title')
                    ->label('Service Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rate_per_impression')
                    ->label('Rate Per Impression')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),

                TextColumn::make('impressions')
                    ->label('Impressions')
                    ->sortable()
                    ->default(0)
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0)),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->default(true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
