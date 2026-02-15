#!/bin/bash

# Laravel Production Optimization Script
# Run this script on your production server

echo "🚀 Starting Laravel Optimization..."

# Step 1: Clear all caches
echo "📦 Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Step 2: Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

# Step 3: Optimize Composer autoloader
echo "📚 Optimizing Composer autoloader..."
composer install --optimize-autoloader --no-dev

# Step 4: Generate IDE helper files (optional)
# composer require --dev barryvdh/laravel-ide-helper
# php artisan ide-helper:generate
# php artisan ide-helper:models

echo "✅ Optimization complete!"
echo ""
echo "📝 Next steps:"
echo "  1. Make sure APP_DEBUG=false in .env"
echo "  2. Make sure APP_ENV=production in .env"
echo "  3. Enable OPcache in php.ini"
echo "  4. Consider using Laravel Octane: php artisan octane:install"
echo "  5. Setup Redis for caching: CACHE_DRIVER=redis"
echo ""
echo "🎉 Your Laravel app should be much faster now!"















