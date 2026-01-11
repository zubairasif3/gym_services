<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ShareProfileButton extends Component
{
    public User $user;
    public $showDropdown = false;
    
    public function mount(User $user)
    {
        $this->user = $user;
    }
    
    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }
    
    public function share($platform)
    {
        $this->showDropdown = false;
        
        // Generate share URL and open in new window via JavaScript
        $shareUrl = $this->getShareUrl($platform);
        $this->dispatch('open-share-url', ['url' => $shareUrl]);
    }
    
    public function copyLink()
    {
        $this->showDropdown = false;
        
        $profileUrl = route('professional.profile', $this->user->username);
        $this->dispatch('copy-to-clipboard', ['text' => $profileUrl]);
    }
    
    private function getShareUrl($platform)
    {
        $profileUrl = route('professional.profile', $this->user->username);
        $profileTitle = $this->user->name . ' ' . $this->user->surname . ' - Professional Profile';
        
        $urls = [
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($profileUrl),
            'twitter' => "https://twitter.com/intent/tweet?url=" . urlencode($profileUrl) . "&text=" . urlencode($profileTitle),
            'whatsapp' => "https://wa.me/?text=" . urlencode($profileTitle . ' ' . $profileUrl),
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($profileUrl),
        ];
        
        return $urls[$platform] ?? $profileUrl;
    }
    
    public function render()
    {
        return view('livewire.share-profile-button');
    }
}

