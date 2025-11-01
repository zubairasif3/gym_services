<?php

namespace App\Filament\Resources\GigResource\Pages;

use App\Filament\Resources\GigResource;
use App\Models\GigImage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGig extends EditRecord
{
    protected static string $resource = GigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing images for the edit form
        $existingImages = $this->record->images()->orderBy('display_order')->pluck('image_path')->toArray();
        $data['gig_images'] = $existingImages;
        
        return $data;
    }

    protected function afterSave(): void
    {
        $gigImages = $this->data['gig_images'] ?? [];
        
        // Delete existing images
        $this->record->images()->delete();
        
        // Create new images
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove gig_images from the main data array since it's not a direct field on the gig table
        unset($data['gig_images']);
        return $data;
    }
}
