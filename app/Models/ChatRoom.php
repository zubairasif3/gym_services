<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{

    // Define the fillable properties (columns that can be mass-assigned)
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'is_active',
    ];

    /**
     * Get the sender of the chat room.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the chat room.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the messages for the chat room.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Scope a query to only include chat rooms between the given users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $senderId
     * @param int $receiverId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetween($query, $senderId, $receiverId)
    {
        return $query->where(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($senderId, $receiverId) {
            $q->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        });
    }
}
