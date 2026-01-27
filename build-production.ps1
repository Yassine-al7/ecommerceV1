# Compilation des assets pour la production

Write-Host "=== Compilation des assets pour la production ===" -ForegroundColor Green

# Installer les dépendances Node.js
Write-Host "Installation des dépendances Node.js..." -ForegroundColor Yellow
npm install

# Compiler les assets pour la production
Write-Host "Compilation des assets..." -ForegroundColor Yellow
npm run build

# Optimiser l'autoloader Composer
Write-Host "Optimisation de l'autoloader Composer..." -ForegroundColor Yellow
composer install --optimize-autoloader --no-dev

# Nettoyer le cache
Write-Host "Nettoyage du cache..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "=== Compilation terminée ===" -ForegroundColor Green
Write-Host "Assets compilés dans public/build/" -ForegroundColor Cyan
Write-Host "N'oubliez pas de déployer le dossier public/build/ sur Hostinger" -ForegroundColor Red
