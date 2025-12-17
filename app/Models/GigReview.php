<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GigReview extends Model
{
    protected $fillable = [
        'gig_id',
        'user_id',
        'order_id',
        'rating',
        'comment',
        'is_verified',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'helpful_count' => 'integer',
    ];

    /**
     * Get the gig that this review belongs to
     */
    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }

    /**
     * Get the user who wrote this review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this review (disabled until Order model exists)
     * Uncomment when Order model is created
     */
    // public function order(): BelongsTo
    // {
    //     return $this->belongsTo(Order::class);
    // }

    /**
     * Scope to get only verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter by rating
     */
    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to get reviews ordered by most helpful
     */
    public function scopeMostHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    /**
     * Scope to get recent reviews
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
