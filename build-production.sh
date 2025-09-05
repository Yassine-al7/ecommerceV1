#!/bin/bash

echo "🚀 Building production assets..."

# Installer les dépendances Node.js
echo "📦 Installing Node.js dependencies..."
npm install

# Build des assets pour la production
echo "🎨 Building assets for production..."
npm run build

# Vérifier que les assets sont créés
echo "✅ Checking built assets..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Vite manifest found"
    cat public/build/manifest.json
else
    echo "❌ Vite manifest not found"
fi

if [ -f "public/build/assets/app.css" ]; then
    echo "✅ CSS assets found"
    ls -la public/build/assets/
else
    echo "❌ CSS assets not found"
fi

echo "🎯 Production build completed!"
