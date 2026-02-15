<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Send appointment confirmation message with buttons
     */
    public function sendAppointmentConfirmationMessage(Appointment $appointment): void
    {
        $professional = $appointment->professional;
        $client = $appointment->client;

        // Get or create chat room
        $chatRoom = $this->getOrCreateChatRoom($professional->id, $client->id);

        // Create message with button data
        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => $professional->id,
            'message' => "Your appointment for {$appointment->service->title} on {$appointment->appointment_date->format('F d, Y')} at {$appointment->appointment_time->format('h:i A')} has been confirmed.",
            'is_active' => true,
            'button_data' => [
                'type' => 'appointment_confirmation',
                'appointment_id' => $appointment->id,
                'buttons' => [
                    [
                        'label' => 'Confirm',
                        'action' => 'appointment_confirm',
                        'style' => 'primary',
                    ],
                    [
                        'label' => 'Cancel',
                        'action' => 'appointment_cancel',
                        'style' => 'danger',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Send appointment cancellation message
     */
    public function sendAppointmentCancellationMessage(Appointment $appointment): void
    {
        $professional = $appointment->professional;
        $client = $appointment->client;

        // Get or create chat room
        $chatRoom = $this->getOrCreateChatRoom($professional->id, $client->id);

        $reason = $appointment->cancellation_reason 
            ? " Reason: {$appointment->cancellation_reason}" 
            : '';

        // Create message
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => $professional->id,
            'message' => "Your appointment for {$appointment->service->title} on {$appointment->appointment_date->format('F d, Y')} at {$appointment->appointment_time->format('h:i A')} has been cancelled.{$reason}",
            'is_active' => true,
        ]);
    }

    /**
     * Get or create a chat room between two users
     */
    protected function getOrCreateChatRoom(int $userId1, int $userId2): ChatRoom
    {
        $room = ChatRoom::where(function($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)
                  ->where('receiver_id', $userId2);
        })->orWhere(function($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)
                  ->where('receiver_id', $userId1);
        })->first();

        if (!$room) {
            $room = ChatRoom::create([
                'sender_id' => $userId1,
                'receiver_id' => $userId2,
                'is_active' => true,
            ]);
        }

        return $room;
    }

    /**
     * Handle button action from chat message
     */
    public function handleButtonAction(int $messageId, string $action, int $userId): array
    {
        $message = ChatMessage::findOrFail($messageId);

        if (!$message->button_data || !isset($message->button_data['appointment_id'])) {
            return ['success' => false, 'error' => 'Invalid message action.'];
        }

        $appointmentId = $message->button_data['appointment_id'];
        $appointment = Appointment::findOrFail($appointmentId);

        // Verify user has permission
        if ($appointment->client_id !== $userId) {
            return ['success' => false, 'error' => 'Unauthorized.'];
        }

        switch ($action) {
            case 'appointment_confirm':
                // Appointment is already confirmed, this is just acknowledgment
                return ['success' => true, 'message' => 'Appointment confirmed.'];
                
            case 'appointment_cancel':
                // Check if can be cancelled
                if (!$appointment->canBeCancelled()) {
                    return [
                        'success' => false, 
                        'error' => 'Appointments can only be cancelled at least 24 hours in advance.'
                    ];
                }

                // This will be handled by the AppointmentController
                return [
                    'success' => true, 
                    'message' => 'Please provide a cancellation reason.',
                    'requires_reason' => true,
                    'appointment_id' => $appointmentId,
                ];
        }

        return ['success' => false, 'error' => 'Unknown action.'];
    }
}
