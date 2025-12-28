<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GigReaction extends Model
{
    protected $fillable = [
        'user_id',
        'gig_id',
        'emoji',
    ];

    /**
     * Available emoji reactions
     */
    public const EMOJIS = [
        '💪',
        '💯',
        '🔥',
        '❤️',
        '😍',
        '🔝',
        '👏',
    ];

    /**
     * Get the user who reacted
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the gig that was reacted to
     */
    public function gig(): BelongsTo
    {
        return $this->belongsTo(Gig::class);
    }
}

