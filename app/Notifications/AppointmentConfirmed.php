<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmed extends Notification
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
        return (new MailMessage)
            ->subject('Appointment Confirmed - FitScout')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your appointment has been confirmed.')
            ->line('**Service:** ' . $this->appointment->service->title)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('F d, Y'))
            ->line('**Time:** ' . $this->appointment->appointment_time->format('h:i A'))
            ->line('**Professional:** ' . $this->appointment->professional->name . ' ' . $this->appointment->professional->surname)
            ->line('Please make sure to arrive on time. You will receive a reminder 24 hours before your appointment.')
            ->action('View Appointment', route('appointments.index'))
            ->line('Thank you for using FitScout!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'appointment_confirmed',
            'appointment_id' => $this->appointment->id,
            'service_title' => $this->appointment->service->title,
            'appointment_date' => $this->appointment->appointment_date->format('Y-m-d'),
            'appointment_time' => $this->appointment->appointment_time->format('H:i'),
            'professional_name' => $this->appointment->professional->name . ' ' . $this->appointment->professional->surname,
            'message' => 'Your appointment for ' . $this->appointment->service->title . ' has been confirmed.',
        ];
    }
}
