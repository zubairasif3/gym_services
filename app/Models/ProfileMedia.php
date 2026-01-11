<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'media_type',
        'file_path',
        'thumbnail_path',
        'duration',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the user that owns the media
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all reactions for this media
     */
    public function reactions()
    {
        return $this->hasMany(ProfileMediaReaction::class);
    }

    /**
     * Get reaction counts grouped by emoji
     */
    public function getReactionCountsAttribute()
    {
        return $this->reactions()
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->pluck('count', 'emoji')
            ->toArray();
    }

    /**
     * Scope to get only active media
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only images
     */
    public function scopeImages($query)
    {
        return $query->where('media_type', 'image');
    }

    /**
     * Scope to get only videos
     */
    public function scopeVideos($query)
    {
        return $query->where('media_type', 'video');
    }

    /**
     * Scope to order by custom order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }
}
