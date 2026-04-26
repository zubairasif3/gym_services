<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\ProfileMedia;
use Carbon\Carbon;

class ProfileMediaObserver
{
    public function created(ProfileMedia $media): void
    {
        // Notify followers only for active media by professionals.
        if (! $media->is_active) {
            return;
        }

        $professional = $media->user;
        if (! $professional || (int) ($professional->user_type ?? 0) !== 3) {
            return;
        }

        $username = $professional->username;
        $profileUrl = $username ? url('/@' . ltrim($username, '@')) : url('/');

        $followerIds = $professional->followers()
            ->where('user_type', 2)
            ->pluck('users.id');

        if ($followerIds->isEmpty()) {
            return;
        }

        $now = Carbon::now();

        $rows = $followerIds->map(function ($followerId) use ($professional, $media, $profileUrl, $now) {
            $url = $profileUrl . '?media_id=' . $media->id;

            return [
                'user_id' => $followerId,
                'type' => 'new_profile_media',
                'data' => json_encode([
                    'message' => 'posted new profile content',
                    'url' => $url,
                ]),
                'related_user_id' => $professional->id,
                'related_model_type' => ProfileMedia::class,
                'related_model_id' => $media->id,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        Notification::insert($rows);
    }
}

