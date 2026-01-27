#!/bin/bash

echo "🚀 Starting production deployment..."

# 1. Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin main

# 2. Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# 4. Build assets for production
echo "🎨 Building assets for production..."
npm run build

# 5. Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 6. Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# 7. Cache for production
echo "⚡ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 public/build

# 9. Verify assets
echo "✅ Verifying assets..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Vite manifest found"
    echo "📄 Manifest content:"
    cat public/build/manifest.json
else
    echo "❌ Vite manifest not found - assets may not be built correctly"
fi

echo "🎯 Production deployment completed!"
echo "🌐 Your site should now be optimized and fast!"
