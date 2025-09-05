#!/bin/bash

# Script d'optimisation pour la production
echo "🚀 Optimizing for production..."

# 1. Optimiser Composer
echo "📦 Optimizing Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Construire les assets
echo "🏗️ Building assets..."
if [ -f "package.json" ]; then
    npm install --production
    npm run build
else
    echo "No package.json found, using fallback assets"
fi

# 3. Optimiser Laravel
echo "⚙️ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Nettoyer les caches
echo "🧹 Cleaning caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Recréer les caches optimisés
echo "🔄 Recreating optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Optimiser les permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 644 public/build/assets/*.css
chmod -R 644 public/build/assets/*.js

# 7. Vérifier la structure
echo "📁 Checking structure..."
echo "Build directory contents:"
ls -la public/build/assets/ 2>/dev/null || echo "Build directory not found"

echo "✅ Production optimization completed!"
echo "🎉 Your site should now be faster and more responsive!"
