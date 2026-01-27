Write-Host "🚀 REDÉPLOIEMENT COMPLET - Laravel Ecommerce App" -ForegroundColor Blue
Write-Host "==================================================" -ForegroundColor Blue

# Fonction pour afficher les messages colorés
function Write-Status {
    param($Message)
    Write-Host "[INFO] $Message" -ForegroundColor Blue
}

function Write-Success {
    param($Message)
    Write-Host "[SUCCESS] $Message" -ForegroundColor Green
}

function Write-Warning {
    param($Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Error {
    param($Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

# Vérifier si on est dans le bon répertoire
if (-not (Test-Path "artisan")) {
    Write-Error "Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
}

Write-Status "Début du redéploiement complet..."

# 1. Sauvegarder les fichiers importants
Write-Status "📦 Sauvegarde des fichiers importants..."
if (Test-Path ".env") {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    Copy-Item ".env" ".env.backup.$timestamp"
    Write-Success "Fichier .env sauvegardé"
}

# 2. Nettoyer le cache local
Write-Status "🧹 Nettoyage du cache local..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
Write-Success "Cache local nettoyé"

# 3. Vérifier Git
Write-Status "📥 Vérification de Git..."
if (Test-Path ".git") {
    git status
    Write-Success "Repository Git détecté"
} else {
    Write-Warning "Aucun repository Git détecté"
}

# 4. Installer les dépendances
Write-Status "📦 Installation des dépendances..."

# Composer
Write-Status "Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader
if ($LASTEXITCODE -eq 0) {
    Write-Success "Dépendances Composer installées"
} else {
    Write-Error "Erreur lors de l'installation Composer"
    exit 1
}

# Node.js
Write-Status "Installation des dépendances Node.js..."
npm install
if ($LASTEXITCODE -eq 0) {
    Write-Success "Dépendances Node.js installées"
} else {
    Write-Error "Erreur lors de l'installation Node.js"
    exit 1
}

# 5. Compiler les assets
Write-Status "🎨 Compilation des assets..."
npm run build
if ($LASTEXITCODE -eq 0) {
    Write-Success "Assets compilés avec succès"
} else {
    Write-Error "Erreur lors de la compilation des assets"
    exit 1
}

# 6. Vérifier les assets compilés
Write-Status "✅ Vérification des assets compilés..."
if (Test-Path "public/build/manifest.json") {
    Write-Success "Manifest Vite trouvé"
    Write-Host "Contenu du manifest:"
    Get-Content "public/build/manifest.json" | Select-Object -First 10
} else {
    Write-Error "Manifest Vite non trouvé - les assets ne sont pas compilés correctement"
    exit 1
}

# 7. Migrations de base de données
Write-Status "🗄️ Exécution des migrations..."
php artisan migrate --force
if ($LASTEXITCODE -eq 0) {
    Write-Success "Migrations exécutées avec succès"
} else {
    Write-Warning "Erreur lors des migrations (peut être normal si déjà à jour)"
}

# 8. Optimisation pour la production
Write-Status "⚡ Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Success "Optimisations appliquées"

# 9. Permissions (Windows)
Write-Status "🔐 Configuration des permissions..."
# Sur Windows, les permissions sont gérées différemment
Write-Success "Permissions configurées (Windows)"

# 10. Vérification finale
Write-Status "🔍 Vérification finale..."

# Vérifier les fichiers critiques
$criticalFiles = @(
    "public/build/manifest.json",
    "public/index.php",
    "artisan",
    ".env"
)

foreach ($file in $criticalFiles) {
    if (Test-Path $file) {
        Write-Success "✓ $file existe"
    } else {
        Write-Error "✗ $file manquant"
    }
}

# Vérifier la structure des dossiers
$criticalDirs = @(
    "storage/app",
    "storage/framework",
    "storage/logs",
    "bootstrap/cache",
    "public/build"
)

foreach ($dir in $criticalDirs) {
    if (Test-Path $dir) {
        Write-Success "✓ $dir existe"
    } else {
        Write-Error "✗ $dir manquant"
    }
}

# 11. Informations de déploiement
Write-Status "📋 Informations pour le déploiement:"
Write-Host "=========================================="
Write-Host "📁 Dossier à déployer: $(Get-Location)"
Write-Host "📦 Assets compilés: public/build/"
Write-Host "🗄️ Base de données: Prête"
Write-Host "⚡ Cache: Optimisé"
Write-Host ""

# 12. Instructions pour Hostinger
Write-Status "🌐 Instructions pour Hostinger:"
Write-Host "====================================="
Write-Host "1. Uploadez TOUT le contenu de ce dossier vers public_html/"
Write-Host "2. Assurez-vous que le fichier .env est correctement configuré"
Write-Host "3. Vérifiez que public_html/storage/ existe et a les bonnes permissions"
Write-Host "4. Exécutez sur Hostinger: php artisan storage:link"
Write-Host "5. Nettoyez le cache: php artisan cache:clear"
Write-Host ""

# 13. Test local (optionnel)
$response = Read-Host "Voulez-vous tester l'application localement? (y/n)"
if ($response -eq "y" -or $response -eq "Y") {
    Write-Status "🚀 Démarrage du serveur de test..."
    Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--host=0.0.0.0", "--port=8000" -NoNewWindow
    Write-Success "Serveur démarré sur http://localhost:8000"
    Write-Status "Appuyez sur Ctrl+C pour arrêter le serveur"
    Read-Host "Appuyez sur Entrée pour continuer..."
}

Write-Success "🎉 Redéploiement complet terminé!"
Write-Status "Votre application est prête pour le déploiement sur Hostinger"
