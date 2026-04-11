<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification
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
            ->subject('Appointment Reminder - FitScout')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder that you have an upcoming appointment.')
            ->line('**Service:** ' . $this->appointment->service->title)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('F d, Y'))
            ->line('**Time:** ' . $this->appointment->appointment_time->format('h:i A'))
            ->line('**Professional:** ' . $this->appointment->professional->name . ' ' . $this->appointment->professional->surname)
            ->line('Please make sure to arrive on time. If you need to cancel, please do so at least 24 hours in advance.')
            ->action('View Appointment', route('appointments.index'))
            ->line('Thank you for using FitScout!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'appointment_reminder',
            'appointment_id' => $this->appointment->id,
            'service_title' => $this->appointment->service->title,
            'appointment_date' => $this->appointment->appointment_date->format('Y-m-d'),
            'appointment_time' => $this->appointment->appointment_time->format('H:i'),
            'professional_name' => $this->appointment->professional->name . ' ' . $this->appointment->professional->surname,
            'message' => 'Reminder: You have an appointment for ' . $this->appointment->service->title . ' tomorrow.',
        ];
    }
}
