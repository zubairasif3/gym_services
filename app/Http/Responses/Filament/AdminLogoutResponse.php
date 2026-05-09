<?php

namespace App\Http\Responses\Filament;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class AdminLogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('web.login');
    }
}
