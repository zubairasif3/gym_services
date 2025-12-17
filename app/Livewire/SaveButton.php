<?php

namespace App\Livewire;

use App\Models\Gig;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SaveButton extends Component
{
    public Gig $gig;
    public $isSaved = false;
    
    public function mount(Gig $gig)
    {
        $this->gig = $gig;
        $this->checkSavedStatus();
    }
    
    public function checkSavedStatus()
    {
        if (Auth::check()) {
            $this->isSaved = $this->gig->isSavedByUser(Auth::id());
        }
    }
    
    public function toggleSave()
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }
        
        if ($this->isSaved) {
            // Unsave
            $this->gig->saves()->where('user_id', Auth::id())->delete();
            $this->isSaved = false;
        } else {
            // Save
            $this->gig->saves()->create([
                'user_id' => Auth::id(),
            ]);
            $this->isSaved = true;
        }
    }
    
    public function render()
    {
        return view('livewire.save-button');
    }
}
