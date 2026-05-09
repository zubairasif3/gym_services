<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Http\Request;

class RedirectAdminGuestsToWebLogin extends FilamentAuthenticate
{
    protected function redirectTo($request): ?string
    {
        if (! $request instanceof Request) {
            return route('web.login');
        }

        return route('web.login', ['intended' => $this->safeAdminIntendedUrl($request)]);
    }

    private function safeAdminIntendedUrl(Request $request): string
    {
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $candidates = array_filter([
            $request->fullUrl(),
            $request->headers->get('Referer'),
        ]);

        foreach ($candidates as $candidate) {
            if (! is_string($candidate) || $candidate === '') {
                continue;
            }
            $path = parse_url($candidate, PHP_URL_PATH) ?: '';
            if (! str_starts_with($path, '/admin')) {
                continue;
            }
            if ($appHost && (parse_url($candidate, PHP_URL_HOST) ?: '') !== $appHost) {
                continue;
            }
            if (str_contains($path, '..')) {
                continue;
            }

            return $candidate;
        }

        return url('/admin');
    }
}
