# Laravel Optimization for cPanel Hosting

## 🎯 Running Laravel on cPanel (Without Octane)

Since cPanel shared hosting doesn't support Laravel Octane, here are the best optimization strategies:

---

## ✅ Step-by-Step Optimization for cPanel

### 1. **Upload Optimized Files**

Before deploying, run these commands locally:

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Optimize Composer
composer install --optimize-autoloader --no-dev
```

### 2. **Update .env for Production**

Update your `.env` file on cPanel:

```env
APP_NAME="FitScout"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Cache Configuration
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Optimization
BCRYPT_ROUNDS=10
```

### 3. **Setup via SSH (Terminal)**

If you have SSH access to cPanel:

```bash
# Navigate to your Laravel project
cd ~/public_html  # or wherever your Laravel is installed

# Run optimization commands
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set correct permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
```

### 4. **Setup .htaccess (Important!)**

Make sure your `public/.htaccess` file is correct:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Enable GZIP Compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
    </IfModule>

    # Browser Caching
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType image/jpg "access plus 1 year"
        ExpiresByType image/jpeg "access plus 1 year"
        ExpiresByType image/gif "access plus 1 year"
        ExpiresByType image/png "access plus 1 year"
        ExpiresByType text/css "access plus 1 month"
        ExpiresByType application/javascript "access plus 1 month"
    </IfModule>
</IfModule>
```

---

## 🚀 Performance Optimizations for cPanel

### 1. **Enable OPcache**

Contact your hosting provider or enable in cPanel > Select PHP Version > Options:
- `opcache.enable = On`
- `opcache.memory_consumption = 256`
- `opcache.max_accelerated_files = 10000`

### 2. **Use Database Optimization**

Add indexes to your database:

```sql
-- Add indexes to frequently queried columns
ALTER TABLE gigs ADD INDEX idx_is_active (is_active);
ALTER TABLE gigs ADD INDEX idx_user_id (user_id);
ALTER TABLE gigs ADD INDEX idx_subcategory_id (subcategory_id);
ALTER TABLE gig_reviews ADD INDEX idx_gig_id (gig_id);
ALTER TABLE gig_reviews ADD INDEX idx_user_id (user_id);
ALTER TABLE users ADD INDEX idx_user_type (user_type);
ALTER TABLE notifications ADD INDEX idx_user_read (user_id, read_at);
```

### 3. **Optimize Images**

- Compress images before uploading
- Use WebP format
- Implement lazy loading in your views
- Use CDN if possible (Cloudflare free tier)

### 4. **Implement Caching in Code**

Update your controllers to cache expensive queries:

**Example: HomeController.php**
```php
public function index()
{
    // Cache popular gigs for 1 hour
    $popularGigs = Cache::remember('popular_gigs', 3600, function () {
        return Gig::with(['user.profile', 'images'])
            ->where('is_active', true)
            ->orderBy('impressions', 'desc')
            ->take(8)
            ->get();
    });

    // Cache categories for 1 day
    $categories = Cache::remember('categories', 86400, function () {
        return Category::with('subcategories')->get();
    });

    return view('web.index', compact('popularGigs', 'categories'));
}
```

### 5. **Eager Loading (Prevent N+1 Queries)**

Always use `with()` to load relationships:

```php
// Bad - N+1 queries
$gigs = Gig::all();

// Good - Eager loading
$gigs = Gig::with(['user', 'images', 'reviews', 'packages'])->get();
```

---

## 🔧 cPanel-Specific Setup

### 1. **Setup Cron Jobs for Queue**

In cPanel > Cron Jobs, add:

```
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### 2. **Setup Symlink for Storage**

SSH into your server:
```bash
cd ~/public_html
php artisan storage:link
```

### 3. **File Structure for cPanel**

Your Laravel project structure on cPanel should be:

```
/home/username/
├── public_html/           # This is your Laravel's public folder
│   ├── index.php
│   ├── .htaccess
│   └── ... (public assets)
├── laravel/              # Your Laravel application
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   └── vendor/
```

Or symlink public folder:
```bash
ln -s ~/laravel/public ~/public_html
```

---

## 📊 Expected Performance Improvements

Without Octane but with these optimizations:

| Optimization | Speed Improvement |
|-------------|-------------------|
| Config/Route Cache | 30-40% faster |
| OPcache | 20-30% faster |
| Database Indexes | 50-100% faster |
| Query Caching | 40-60% faster |
| Eager Loading | 80-90% faster |
| GZIP Compression | 10-20% faster |
| Browser Caching | 30-50% faster (repeat visits) |

**Combined**: You should see **2-3x performance improvement**

---

## 🎯 Alternative: Use VPS Instead of Shared cPanel

If you need Laravel Octane's performance (3-4x faster), consider:

### Recommended VPS Providers:
1. **DigitalOcean** ($6/month) - Easy Laravel setup
2. **Vultr** ($6/month) - Good performance
3. **Linode** ($5/month) - Reliable
4. **AWS Lightsail** ($5/month) - Scalable
5. **Hetzner** ($4/month) - Best value

### With VPS, you can:
- Install Laravel Octane (Swoole/FrankenPHP)
- Use Redis for caching
- Setup queue workers
- Full control over server

---

## 🚨 Common cPanel Issues & Fixes

### Issue 1: "500 Internal Server Error"
```bash
# Fix permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
```

### Issue 2: "Composer not found"
```bash
# Use full path
/usr/local/bin/php /usr/local/bin/composer install
```

### Issue 3: "Storage link broken"
```bash
# Remove old symlink and create new
rm public/storage
php artisan storage:link
```

### Issue 4: "Session/Cache errors"
```bash
# Clear and rebuild cache
php artisan cache:clear
php artisan config:cache
```

---

## 📝 Deployment Checklist for cPanel

- [ ] Run `php artisan optimize` locally
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Update `.env` with production values
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Upload optimized files to cPanel
- [ ] Run `php artisan migrate --force` (if needed)
- [ ] Run `php artisan storage:link`
- [ ] Setup cron jobs
- [ ] Check file permissions (755 for folders, 644 for files)
- [ ] Test website functionality
- [ ] Enable OPcache in cPanel
- [ ] Setup GZIP compression
- [ ] Configure browser caching

---

## 💡 Pro Tips for cPanel Performance

1. **Use Cloudflare CDN** (Free) - Speeds up static assets
2. **Enable cPanel APCu** - Faster data caching
3. **Optimize Database** - Run `OPTIMIZE TABLE` monthly
4. **Minimize Middleware** - Remove unused middleware
5. **Use Pagination** - Don't load 1000s of records at once
6. **Implement Lazy Loading** - Load images as user scrolls
7. **Compress Assets** - Minify CSS/JS files
8. **Reduce DB Queries** - Use caching aggressively

---

## 📞 Still Slow?

If performance is still poor:
1. **Upgrade to VPS** - Shared hosting has limits
2. **Use Laravel Forge** - Automated server management
3. **Implement CDN** - Cloudflare, AWS CloudFront
4. **Database Optimization** - Add indexes, optimize queries
5. **Profile Your App** - Use Laravel Telescope to find bottlenecks

---

## 🎉 Summary

**Laravel Octane won't work on standard cPanel**, but you can still achieve **2-3x performance improvement** with:
- ✅ Config/Route caching
- ✅ OPcache enabled
- ✅ Database optimization
- ✅ Code-level caching
- ✅ GZIP compression
- ✅ Browser caching

For **3-4x improvement with Octane**, you need a **VPS** ($5-6/month).















