<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatRoom;

class MessagesDropdown extends Component
{
    public $recentChats = [];
    public $unreadCount = 0;
    
    public function mount()
    {
        $this->loadRecentChats();
    }
    
    public function loadRecentChats()
    {
        $rooms = ChatRoom::where(function($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })
        ->with(['sender', 'receiver'])
        ->latest('updated_at')
        ->take(5)
        ->get();
        
        $this->recentChats = $rooms->map(function($room) {
            $otherUser = $room->sender_id === auth()->id() ? $room->receiver : $room->sender;
            $lastMessage = $room->messages()->latest()->first();
            
            $unreadCount = $room->messages()
                ->where('sender_id', '!=', auth()->id())
                ->where('created_at', '>', function($query) use ($room) {
                    $query->select('last_read_at')
                          ->from('chat_room_participants')
                          ->where('chat_room_id', $room->id)
                          ->where('user_id', auth()->id())
                          ->limit(1);
                })
                ->count();
            
            return [
                'id' => $room->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name . ' ' . ($otherUser->surname ?? ''),
                    'avatar' => $otherUser->avatar_url ? asset('storage/' . $otherUser->avatar_url) : null,
                    'initials' => $otherUser->initials
                ],
                'last_message' => $lastMessage ? [
                    'text' => $lastMessage->message ?: '📎 Attachment',
                    'time' => $lastMessage->created_at->diffForHumans()
                ] : null,
                'unread_count' => $unreadCount
            ];
        })->toArray();
        
        $this->unreadCount = collect($this->recentChats)->where('unread_count', '>', 0)->count();
    }
    
    public function openChat($roomId)
    {
        $this->dispatch('open-chat-sidebar', roomId: $roomId);
    }
    
    public function render()
    {
        return view('livewire.messages-dropdown');
    }
}
