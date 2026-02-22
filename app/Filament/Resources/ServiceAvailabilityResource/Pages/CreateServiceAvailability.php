<?php

namespace App\Filament\Resources\ServiceAvailabilityResource\Pages;

use App\Filament\Resources\ServiceAvailabilityResource;
use App\Models\ServiceAvailability;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateServiceAvailability extends CreateRecord
{
    protected static string $resource = ServiceAvailabilityResource::class;

    /** When repeat is used, number of records created (for notification and redirect). */
    public ?int $createdCount = null;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $repeatType = $data['repeat_type'] ?? 'none';
        $repeatEndDate = $data['repeat_end_date'] ?? null;

        $payload = collect($data)->only(['service_id', 'availability_date', 'start_time', 'end_time', 'is_active'])->all();
        $payload['is_active'] = $payload['is_active'] ?? true;
        $startTime = $payload['start_time'] instanceof \Carbon\Carbon
            ? $payload['start_time']->format('H:i')
            : Carbon::parse($payload['start_time'])->format('H:i');
        $endTime = $payload['end_time'] instanceof \Carbon\Carbon
            ? $payload['end_time']->format('H:i')
            : Carbon::parse($payload['end_time'])->format('H:i');
        $payload['start_time'] = $startTime;
        $payload['end_time'] = $endTime;

        if ($repeatType === 'none' || ! in_array($repeatType, ['daily', 'weekly'], true) || ! $repeatEndDate) {
            $payload['availability_date'] = $payload['availability_date'] instanceof \Carbon\Carbon
                ? $payload['availability_date']->format('Y-m-d')
                : Carbon::parse($payload['availability_date'])->format('Y-m-d');
            $record = $this->getModel()::create($payload);
            $this->createdCount = 1;
            return $record;
        }

        $startDate = $payload['availability_date'] instanceof \Carbon\Carbon
            ? $payload['availability_date']
            : Carbon::parse($payload['availability_date']);
        $endDate = Carbon::parse($repeatEndDate);
        $step = $repeatType === 'weekly' ? 7 : 1;
        $current = $startDate->copy();
        $last = null;
        $created = 0;

        while ($current->lte($endDate)) {
            $dateStr = $current->toDateString();
            if (ServiceAvailability::canCreateSlot((int) $payload['service_id'], $dateStr, $startTime, $endTime)) {
                $last = $this->getModel()::create([
                    'service_id' => $payload['service_id'],
                    'availability_date' => $dateStr,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_active' => $payload['is_active'],
                ]);
                $created++;
            }
            $current->addDays($step);
        }

        $this->createdCount = $created;
        if ($last !== null) {
            return $last;
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'repeat_end_date' => 'No availabilities could be created: all selected dates have overlapping slots or already have the maximum slots per hour.',
        ]);
    }

    public function getRedirectUrl(): string
    {
        if ($this->createdCount !== null && $this->createdCount > 1) {
            return static::getResource()::getUrl('index');
        }
        return parent::getRedirectUrl();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        if ($this->createdCount !== null && $this->createdCount > 1) {
            return "Created {$this->createdCount} availabilities.";
        }
        return parent::getCreatedNotificationTitle();
    }
}
