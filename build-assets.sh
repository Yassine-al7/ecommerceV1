#!/bin/bash

# Script pour construire les assets sur Hostinger
echo "🔨 Building assets for production..."

# Installer les dépendances Node.js
echo "📦 Installing Node.js dependencies..."
npm install

# Construire les assets avec Vite
echo "🏗️ Building assets with Vite..."
npm run build

# Vérifier que le dossier build existe
if [ -d "public/build" ]; then
    echo "✅ Assets built successfully!"
    echo "📁 Build directory contents:"
    ls -la public/build/
else
    echo "❌ Build directory not found!"
    echo "📁 Public directory contents:"
    ls -la public/
fi

echo "🎉 Build process completed!"
