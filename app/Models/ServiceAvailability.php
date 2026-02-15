<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAvailability extends Model
{
    protected $fillable = [
        'service_id',
        'availability_date',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'availability_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Get the service this availability belongs to
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope to get active availabilities only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get availabilities for a specific date
     */
    public function scopeForDate($query, $date)
    {
        $dateStr = $date instanceof \Carbon\Carbon
            ? $date->toDateString()
            : \Carbon\Carbon::parse($date)->toDateString();
        return $query->where('availability_date', $dateStr);
    }

    /**
     * Scope to get availabilities within a date range
     */
    public function scopeInRange($query, $start, $end)
    {
        $startStr = $start instanceof \Carbon\Carbon ? $start->toDateString() : \Carbon\Carbon::parse($start)->toDateString();
        $endStr = $end instanceof \Carbon\Carbon ? $end->toDateString() : \Carbon\Carbon::parse($end)->toDateString();
        return $query->whereBetween('availability_date', [$startStr, $endStr]);
    }

    /**
     * Formatted date for display
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->availability_date->format('l, M j, Y');
    }
}
