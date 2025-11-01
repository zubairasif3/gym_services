<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use App\Models\UserProfile;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function handleRecordCreation(array $data): Model
    {

        // dd($data);
        $user = static::getModel()::create($data);
        $data['profile']['user_id'] = $user->id;
        UserProfile::create($data['profile']);
        return $user;
    }
}
