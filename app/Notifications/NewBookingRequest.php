<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingRequest extends Notification
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
            ->subject('New Booking Request - FitScout')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new appointment booking request.')
            ->line('**Service:** ' . $this->appointment->service->title)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('F d, Y'))
            ->line('**Time:** ' . $this->appointment->appointment_time->format('h:i A'))
            ->line('**Client:** ' . $this->appointment->client_name . ' ' . $this->appointment->client_surname)
            ->line('**Client Email:** ' . $this->appointment->client_email)
            ->line('Please review and confirm or cancel this appointment request.')
            ->action('View Request', url('/admin/appointments/' . $this->appointment->id))
            ->line('Thank you for using FitScout!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_booking_request',
            'appointment_id' => $this->appointment->id,
            'service_title' => $this->appointment->service->title,
            'appointment_date' => $this->appointment->appointment_date->format('Y-m-d'),
            'appointment_time' => $this->appointment->appointment_time->format('H:i'),
            'client_name' => $this->appointment->client_name . ' ' . $this->appointment->client_surname,
            'message' => 'New booking request for ' . $this->appointment->service->title . ' from ' . $this->appointment->client_name,
        ];
    }
}
