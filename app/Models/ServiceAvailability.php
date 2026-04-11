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
        'slot_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'availability_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'slot_duration_minutes' => 'integer',
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

    /**
     * Check if a slot can be created (no overlap; allow 1×60 min OR 2×30 min per hour, not mixed).
     */
    public static function canCreateSlot(int $serviceId, string $dateStr, string $startTime, string $endTime): bool
    {
        /* Overlap: ranges overlap only if newStart < existingEnd AND newEnd > existingStart (touching at boundary = no overlap) */
        $overlapping = static::where('service_id', $serviceId)
            ->where('availability_date', $dateStr)
            ->where('is_active', true)
            ->whereRaw('start_time < ? AND end_time > ?', [$endTime, $startTime])
            ->exists();

        if ($overlapping) {
            return false;
        }

        $startM = (int) substr($startTime, 0, 2) * 60 + (int) substr($startTime, 3, 2);
        $endM = (int) substr($endTime, 0, 2) * 60 + (int) substr($endTime, 3, 2);
        $durationM = $endM - $startM;
        $startHour = (int) substr($startTime, 0, 2);
        $hourStart = sprintf('%02d:00', $startHour);
        $hourEnd = $startHour < 23 ? sprintf('%02d:00', $startHour + 1) : '23:59';

        $slotsInHour = static::where('service_id', $serviceId)
            ->where('availability_date', $dateStr)
            ->where('is_active', true)
            ->where('start_time', '<', $hourEnd)
            ->where('end_time', '>', $hourStart)
            ->get();

        $has60InHour = $slotsInHour->contains(function ($s) {
            $sm = (int) $s->start_time->format('H') * 60 + (int) $s->start_time->format('i');
            $em = (int) $s->end_time->format('H') * 60 + (int) $s->end_time->format('i');
            return ($em - $sm) >= 60;
        });

        if ($durationM >= 60) {
            return $slotsInHour->isEmpty();
        }
        if ($has60InHour) {
            return false;
        }
        return $slotsInHour->count() < 2;
    }
}
