<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'service_id',
        'client_id',
        'professional_id',
        'appointment_date',
        'appointment_time',
        'duration_minutes',
        'status',
        'client_name',
        'client_surname',
        'client_email',
        'client_phone',
        'client_date_of_birth',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'is_external',
        'external_color',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'client_date_of_birth' => 'date',
        'cancelled_at' => 'datetime',
        'is_external' => 'boolean',
    ];

    /**
     * Get the service this appointment belongs to
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the client (user) for this appointment
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the professional (user) for this appointment
     */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    /**
     * Get reminders for this appointment
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    /**
     * Scope to get pending appointments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get confirmed appointments
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope to get cancelled appointments
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to get upcoming appointments
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed');
    }

    /**
     * Scope to get external appointments
     */
    public function scopeExternal($query)
    {
        return $query->where('is_external', true);
    }

    /**
     * Scope to get platform appointments (non-external)
     */
    public function scopePlatform($query)
    {
        return $query->where('is_external', false);
    }

    /**
     * Check if appointment can be cancelled (24 hours before)
     */
    public function canBeCancelled(): bool
    {
        $appointmentDateTime = Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
        $hoursUntilAppointment = now()->diffInHours($appointmentDateTime, false);
        
        return $hoursUntilAppointment >= 24;
    }

    /**
     * Get appointment datetime as Carbon instance
     */
    public function getAppointmentDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
    }

    /**
     * Check if reminder should be sent (24 hours before)
     */
    public function shouldSendReminder(): bool
    {
        $appointmentDateTime = $this->appointment_datetime;
        $hoursUntilAppointment = now()->diffInHours($appointmentDateTime, false);
        
        return $hoursUntilAppointment >= 23 && $hoursUntilAppointment <= 25;
    }
}
