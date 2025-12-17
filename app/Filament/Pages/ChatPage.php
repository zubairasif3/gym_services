<?php

namespace App\Filament\Pages;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Button;
use Illuminate\Support\Facades\Auth;

class ChatPage extends Page
{
    // DISABLED - Chat is now handled via toolbar on frontend
    protected static bool $shouldRegisterNavigation = false;
    
    protected static ?string $navigationLabel = 'Chats';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static string $view = 'filament.pages.chat-page';

    protected static ?string $navigationGroup = 'Conversation';
    protected static ?int $navigationSort = 1;

    protected static ?string $title = '';


    public $chatRoomId;
    public $messages;
    public $newMessage;
    public $chatRooms;

    public function mount()
    {
        $this->chatRoomId = request()->query('chat_room_id'); // Get chat room from query string
        $this->messages = ChatMessage::where('chat_room_id', $this->chatRoomId)->get();
        $this->chatRooms = ChatRoom::where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id())->get();
    }

    // public function render()
    // {
    //     return view('filament.pages.chat-page', [
    //         'chatRooms' => ChatRoom::where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id())->get(),
    //         'messages' => $this->messages,
    //     ]);
    // }

    public function sendMessage()
    {
        // Validate input
        // $this->validate([
        //     'newMessage' => 'required|string|max:255',
        // ]);
        // dd($this->newMessage);
        // Create new message
        ChatMessage::create([
            'chat_room_id' => $this->chatRoomId,
            'sender_id' => Auth::id(),
            'message' => $this->newMessage,
        ]);

        // Clear the message input
        $this->newMessage = '';

        // Update messages
        $this->messages = ChatMessage::where('chat_room_id', $this->chatRoomId)->get();
    }
}
