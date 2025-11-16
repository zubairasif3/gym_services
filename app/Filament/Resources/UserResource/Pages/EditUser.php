<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use App\Models\UserProfile;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Redirect Customer and Professional users to view page
        if (in_array($this->record->user_type, [2, 3])) {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $record]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->user_type == 1), // Only allow delete for Admin users
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Prevent updating Customer and Professional users
        if (in_array($record->user_type, [2, 3])) {
            return $record;
        }
        
        if($record->profile){
            $record->profile->update($data['profile']);
        }else{
            $data['profile']['user_id'] = $record->id;
            UserProfile::create($data['profile']);
        }
        $record->update($data);
        return $record;
    }
}
