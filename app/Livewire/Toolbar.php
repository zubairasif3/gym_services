<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Toolbar extends Component
{
    public $notificationsCount = 0;
    public $messagesCount = 0;
    public $followingCount = 0;
    public $showNotifications = false;
    public $showMessages = false;
    public $showProfile = false;
    
    public function mount()
    {
        $this->loadCounts();
    }
    
    #[On('notification-created')]
    #[On('notification-read')]
    #[On('message-received')]
    #[On('message-read')]
    #[On('follower-added')]
    #[On('follower-removed')]
    public function loadCounts()
    {
        $user = auth()->user();
        
        // Load unread notifications count
        $this->notificationsCount = $user->unreadNotifications()->count();
        
        // Load unread messages count
        $this->messagesCount = $this->getUnreadMessagesCount();
        
        // Load following count
        $this->followingCount = $user->following_count;
    }
    
    private function getUnreadMessagesCount()
    {
        return \App\Models\ChatRoom::where(function($query) {
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
    }
    
    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
        $this->showMessages = false;
        $this->showProfile = false;
    }
    
    public function toggleMessages()
    {
        $this->showMessages = !$this->showMessages;
        $this->showNotifications = false;
        $this->showProfile = false;
    }
    
    public function toggleProfile()
    {
        $this->showProfile = !$this->showProfile;
        $this->showNotifications = false;
        $this->showMessages = false;
    }
    
    public function openChat()
    {
        $this->dispatch('open-chat-sidebar');
    }
    
    public function render()
    {
        return view('livewire.toolbar');
    }
}
