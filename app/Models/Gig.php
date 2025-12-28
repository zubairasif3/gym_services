<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gig extends Model
{
    protected $fillable = [
        'user_id', 'subcategory_id', 'title', 'slug', 'description', 'about',
        'starting_price', 'thumbnail', 'delivery_time', 'requirements', 'skills',
        'experience_level', 'what_included', 'what_not_included',
        'is_featured', 'is_active', 'impressions', 'clicks', 'rating', 'ratings_count'
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
    ];

    /**
     * Get the images for this gig
     */
    public function images(): HasMany
    {
        return $this->hasMany(GigImage::class);
    }

    /**
     * Get the packages for this gig
     */
    public function packages(): HasMany
    {
        return $this->hasMany(GigPackage::class);
    }

    /**
     * Get the reviews for this gig
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(GigReview::class);
    }

    /**
     * Get the saves for this gig
     */
    public function saves(): HasMany
    {
        return $this->hasMany(GigSave::class);
    }

    /**
     * Get the shares for this gig
     */
    public function shares(): HasMany
    {
        return $this->hasMany(GigShare::class);
    }

    /**
     * Get the reactions for this gig
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(GigReaction::class);
    }

    /**
     * Get the subcategory this gig belongs to
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Get the user who owns this gig
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get users who saved this gig
     */
    public function savedBy()
    {
        return $this->belongsToMany(User::class, 'gig_saves')
            ->withTimestamps();
    }

    /**
     * Check if a user has saved this gig
     */
    public function isSavedByUser($userId): bool
    {
        return $this->saves()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's reaction to this gig
     */
    public function getUserReaction($userId): ?string
    {
        $reaction = $this->reactions()->where('user_id', $userId)->first();
        return $reaction ? $reaction->emoji : null;
    }

    /**
     * Get reaction counts grouped by emoji
     */
    public function getReactionCounts(): array
    {
        $reactions = $this->reactions()
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->pluck('count', 'emoji')
            ->toArray();
        
        // Ensure all emojis are present with 0 count if not reacted
        $allEmojis = GigReaction::EMOJIS;
        $result = [];
        foreach ($allEmojis as $emoji) {
            $result[$emoji] = $reactions[$emoji] ?? 0;
        }
        
        return $result;
    }

    /**
     * Get the average rating with reviews
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get reviews with user information
     */
    public function getReviewsWithUser()
    {
        return $this->reviews()
            ->with('user.profile')
            ->recent()
            ->get();
    }

    /**
     * Scope to include review statistics
     */
    public function scopeWithReviewStats($query)
    {
        return $query->withCount('reviews')
            ->withAvg('reviews', 'rating');
    }

    /**
     * Scope to get active gigs only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured gigs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
