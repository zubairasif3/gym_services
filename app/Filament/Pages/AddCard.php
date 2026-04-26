<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class AddCard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.add-card';

    protected static bool $shouldRegisterNavigation = false;

    public function getHeading(): string|Htmlable
    {
        return '';
    }
}
