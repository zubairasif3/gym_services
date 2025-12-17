<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GigShare extends Model
{
    protected $fillable = [
        'gig_id',
        'user_id',
        'platform',
        'ip_address',
    ];

    /**
     * Get the gig that was shared
     */
    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }

    /**
     * Get the user who shared the gig (nullable)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get shares for a specific gig
     */
    public function scopeForGig($query, $gigId)
    {
        return $query->where('gig_id', $gigId);
    }

    /**
     * Scope to filter by platform
     */
    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get recent shares
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get share count by platform for a gig
     */
    public static function getShareCountByPlatform($gigId)
    {
        return static::where('gig_id', $gigId)
            ->selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform')
            ->toArray();
    }
}
