<?php

namespace App\Livewire;

use App\Models\Gig;
use App\Models\GigReaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ReactionButton extends Component
{
    public Gig $gig;
    public $userReaction = null;
    public $reactionCounts = [];
    public $showEmojiPicker = false;
    
    public function mount(Gig $gig)
    {
        $this->gig = $gig;
        $this->loadReactions();
    }
    
    public function loadReactions()
    {
        // Get user's current reaction
        if (Auth::check()) {
            $this->userReaction = $this->gig->getUserReaction(Auth::id());
        }
        
        // Get all reaction counts
        $this->reactionCounts = $this->gig->getReactionCounts();
    }
    
    public function toggleEmojiPicker()
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }
        
        $this->showEmojiPicker = !$this->showEmojiPicker;
    }
    
    public function react($emoji)
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }
        
        // Prevent users from reacting to their own services
        if (Auth::id() === $this->gig->user_id) {
            session()->flash('error', 'You cannot react to your own service.');
            return;
        }
        
        // Find existing reaction
        $existingReaction = $this->gig->reactions()
            ->where('user_id', Auth::id())
            ->first();
        
        if ($existingReaction) {
            if ($existingReaction->emoji === $emoji) {
                // If clicking the same emoji, remove the reaction
                $existingReaction->delete();
                $this->userReaction = null;
            } else {
                // Update to new emoji
                $existingReaction->update(['emoji' => $emoji]);
                $this->userReaction = $emoji;
            }
        } else {
            // Create new reaction
            $this->gig->reactions()->create([
                'user_id' => Auth::id(),
                'emoji' => $emoji,
            ]);
            $this->userReaction = $emoji;
        }
        
        $this->showEmojiPicker = false;
        $this->loadReactions();
        
        // Dispatch browser event for animation
        $this->dispatch('reaction-updated');
    }
    
    public function getTotalReactionsProperty()
    {
        return array_sum($this->reactionCounts);
    }
    
    public function render()
    {
        return view('livewire.reaction-button');
    }
}

