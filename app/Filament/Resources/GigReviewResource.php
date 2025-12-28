<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GigReviewResource\Pages;
use App\Filament\Resources\GigReviewResource\RelationManagers;
use App\Models\GigReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GigReviewResource extends Resource
{
    protected static ?string $model = GigReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationGroup = 'Services';
    
    protected static ?string $navigationLabel = 'Reviews';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $recordTitleAttribute = 'comment';

    public static function shouldRegisterNavigation(): bool
    {
        return  false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Review Information')
                    ->schema([
                        Forms\Components\Select::make('gig_id')
                            ->relationship('gig', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\Select::make('rating')
                            ->required()
                            ->options([
                                1 => '⭐ 1 - Poor',
                                2 => '⭐⭐ 2 - Fair',
                                3 => '⭐⭐⭐ 3 - Good',
                                4 => '⭐⭐⭐⭐ 4 - Very Good',
                                5 => '⭐⭐⭐⭐⭐ 5 - Excellent',
                            ])
                            ->default(5)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('helpful_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Review Content')
                    ->schema([
                        Forms\Components\Textarea::make('comment')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified Purchase')
                            ->helperText('Mark this review as verified purchase')
                            ->default(false),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created At')
                            ->content(fn ($record) => $record ? $record->created_at->format('M d, Y H:i') : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Updated At')
                            ->content(fn ($record) => $record ? $record->updated_at->format('M d, Y H:i') : '-'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gig.title')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reviewer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn (string $state): string => match ((int)$state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                    })
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('helpful_count')
                    ->label('Helpful')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ 5 Stars',
                        4 => '⭐⭐⭐⭐ 4 Stars',
                        3 => '⭐⭐⭐ 3 Stars',
                        2 => '⭐⭐ 2 Stars',
                        1 => '⭐ 1 Star',
                    ]),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified Purchase')
                    ->placeholder('All reviews')
                    ->trueLabel('Verified only')
                    ->falseLabel('Unverified only'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (GigReview $record) => $record->update(['is_verified' => true]))
                    ->visible(fn (GigReview $record) => !$record->is_verified),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('verify')
                        ->label('Mark as Verified')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_verified' => true])),
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
            'index' => Pages\ListGigReviews::route('/'),
            'create' => Pages\CreateGigReview::route('/create'),
            'edit' => Pages\EditGigReview::route('/{record}/edit'),
        ];
    }
}
