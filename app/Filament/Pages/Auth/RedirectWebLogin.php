<?php

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;

class RedirectWebLogin extends Login
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl())->send();
        }

        redirect()->route('web.login', ['intended' => url('/admin')])->send();
    }
}
