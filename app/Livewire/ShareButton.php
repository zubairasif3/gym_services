<?php

namespace App\Livewire;

use App\Models\Gig;
use App\Models\GigShare;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ShareButton extends Component
{
    public Gig $gig;
    public $showDropdown = false;
    
    public function mount(Gig $gig)
    {
        $this->gig = $gig;
    }
    
    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }
    
    public function share($platform)
    {
        // Track the share
        GigShare::create([
            'gig_id' => $this->gig->id,
            'user_id' => Auth::id(),
            'platform' => $platform,
            'ip_address' => request()->ip(),
        ]);
        
        $this->showDropdown = false;
        
        // Generate share URL and open in new window via JavaScript
        $shareUrl = $this->getShareUrl($platform);
        $this->dispatch('open-share-url', ['url' => $shareUrl]);
    }
    
    public function copyLink()
    {
        // Track as 'link' share
        GigShare::create([
            'gig_id' => $this->gig->id,
            'user_id' => Auth::id(),
            'platform' => 'link',
            'ip_address' => request()->ip(),
        ]);
        
        $this->showDropdown = false;
        
        $gigUrl = route('gigs.show', $this->gig->slug);
        $this->dispatch('copy-to-clipboard', ['text' => $gigUrl]);
    }
    
    private function getShareUrl($platform)
    {
        $gigUrl = route('gigs.show', $this->gig->slug);
        $gigTitle = $this->gig->title;
        
        $urls = [
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($gigUrl),
            'twitter' => "https://twitter.com/intent/tweet?url=" . urlencode($gigUrl) . "&text=" . urlencode($gigTitle),
            'whatsapp' => "https://wa.me/?text=" . urlencode($gigTitle . ' ' . $gigUrl),
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($gigUrl),
        ];
        
        return $urls[$platform] ?? $gigUrl;
    }
    
    public function render()
    {
        return view('livewire.share-button');
    }
}
