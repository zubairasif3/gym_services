<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->navigationGroups([
                NavigationGroup::make(__('admin.navigation.dashboard')),
                NavigationGroup::make(__('admin.navigation.categories')),
                NavigationGroup::make(__('admin.navigation.marketplace')),
                NavigationGroup::make(__('admin.navigation.conversation')),
                NavigationGroup::make(__('admin.navigation.users')),
                NavigationGroup::make(__('admin.navigation.setting')),
            ])
            ->navigationItems([
                NavigationItem::make('visit-website')
                    ->label(fn (): string => __('admin.navigation.visit_website'))
                // ->url('/', shouldOpenInNewTab: true)
                    ->url('/')
                    ->icon('heroicon-o-globe-alt')
                    ->sort(999),
                NavigationItem::make('service-calendar')
                    ->label(fn (): string => __('admin.navigation.service_calendar'))
                    ->url(fn (): string => route('appointments.book', ['username' => auth()->user()->username]))
                    ->icon('heroicon-o-calendar-days')
                    ->sort(1)
                    ->group(fn (): string => __('admin.navigation.appointments'))
                    ->visible(fn (): bool => auth()->check()
                        && auth()->user()->user_type === 3
                        && ! empty(auth()->user()->username ?? '')),
            ])
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(asset('web/images/logo-dark.png'))
            ->favicon(asset('favicon1.ico'))
            ->brandLogoHeight('3.5rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\SubscriptionBillingWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                SetLocale::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                // FilamentEditProfilePlugin::make()
                //     ->slug('my-profile')
                //     ->setTitle('Edit Profile')
                //     ->setNavigationLabel('Edit Profile')
                //     ->setNavigationGroup('Setting')
                //     ->setIcon('heroicon-o-cog-6-tooth')
                //     ->shouldShowBrowserSessionsForm()
                //     // ->shouldShowAvatarForm()
                //     ,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->colors([
                'primary' => '#00b3f1',
                'secondary' => '#00b3f1',
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): ?string => \Illuminate\Support\Facades\Blade::render('@livewire(\App\Livewire\TeamSelector::class)'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn (): ?string => \Illuminate\Support\Facades\Blade::render('<div class="flex items-center mr-4">@livewire(\App\Livewire\AdminNotificationBell::class)</div>'),
            );
    }
}
