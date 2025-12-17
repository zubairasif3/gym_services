<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatRoomParticipant;
use App\Models\User;
use App\Models\Notification;

class ChatSidebar extends Component
{
    use WithFileUploads;
    
    public $isOpen = false;
    public $activeRoomId = null;
    public $message = '';
    public $attachment = null;
    public $rooms = [];
    public $messages = [];
    public $otherUser = null;
    public $showEmojiPicker = false;
    
    protected $listeners = ['open-chat-sidebar' => 'openChat'];
    
    public function mount()
    {
        $this->loadRooms();
    }
    
    #[On('open-chat-sidebar')]
    public function openChat($roomId = null, $userId = null)
    {
        $this->isOpen = true;
        
        if ($userId) {
            // Create or get room with specific user
            $this->getOrCreateRoom($userId);
        } elseif ($roomId) {
            $this->activeRoomId = $roomId;
            $this->loadMessages();
        }
    }
    
    public function closeChat()
    {
        $this->isOpen = false;
        $this->activeRoomId = null;
        $this->reset(['message', 'attachment', 'messages', 'otherUser']);
    }
    
    public function loadRooms()
    {
        $rooms = ChatRoom::where(function($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })
        ->with(['sender', 'receiver'])
        ->latest('updated_at')
        ->get();
        
        $this->rooms = $rooms->map(function($room) {
            $otherUser = $room->sender_id === auth()->id() ? $room->receiver : $room->sender;
            $lastMessage = $room->messages()->latest()->first();
            
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
                ] : null
            ];
        })->toArray();
    }
    
    public function selectRoom($roomId)
    {
        $this->activeRoomId = $roomId;
        $this->loadMessages();
    }
    
    public function loadMessages()
    {
        $room = ChatRoom::findOrFail($this->activeRoomId);
        
        // Get other user info
        $otherUser = $room->sender_id === auth()->id() ? $room->receiver : $room->sender;
        $this->otherUser = [
            'id' => $otherUser->id,
            'name' => $otherUser->name . ' ' . ($otherUser->surname ?? ''),
            'avatar' => $otherUser->avatar_url ? asset('storage/' . $otherUser->avatar_url) : null,
            'initials' => $otherUser->initials
        ];
        
        $this->messages = ChatMessage::where('chat_room_id', $this->activeRoomId)
            ->with('sender:id,name,surname,avatar_url')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'attachment_path' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                    'attachment_type' => $message->attachment_type,
                    'attachment_name' => $message->attachment_original_name,
                    'is_own' => $message->sender_id === auth()->id(),
                    'sender_name' => $message->sender->name,
                    'created_at' => $message->created_at->format('H:i')
                ];
            })
            ->toArray();
        
        // Mark as read
        ChatRoomParticipant::where('chat_room_id', $this->activeRoomId)
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => now()]);
            
        $this->dispatch('message-read');
    }
    
    public function sendMessage()
    {
        $this->validate([
            'message' => 'required_without:attachment|max:2000',
            'attachment' => 'nullable|file|max:10240' // 10MB
        ]);
        
        $room = ChatRoom::findOrFail($this->activeRoomId);
        
        // Handle file attachment
        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;
        
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('chat-attachments', 'public');
            $attachmentName = $this->attachment->getClientOriginalName();
            
            $mimeType = $this->attachment->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $attachmentType = 'image';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $attachmentType = 'video';
            } else {
                $attachmentType = 'document';
            }
        }
        
        // Create message
        $message = ChatMessage::create([
            'chat_room_id' => $this->activeRoomId,
            'sender_id' => auth()->id(),
            'message' => $this->message,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_original_name' => $attachmentName,
            'is_active' => true
        ]);
        
        $room->touch();
        
        // Create notification
        $receiverId = $room->sender_id === auth()->id() ? $room->receiver_id : $room->sender_id;
        Notification::create([
            'user_id' => $receiverId,
            'type' => 'new_message',
            'related_user_id' => auth()->id(),
            'related_model_type' => 'ChatMessage',
            'related_model_id' => $message->id,
            'data' => json_encode([
                'sender_name' => auth()->user()->name,
                'message_preview' => $this->message ? substr($this->message, 0, 50) : 'Sent an attachment'
            ])
        ]);
        
        $this->reset(['message', 'attachment']);
        $this->loadMessages();
        $this->loadRooms();
        $this->dispatch('message-sent');
    }
    
    public function getOrCreateRoom($userId)
    {
        $receiver = User::findOrFail($userId);
        
        $room = ChatRoom::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', auth()->id());
        })->first();
        
        if (!$room) {
            $room = ChatRoom::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $userId,
                'is_active' => true
            ]);
            
            ChatRoomParticipant::create([
                'chat_room_id' => $room->id,
                'user_id' => auth()->id(),
                'last_read_at' => now()
            ]);
            
            ChatRoomParticipant::create([
                'chat_room_id' => $room->id,
                'user_id' => $userId,
                'last_read_at' => now()
            ]);
        }
        
        $this->activeRoomId = $room->id;
        $this->loadRooms();
        $this->loadMessages();
    }
    
    public function addEmoji($emoji)
    {
        $this->message .= $emoji;
        $this->showEmojiPicker = false;
    }
    
    public function toggleEmojiPicker()
    {
        $this->showEmojiPicker = !$this->showEmojiPicker;
    }
    
    public function render()
    {
        return view('livewire.chat-sidebar');
    }
}
