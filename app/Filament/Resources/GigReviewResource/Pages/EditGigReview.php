<?php

namespace App\Filament\Resources\GigReviewResource\Pages;

use App\Filament\Resources\GigReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGigReview extends EditRecord
{
    protected static string $resource = GigReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
