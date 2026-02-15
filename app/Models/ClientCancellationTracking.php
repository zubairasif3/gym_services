<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ClientCancellationTracking extends Model
{
    protected $table = 'client_cancellation_tracking';
    protected $fillable = [
        'client_id',
        'month',
        'year',
        'cancellation_count',
        'is_blocked',
        'blocked_until',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'cancellation_count' => 'integer',
        'is_blocked' => 'boolean',
        'blocked_until' => 'date',
    ];

    /**
     * Get the client (user) for this tracking
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get or create tracking for current month
     */
    public static function getOrCreateForCurrentMonth($clientId): self
    {
        $now = now();
        return self::firstOrCreate(
            [
                'client_id' => $clientId,
                'month' => $now->month,
                'year' => $now->year,
            ],
            [
                'cancellation_count' => 0,
                'is_blocked' => false,
            ]
        );
    }

    /**
     * Increment cancellation count
     */
    public function incrementCancellation(): void
    {
        $this->increment('cancellation_count');
        
        // Block if exceeds limit
        if ($this->cancellation_count >= 3) {
            $this->block();
        }
    }

    /**
     * Block client from making new bookings
     */
    public function block(): void
    {
        $this->update([
            'is_blocked' => true,
            'blocked_until' => now()->addMonth(),
        ]);
    }

    /**
     * Unblock client
     */
    public function unblock(): void
    {
        $this->update([
            'is_blocked' => false,
            'blocked_until' => null,
        ]);
    }

    /**
     * Check if client is currently blocked
     */
    public function isCurrentlyBlocked(): bool
    {
        if (!$this->is_blocked) {
            return false;
        }

        // Check if block period has expired
        if ($this->blocked_until && now()->greaterThan($this->blocked_until)) {
            $this->unblock();
            return false;
        }

        return true;
    }

    /**
     * Check if client can make new bookings
     */
    public static function canClientBook($clientId): bool
    {
        $tracking = self::getOrCreateForCurrentMonth($clientId);
        return !$tracking->isCurrentlyBlocked();
    }
}
