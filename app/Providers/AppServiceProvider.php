<?php

namespace App\Providers;

use App\Http\Responses\Filament\AdminLogoutResponse;
use App\Models\Category;
use App\Models\ProfileMedia;
use App\Models\Service;
use App\Observers\ProfileMediaObserver;
use App\Observers\ServiceObserver;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as FilamentLogoutResponseContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FilamentLogoutResponseContract::class, AdminLogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ProfileMedia::observe(ProfileMediaObserver::class);
        Service::observe(ServiceObserver::class);

       // Retrieve active categories with active gigs
        $categories = Category::with(['gigs' => function ($query) {
            $query->where('gigs.is_active', true); // Filter active gigs
        }, 'subcategories'])
        ->where('categories.is_active', true) // Filter active categories
        ->get();

        // If you need this data available globally (e.g., to all views)
        view()->share('active_categories', $categories); // Share the data across all views
    }
}
