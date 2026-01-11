<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileMediaReaction extends Model
{
    protected $fillable = [
        'profile_media_id',
        'user_id',
        'emoji',
        'ip_address',
    ];

    /**
     * Get the profile media that owns the reaction
     */
    public function profileMedia(): BelongsTo
    {
        return $this->belongsTo(ProfileMedia::class);
    }

    /**
     * Get the user that owns the reaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
