<?php

namespace App\Services;

use App\Models\ClientCancellationTracking;
use Carbon\Carbon;

class CancellationTrackingService
{
    /**
     * Track a cancellation for a client
     */
    public function trackCancellation(int $clientId): void
    {
        $tracking = ClientCancellationTracking::getOrCreateForCurrentMonth($clientId);
        $tracking->incrementCancellation();
    }

    /**
     * Check if client can make new bookings
     */
    public function canClientBook(int $clientId): bool
    {
        return ClientCancellationTracking::canClientBook($clientId);
    }

    /**
     * Get cancellation count for current month
     */
    public function getCurrentMonthCancellationCount(int $clientId): int
    {
        $tracking = ClientCancellationTracking::getOrCreateForCurrentMonth($clientId);
        return $tracking->cancellation_count;
    }

    /**
     * Reset monthly cancellation counts (should be called by scheduled job)
     */
    public function resetMonthlyCounts(): void
    {
        // This would typically be called at the start of each month
        // For now, we check on-demand in the model
    }

    /**
     * Unblock clients whose block period has expired
     */
    public function unblockExpiredClients(): void
    {
        $expiredBlocks = ClientCancellationTracking::where('is_blocked', true)
            ->whereNotNull('blocked_until')
            ->where('blocked_until', '<=', now())
            ->get();

        foreach ($expiredBlocks as $tracking) {
            $tracking->unblock();
        }
    }
}
