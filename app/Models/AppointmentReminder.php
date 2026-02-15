<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentReminder extends Model
{
    protected $fillable = [
        'appointment_id',
        'reminder_type',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the appointment this reminder belongs to
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Check if reminder was already sent
     */
    public static function wasSent($appointmentId, $reminderType): bool
    {
        return self::where('appointment_id', $appointmentId)
            ->where('reminder_type', $reminderType)
            ->exists();
    }
}
