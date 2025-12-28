<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Log;

class FollowButton extends Component
{
    public $user;
    public $isFollowing = false;
    public $followersCount = 0;
    public $isLoading = false;
    
    public function mount(User $user)
    {
        $this->user = $user;
        $this->loadFollowState();
    }
    
    public function loadFollowState()
    {
        $this->isFollowing = auth()->check() ? auth()->user()->isFollowing($this->user) : false;
        $this->followersCount = $this->user->followers_count;
        $this->isLoading = false;
    }
    
    public function toggleFollow()
    {
        if (!auth()->check()) {
            return redirect()->route('web.login');
        }
        
        // Prevent users from following themselves
        if (auth()->id() === $this->user->id) {
            session()->flash('error', 'You cannot follow yourself.');
            return;
        }
        
        $this->isLoading = true;
        
        try {
            if ($this->isFollowing) {
                $this->unfollow();
            } else {
                $this->follow();
            }
        } catch (Exception $e) {
            Log::error('Follow toggle error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred. Please try again.');
        } finally {
            $this->isLoading = false;
        }
    }
    
    private function follow()
    {
        // Prevent users from following themselves
        if (auth()->id() === $this->user->id) {
            session()->flash('error', 'You cannot follow yourself.');
            $this->loadFollowState();
            return;
        }
        
        // Check if already following to prevent duplicates
        if (auth()->user()->isFollowing($this->user)) {
            $this->loadFollowState();
            return;
        }
        
        auth()->user()->following()->attach($this->user->id);
        $this->user->increment('followers_count');
        auth()->user()->increment('following_count');
        
        // Create notification
        try {
            Notification::create([
                'user_id' => $this->user->id,
                'type' => 'new_follower',
                'related_user_id' => auth()->id(),
                'data' => json_encode([
                    'follower_name' => auth()->user()->name . ' ' . (auth()->user()->surname ?? ''),
                    'message' => 'started following you'
                ])
            ]);
        } catch (Exception $e) {
            Log::error('Notification creation error: ' . $e->getMessage());
        }
        
        $this->isFollowing = true;
        $this->followersCount = $this->user->fresh()->followers_count;
        $this->dispatch('follower-added');
    }
    
    private function unfollow()
    {
        // Check if not following to prevent errors
        if (!auth()->user()->isFollowing($this->user)) {
            $this->loadFollowState();
            return;
        }
        
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
