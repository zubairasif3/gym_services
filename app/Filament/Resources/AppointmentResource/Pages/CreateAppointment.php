<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically set professional_id to logged-in user (using Filament auth)
        $data['professional_id'] = Filament::auth()->id();
        
        // Set client_id same as professional for external appointments
        // For regular appointments, client_id should be set by the booking system
        if (!isset($data['client_id'])) {
            $data['client_id'] = Filament::auth()->id();
        }
        
        return $data;
    }
}
