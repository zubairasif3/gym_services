<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyProfilePreview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.my-profile-preview';

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.preview_edit_profile');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.profile');
    }
    
    public function getTitle(): string | Htmlable
    {
        return __('admin.pages.profile_preview_title');
    }
    
    public function getHeading(): string | Htmlable
    {
        return __('admin.pages.profile_preview_heading');
    }
    
    public function mount(): void
    {
        // No redirect here - handled in the view
    }
}

