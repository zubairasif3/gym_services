<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AddCard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.add-card';

    protected static bool $shouldRegisterNavigation = false;
}
