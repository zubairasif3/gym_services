<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;

class NotificationsDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    
    public function mount()
    {
        $this->loadNotifications();
    }
    
    public function loadNotifications()
    {
        $this->notifications = auth()->user()
            ->notifications()
            ->where('type', '!=', 'new_message') // Exclude message notifications
            ->with('relatedUser:id,name,surname,avatar_url')
            ->take(10)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'is_read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'related_user' => $notification->relatedUser ? [
                        'name' => $notification->relatedUser->name . ' ' . $notification->relatedUser->surname,
                        'avatar' => $notification->relatedUser->avatar_url 
                            ? asset('storage/' . $notification->relatedUser->avatar_url) 
                            : null,
                        'initials' => $notification->relatedUser->initials
                    ] : null
                ];
            });
            
        $this->unreadCount = auth()->user()->unreadNotifications()->where('type', '!=', 'new_message')->count();
    }
    
    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
            $this->dispatch('notification-read');
        }
    }
    
    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        $this->loadNotifications();
        $this->dispatch('notification-read');
    }
    
    public function render()
    {
        return view('livewire.notifications-dropdown');
    }
}
