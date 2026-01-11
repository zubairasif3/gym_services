<?php

namespace App\Livewire;

use App\Models\ProfileMedia;
use App\Models\ProfileMediaReaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProfileMediaReactions extends Component
{
    public $mediaId;
    public $reactions = [];
    public $userReactions = [];
    
    /**
     * Available emoji reactions
     */
    public const EMOJIS = [
        '🔝',
        '💪',
        '💯',
        '👏',
        '🔥',
        '❤️',
        '😍',
        '😱',
    ];
    
    protected $listeners = ['react-to-media' => 'reactToMedia'];
    
    public function mount($mediaId)
    {
        $this->mediaId = $mediaId;
        $this->loadReactions();
    }
    
    public function loadReactions()
    {
        $media = ProfileMedia::find($this->mediaId);
        
        if (!$media) {
            return;
        }
        
        // Get reaction counts for each emoji using binary comparison
        foreach (self::EMOJIS as $emoji) {
            $this->reactions[$emoji] = $media->reactions()
                ->whereRaw('BINARY emoji = ?', [$emoji])
                ->count();
        }
        
        // Get user's reactions
        if (Auth::check()) {
            $userReaction = $media->reactions()
                ->where('user_id', Auth::id())
                ->first();
            
            if ($userReaction) {
                $this->userReactions[$userReaction->emoji] = true;
            }
        }
    }
    
    public function reactToMedia($mediaId, $emoji)
    {
        // Require authentication
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to react.');
            return redirect()->route('web.login');
        }
        
        $media = ProfileMedia::find($mediaId);
        
        if (!$media) {
            return;
        }
        
        // Find existing reaction from current user
        $existingReaction = $media->reactions()
            ->where('user_id', Auth::id())
            ->first();
        
        if ($existingReaction) {
            if ($existingReaction->emoji === $emoji) {
                // Remove reaction if clicking the same emoji
                $existingReaction->delete();
                unset($this->userReactions[$emoji]);
            } else {
                // Update to new emoji
                $existingReaction->update(['emoji' => $emoji]);
                $this->userReactions = [$emoji => true];
            }
        } else {
            // Create new reaction
            ProfileMediaReaction::create([
                'profile_media_id' => $media->id,
                'user_id' => Auth::id(),
                'emoji' => $emoji,
                'ip_address' => request()->ip(),
            ]);
            $this->userReactions[$emoji] = true;
        }
        
        $this->loadReactions();
    }
    
    public function render()
    {
        return view('livewire.profile-media-reactions');
    }
}
