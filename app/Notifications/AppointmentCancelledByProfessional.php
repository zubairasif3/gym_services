<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelledByProfessional extends Notification
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Appointment Cancelled - FitScout')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We regret to inform you that your appointment has been cancelled by the professional.')
            ->line('**Service:** ' . $this->appointment->service->title)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('F d, Y'))
            ->line('**Time:** ' . $this->appointment->appointment_time->format('h:i A'))
            ->line('**Professional:** ' . $this->appointment->professional->name . ' ' . $this->appointment->professional->surname);

        if ($this->appointment->cancellation_reason) {
            $mail->line('**Reason:** ' . $this->appointment->cancellation_reason);
        }

        $mail->line('We apologize for any inconvenience. You can book a new appointment at any time.')
            ->action('Book New Appointment', url('/appointments/book/' . $this->appointment->professional->username))
            ->line('Thank you for using FitScout!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'appointment_cancelled_by_professional',
            'appointment_id' => $this->appointment->id,
            'service_title' => $this->appointment->service->title,
            'appointment_date' => $this->appointment->appointment_date->format('Y-m-d'),
            'appointment_time' => $this->appointment->appointment_time->format('H:i'),
            'professional_name' => $this->appointment->professional->name . ' ' . $this->appointment->professional->surname,
            'cancellation_reason' => $this->appointment->cancellation_reason,
            'message' => 'Your appointment for ' . $this->appointment->service->title . ' has been cancelled by the professional.',
        ];
    }
}
