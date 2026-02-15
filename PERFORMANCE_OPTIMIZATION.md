# Laravel Performance Optimization Guide

## 🚀 Performance Issues & Solutions

### Current Status
Your Laravel application is slow after deployment. This is common and can be fixed with several optimization techniques.

---

## 📋 Quick Fixes (Do These First!)

### 1. **Enable Caching (CRITICAL)**
Run these commands on your server:

```bash
# Clear all caches first
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Then optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 2. **Enable OPcache (PHP Level)**
Make sure OPcache is enabled in your `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 3. **Optimize Composer Autoloader**
```bash
composer install --optimize-autoloader --no-dev
```

### 4. **Database Optimization**
- Add indexes to frequently queried columns
- Use eager loading to prevent N+1 queries
- Enable query caching

---

## 🔧 Laravel Octane Setup (Recommended for Production)

Laravel Octane supercharges your application using FrankenPHP, Swoole, or RoadRunner.

### Installation Steps:

#### Step 1: Install Octane
```bash
composer require laravel/octane
```

#### Step 2: Install Octane with FrankenPHP (Recommended)
```bash
php artisan octane:install --server=frankenphp
```

Or with Swoole:
```bash
php artisan octane:install --server=swoole
```

#### Step 3: Configure Octane
Publish the config:
```bash
php artisan vendor:publish --tag=octane-config
```

#### Step 4: Run Octane in Production
```bash
php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000 --workers=4 --max-requests=1000
```

---

## ⚡ Additional Optimizations

### 1. **Enable Redis for Caching & Sessions**

Install Redis:
```bash
composer require predis/predis
```

Update `.env`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
```

### 2. **Optimize Images**
- Use WebP format
- Implement lazy loading
- Use CDN for static assets

### 3. **Enable Gzip Compression**
Add to `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### 4. **Database Query Optimization**
- Use `select()` to limit columns
- Use `chunk()` for large datasets
- Add database indexes
- Use database query caching

---

## 📊 Performance Monitoring

### Install Laravel Debugbar (Dev only)
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Install Laravel Telescope (Production monitoring)
```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

---

## 🎯 Specific Fixes for Your App

### 1. Eager Load Relationships
Update controllers to use eager loading:

```php
// Bad (N+1 query problem)
$gigs = Gig::all();
foreach ($gigs as $gig) {
    echo $gig->user->name; // Triggers additional query
}

// Good (Eager loading)
$gigs = Gig::with(['user', 'images', 'reviews'])->get();
```

### 2. Cache Expensive Queries
```php
$popularGigs = Cache::remember('popular_gigs', 3600, function () {
    return Gig::with(['user', 'images'])
        ->where('is_active', true)
        ->orderBy('impressions', 'desc')
        ->take(10)
        ->get();
});
```

### 3. Use Queue for Heavy Tasks
```php
// Send emails, notifications in background
dispatch(new SendNotificationJob($user, $notification));
```

---

## 🔥 Production Deployment Checklist

- [ ] Run `php artisan optimize`
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Enable OPcache
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Use Redis for cache/sessions
- [ ] Enable Gzip compression
- [ ] Use CDN for assets
- [ ] Install Laravel Octane (optional but recommended)
- [ ] Setup Queue workers
- [ ] Enable HTTPS
- [ ] Setup database indexes

---

## 📈 Expected Performance Improvements

| Optimization | Speed Improvement |
|-------------|-------------------|
| Config/Route Cache | 30-40% |
| OPcache | 20-30% |
| Laravel Octane | 200-300% |
| Redis Cache | 40-60% |
| Database Indexes | 50-100% |
| Eager Loading | 80-90% |

---

## 🚨 Common Mistakes to Avoid

1. ❌ Not running `php artisan optimize` in production
2. ❌ Leaving `APP_DEBUG=true` in production
3. ❌ Not using eager loading (N+1 queries)
4. ❌ Not using caching for expensive queries
5. ❌ Not optimizing database queries
6. ❌ Loading too much data at once
7. ❌ Not using queues for heavy tasks
8. ❌ Not enabling OPcache

---

## 📞 Need Help?

If you're still experiencing issues after these optimizations:
1. Check server resources (CPU, RAM, Disk)
2. Review slow query logs
3. Use Laravel Telescope to identify bottlenecks
4. Consider upgrading server resources
5. Implement CDN for static assets
















