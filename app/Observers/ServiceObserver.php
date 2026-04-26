<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\Service;
use Carbon\Carbon;

class ServiceObserver
{
    public function created(Service $service): void
    {
        if (! $service->is_active) {
            return;
        }

        $this->notifyFollowers($service, 'posted a new service');
    }

    public function updated(Service $service): void
    {
        // Notify when service becomes active (publish) OR when an already-active service changes meaningfully.
        if (! $service->is_active) {
            return;
        }

        if ($service->wasChanged('is_active')) {
            $this->notifyFollowers($service, 'posted a new service');
            return;
        }

        $meaningfulFields = [
            'title',
            'price',
            'description',
            'delivery',
            'category_id',
            'sub_category_id',
        ];

        foreach ($meaningfulFields as $field) {
            if ($service->wasChanged($field)) {
                $this->notifyFollowers($service, 'updated a service');
                return;
            }
        }
    }

    private function notifyFollowers(Service $service, string $verb): void
    {
        $professional = $service->user;
        if (! $professional || (int) ($professional->user_type ?? 0) !== 3) {
            return;
        }

        $username = $professional->username;
        $url = $username ? url('/@' . ltrim($username, '@')) : url('/');

        $followerIds = $professional->followers()
            ->where('user_type', 2)
            ->pluck('users.id');

        if ($followerIds->isEmpty()) {
            return;
        }

        $now = Carbon::now();
        $title = (string) ($service->title ?? '');

        $rows = $followerIds->map(function ($followerId) use ($professional, $service, $url, $now, $title, $verb) {
            return [
                'user_id' => $followerId,
                'type' => 'new_service',
                'data' => json_encode([
                    'message' => $title !== '' ? ($verb . ': ' . $title) : $verb,
                    'url' => $url,
                ]),
                'related_user_id' => $professional->id,
                'related_model_type' => Service::class,
                'related_model_id' => $service->id,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        Notification::insert($rows);
    }
}

