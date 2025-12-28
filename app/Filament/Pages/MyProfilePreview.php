<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyProfilePreview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    
    protected static ?string $navigationLabel = 'Preview/Edit my profile';
    
    protected static ?string $navigationGroup = 'Profile';
    
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.my-profile-preview';
    
    public function getTitle(): string | Htmlable
    {
        return 'Preview My Public Profile';
    }
    
    public function getHeading(): string | Htmlable
    {
        return 'Preview How Your Profile Appears to Customers';
    }
    
    public function mount(): void
    {
        // No redirect here - handled in the view
    }
}

