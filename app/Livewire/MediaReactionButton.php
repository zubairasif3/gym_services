<?php

namespace App\Livewire;

use App\Models\ProfileMedia;
use App\Models\ProfileMediaReaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MediaReactionButton extends Component
{
    public ProfileMedia $media;
    public $userReaction = null;
    public $reactionCounts = [];
    
    /**
     * Available emoji reactions (matching gig-show page)
     */
    public const EMOJIS = [
        '💪',
        '💯',
        '🔥',
        '❤️',
        '😍',
        '🔝',
        '👏',
    ];
    
    public function mount(ProfileMedia $media)
    {
        $this->media = $media;
        $this->loadReactions();
    }
    
    public function loadReactions()
    {
        // Get user's current reaction
        if (Auth::check()) {
            $userReactionRecord = $this->media->reactions()
                ->where('user_id', Auth::id())
                ->first();
            $this->userReaction = $userReactionRecord ? $userReactionRecord->emoji : null;
        } else {
            $userReactionRecord = $this->media->reactions()
                ->where('ip_address', request()->ip())
                ->first();
            $this->userReaction = $userReactionRecord ? $userReactionRecord->emoji : null;
        }
        
        // Get all reaction counts
        $this->reactionCounts = $this->media->reactions()
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->pluck('count', 'emoji')
            ->toArray();
    }
    
    public function react($emoji)
    {
        // Find existing reaction
        $query = $this->media->reactions();
        
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_address', request()->ip());
        }
        
        $existingReaction = $query->first();
        
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
            ProfileMediaReaction::create([
                'profile_media_id' => $this->media->id,
                'user_id' => Auth::id(),
                'emoji' => $emoji,
                'ip_address' => request()->ip(),
            ]);
            $this->userReaction = $emoji;
        }
        
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
        return view('livewire.media-reaction-button');
    }
}
