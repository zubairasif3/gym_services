<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
