#!/bin/bash

echo "=== Compilation des assets pour la production ==="

# Installer les dépendances Node.js
echo "Installation des dépendances Node.js..."
npm install

# Compiler les assets pour la production
echo "Compilation des assets..."
npm run build

# Optimiser l'autoloader Composer
echo "Optimisation de l'autoloader Composer..."
composer install --optimize-autoloader --no-dev

# Nettoyer le cache
echo "Nettoyage du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Compilation terminée ==="
echo "Assets compilés dans public/build/"
echo "N'oubliez pas de déployer le dossier public/build/ sur Hostinger"
