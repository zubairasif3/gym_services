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

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.marketplace');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.promotion.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.promotion.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin.sections.basic_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Promotion Fields
                                Select::make('service_id')
                                    ->label(__('admin.fields.select_service'))
                                    ->relationship('service', 'title')
                                    ->required(),

                                TextInput::make('rate_per_impression')
                                    ->label(__('admin.fields.rate_per_impression'))
                                    ->required()
                                    ->minValue(0.01)
                                    ->prefix('$')
                                    ->numeric(),

                                TextInput::make('impressions')
                                    ->label(__('admin.fields.impressions'))
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText(__('admin.messages.promotion_impressions_help')),

                                Checkbox::make('is_active')
                                    ->label(__('admin.fields.is_active'))
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
                    ->label(__('admin.fields.service_title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rate_per_impression')
                    ->label(__('admin.fields.rate_per_impression'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),

                TextColumn::make('impressions')
                    ->label(__('admin.fields.impressions'))
                    ->sortable()
                    ->default(0)
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0)),

                BooleanColumn::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->sortable()
                    ->default(true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => __('admin.status.active'),
                        0 => __('admin.status.inactive'),
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
