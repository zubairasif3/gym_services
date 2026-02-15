#!/bin/bash

# Laravel Optimization for cPanel
# Run this via SSH: bash cpanel-optimize.sh

echo "🚀 Starting Laravel Optimization for cPanel..."
echo ""

# Get the current directory
LARAVEL_PATH=$(pwd)

echo "📍 Working directory: $LARAVEL_PATH"
echo ""

# Step 1: Clear all caches
echo "🧹 Step 1/6: Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
echo "✅ Caches cleared!"
echo ""

# Step 2: Optimize for production
echo "⚡ Step 2/6: Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Configurations cached!"
echo ""

# Step 3: Optimize application
echo "🔧 Step 3/6: Optimizing application..."
php artisan optimize
echo "✅ Application optimized!"
echo ""

# Step 4: Fix permissions
echo "🔐 Step 4/6: Setting correct permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
echo "✅ Permissions set!"
echo ""

# Step 5: Create storage symlink (if not exists)
echo "🔗 Step 5/6: Creating storage symlink..."
php artisan storage:link
echo "✅ Storage linked!"
echo ""

# Step 6: Optimize Composer autoloader
echo "📦 Step 6/6: Optimizing Composer autoloader..."
composer install --optimize-autoloader --no-dev
echo "✅ Composer optimized!"
echo ""

echo "═══════════════════════════════════════════════════"
echo "✅ OPTIMIZATION COMPLETE!"
echo "═══════════════════════════════════════════════════"
echo ""
echo "📝 Important reminders:"
echo "  ✓ Make sure APP_DEBUG=false in .env"
echo "  ✓ Make sure APP_ENV=production in .env"
echo "  ✓ Enable OPcache in cPanel PHP settings"
echo "  ✓ Setup cron job: * * * * * cd $LARAVEL_PATH && php artisan schedule:run"
echo ""
echo "🎉 Your Laravel app should be much faster now!"
echo ""
echo "📊 Expected improvements:"
echo "  • Config cache: 30-40% faster"
echo "  • Route cache: 20-30% faster"
echo "  • View cache: 15-25% faster"
echo "  • Combined: 2-3x performance boost"
echo ""
echo "💡 For even better performance:"
echo "  • Add database indexes"
echo "  • Use code-level caching (Cache::remember)"
echo "  • Enable GZIP compression"
echo "  • Setup Cloudflare CDN (free)"
echo ""
echo "🚀 Need more speed? Consider upgrading to VPS for Laravel Octane support!"















