<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatRoomParticipant;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Display chat interface
     */
    public function index()
    {
        $totalRooms = ChatRoom::where(function($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })->count();
        
        $totalMessages = ChatMessage::whereHas('chatRoom', function($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })->count();
        
        $unreadCount = $this->getUnreadCount();
        
        return view('web.messages', compact('totalRooms', 'totalMessages', 'unreadCount'));
    }
    
    /**
     * Get or create a chat room between two users
     */
    public function getOrCreateRoom(Request $request)
    {
        $receiverId = $request->receiver_id;
        $receiver = User::findOrFail($receiverId);
        
        // Check if chat room already exists
        $room = ChatRoom::where(function($query) use ($receiverId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $receiverId);
        })->orWhere(function($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)
                  ->where('receiver_id', auth()->id());
        })->first();
        
        // Create new room if doesn't exist
        if (!$room) {
            $room = ChatRoom::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'is_active' => true
            ]);
            
            // Create participants
            ChatRoomParticipant::create([
                'chat_room_id' => $room->id,
                'user_id' => auth()->id(),
                'last_read_at' => now()
            ]);
            
            ChatRoomParticipant::create([
                'chat_room_id' => $room->id,
                'user_id' => $receiverId,
                'last_read_at' => now()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'room' => [
                'id' => $room->id,
                'other_user' => [
                    'id' => $receiver->id,
                    'name' => $receiver->name . ' ' . $receiver->surname,
                    'avatar' => $receiver->avatar_url ? asset('storage/' . $receiver->avatar_url) : null,
                    'initials' => $receiver->initials
                ]
            ]
        ]);
    }
    
    /**
     * Get messages for a specific chat room
     */
    public function getMessages($roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        
        // Verify user has access to this room
        if ($room->sender_id !== auth()->id() && $room->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $messages = ChatMessage::where('chat_room_id', $roomId)
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
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name . ' ' . $message->sender->surname,
                        'avatar' => $message->sender->avatar_url ? asset('storage/' . $message->sender->avatar_url) : null,
                    ],
                    'is_own' => $message->sender_id === auth()->id(),
                    'created_at' => $message->created_at->format('H:i'),
                    'created_at_full' => $message->created_at->format('Y-m-d H:i:s')
                ];
            });
        
        // Update last read timestamp
        ChatRoomParticipant::where('chat_room_id', $roomId)
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => now()]);
        
        return response()->json([
            'messages' => $messages
        ]);
    }
    
    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:chat_rooms,id',
            'message' => 'required_without:attachment|max:2000',
            'attachment' => 'nullable|file|max:10240' // 10MB max
        ]);
        
        $room = ChatRoom::findOrFail($request->room_id);
        
        // Verify user has access
        if ($room->sender_id !== auth()->id() && $room->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Handle file attachment
        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            
            // Determine file type
            $mimeType = $file->getMimeType();
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
            'chat_room_id' => $request->room_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_original_name' => $attachmentName,
            'is_active' => true
        ]);
        
        // Update room timestamp
        $room->touch();
        
        // Get receiver ID
        $receiverId = $room->sender_id === auth()->id() ? $room->receiver_id : $room->sender_id;
        
        // Create notification for receiver
        Notification::create([
            'user_id' => $receiverId,
            'type' => 'new_message',
            'related_user_id' => auth()->id(),
            'related_model_type' => 'ChatMessage',
            'related_model_id' => $message->id,
            'data' => json_encode([
                'sender_name' => auth()->user()->name . ' ' . auth()->user()->surname,
                'message_preview' => $request->message ? substr($request->message, 0, 50) : 'Sent an attachment'
            ])
        ]);
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'attachment_path' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                'attachment_type' => $message->attachment_type,
                'attachment_name' => $message->attachment_original_name,
                'sender' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name . ' ' . auth()->user()->surname,
                    'avatar' => auth()->user()->avatar_url ? asset('storage/' . auth()->user()->avatar_url) : null,
                ],
                'is_own' => true,
                'created_at' => $message->created_at->format('H:i')
            ]
        ]);
    }
    
    /**
     * Get user's chat rooms
     */
    private function getUserChatRooms()
    {
        return ChatRoom::where(function($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })
        ->with(['sender', 'receiver'])
        ->withCount(['messages as unread_count' => function($query) {
            $query->where('sender_id', '!=', auth()->id())
                  ->where('created_at', '>', function($subQuery) {
                      $subQuery->select('last_read_at')
                               ->from('chat_room_participants')
                               ->whereColumn('chat_room_id', 'chat_messages.chat_room_id')
                               ->where('user_id', auth()->id())
                               ->limit(1);
                  });
        }])
        ->latest('updated_at')
        ->get()
        ->map(function($room) {
            $otherUser = $room->sender_id === auth()->id() ? $room->receiver : $room->sender;
            $lastMessage = $room->messages()->latest()->first();
            
            return [
                'id' => $room->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name . ' ' . $otherUser->surname,
                    'avatar' => $otherUser->avatar_url ? asset('storage/' . $otherUser->avatar_url) : null,
                    'initials' => $otherUser->initials
                ],
                'last_message' => $lastMessage ? [
                    'text' => $lastMessage->message ?: 'Attachment',
                    'time' => $lastMessage->created_at->diffForHumans()
                ] : null,
                'unread_count' => $room->unread_count ?? 0
            ];
        });
    }
    
    /**
     * API: Get unread message count
     */
    public function getUnreadCount()
    {
        $count = ChatRoom::where(function($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
        })
        ->whereHas('messages', function($query) {
            $query->where('sender_id', '!=', auth()->id())
                  ->where('created_at', '>', function($subQuery) {
                      $subQuery->select('last_read_at')
                               ->from('chat_room_participants')
                               ->whereColumn('chat_room_id', 'chat_messages.chat_room_id')
                               ->where('user_id', auth()->id())
                               ->limit(1);
                  });
        })
        ->count();
        
        return response()->json(['count' => $count]);
    }
}
