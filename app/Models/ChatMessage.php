<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{

    // Define the fillable properties (columns that can be mass-assigned)
    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'message',
        'read',
        'attachment_path',
        'attachment_type',
        'attachment_original_name',
        'is_active',
    ];

    /**
     * Get the chat room that the message belongs to.
     */
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Scope a query to only include unread messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }
}
