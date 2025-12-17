<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProfileDropdown extends Component
{
    public $user;
    
    public function mount()
    {
        $this->user = auth()->user()->load('profile');
    }
    
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('web.index');
    }
    
    public function render()
    {
        return view('livewire.profile-dropdown');
    }
}
