<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Notification;

class FollowButton extends Component
{
    public $user;
    public $isFollowing = false;
    public $followersCount = 0;
    
    public function mount(User $user)
    {
        $this->user = $user;
        $this->isFollowing = auth()->check() ? auth()->user()->isFollowing($user) : false;
        $this->followersCount = $user->followers_count;
    }
    
    public function toggleFollow()
    {
        if (!auth()->check()) {
            return redirect()->route('web.login');
        }
        
        if ($this->isFollowing) {
            $this->unfollow();
        } else {
            $this->follow();
        }
    }
    
    private function follow()
    {
        auth()->user()->following()->attach($this->user->id);
        $this->user->increment('followers_count');
        auth()->user()->increment('following_count');
        
        // Create notification
        Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_follower',
            'related_user_id' => auth()->id(),
            'data' => json_encode([
                'follower_name' => auth()->user()->name . ' ' . auth()->user()->surname,
                'message' => 'started following you'
            ])
        ]);
        
        $this->isFollowing = true;
        $this->followersCount = $this->user->fresh()->followers_count;
        $this->dispatch('follower-added');
    }
    
    private function unfollow()
    {
        auth()->user()->following()->detach($this->user->id);
        $this->user->decrement('followers_count');
        auth()->user()->decrement('following_count');
        
        $this->isFollowing = false;
        $this->followersCount = $this->user->fresh()->followers_count;
        $this->dispatch('follower-removed');
    }
    
    public function render()
    {
        return view('livewire.follow-button');
    }
}
