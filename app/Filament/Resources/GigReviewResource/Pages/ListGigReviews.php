<?php

namespace App\Filament\Resources\GigReviewResource\Pages;

use App\Filament\Resources\GigReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGigReviews extends ListRecords
{
    protected static string $resource = GigReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
