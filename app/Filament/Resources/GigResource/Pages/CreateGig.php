<?php

namespace App\Filament\Resources\GigResource\Pages;

use App\Filament\Resources\GigResource;
use App\Models\GigImage;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGig extends CreateRecord
{
    protected static string $resource = GigResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $gigImages = $this->data['gig_images'] ?? [];
        
        if (!empty($gigImages)) {
            foreach ($gigImages as $index => $imagePath) {
                GigImage::create([
                    'gig_id' => $this->record->id,
                    'image_path' => $imagePath,
                    // 'display_order' => $index + 1,
                ]);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove gig_images from the main data array since it's not a direct field on the gig table
        unset($data['gig_images']);
        return $data;
    }
}
