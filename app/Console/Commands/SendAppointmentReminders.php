<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use App\Notifications\AppointmentReminder as AppointmentReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send appointment reminders 24 hours before scheduled appointments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for appointments that need reminders...');

        // Get appointments that are 24 hours away (within 23-25 hour window)
        $now = now();
        $targetStart = $now->copy()->addHours(23);
        $targetEnd = $now->copy()->addHours(25);

        $appointments = Appointment::confirmed()
            ->whereBetween('appointment_date', [
                $targetStart->toDateString(),
                $targetEnd->toDateString()
            ])
            ->get()
            ->filter(function ($appointment) {
                return $appointment->shouldSendReminder();
            });

        $sentCount = 0;

        foreach ($appointments as $appointment) {
            // Check if reminder was already sent
            $emailSent = AppointmentReminder::wasSent($appointment->id, 'email');
            $notificationSent = AppointmentReminder::wasSent($appointment->id, 'notification');

            // Send email reminder if not sent
            if (!$emailSent) {
                try {
                    $appointment->client->notify(new AppointmentReminderNotification($appointment));
                    AppointmentReminder::create([
                        'appointment_id' => $appointment->id,
                        'reminder_type' => 'email',
                        'sent_at' => now(),
                    ]);
                    $this->info("Email reminder sent for appointment ID: {$appointment->id}");
                } catch (\Exception $e) {
                    $this->error("Failed to send email reminder for appointment ID: {$appointment->id} - " . $e->getMessage());
                }
            }

            // Send in-app notification if not sent
            if (!$notificationSent) {
                try {
                    $appointment->client->notify(new AppointmentReminderNotification($appointment));
                    AppointmentReminder::create([
                        'appointment_id' => $appointment->id,
                        'reminder_type' => 'notification',
                        'sent_at' => now(),
                    ]);
                    $this->info("In-app notification sent for appointment ID: {$appointment->id}");
                } catch (\Exception $e) {
                    $this->error("Failed to send notification for appointment ID: {$appointment->id} - " . $e->getMessage());
                }
            }

            $sentCount++;
        }

        $this->info("Processed {$sentCount} appointment(s).");
        return Command::SUCCESS;
    }
}
