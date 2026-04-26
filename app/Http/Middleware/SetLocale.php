<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = ['en', 'it'];
        $locale = session('locale', 'en');

        if (! in_array($locale, $allowedLocales, true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}

