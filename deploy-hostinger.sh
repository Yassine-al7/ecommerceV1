#!/bin/bash
# Hostinger Deployment Script for mPDF

echo "🚀 Deploying to Hostinger with mPDF support..."

# 1. Install/update mPDF
echo "📦 Installing mPDF..."
composer install --no-dev --optimize-autoloader

# 2. Clear all Laravel caches
echo "🧹 Clearing Laravel caches..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Create mPDF temp directory with proper permissions
echo "📁 Creating mPDF directories..."
mkdir -p storage/app/mpdf
chmod -R 755 storage/app/mpdf
chmod -R 755 storage/logs
chmod -R 755 storage/framework

# 4. Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Set proper permissions for Hostinger
echo "🔒 Setting permissions..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

echo "✅ Deployment complete! mPDF should now work on Hostinger."
echo "🔗 Test the PDF download feature in admin/invoices"
