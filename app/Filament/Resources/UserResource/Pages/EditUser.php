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
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
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
