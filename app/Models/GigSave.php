<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GigSave extends Model
{
    protected $fillable = [
        'gig_id',
        'user_id',
    ];

    /**
     * Get the gig that was saved
     */
    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }

    /**
     * Get the user who saved the gig
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get saves for a specific gig
     */
    public function scopeForGig($query, $gigId)
    {
        return $query->where('gig_id', $gigId);
    }

    /**
     * Scope to get saves by a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
