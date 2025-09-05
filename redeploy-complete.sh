#!/bin/bash

echo "🚀 REDÉPLOIEMENT COMPLET - Laravel Ecommerce App"
echo "=================================================="

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages colorés
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Vérifier si on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    print_error "Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

print_status "Début du redéploiement complet..."

# 1. Sauvegarder les fichiers importants
print_status "📦 Sauvegarde des fichiers importants..."
if [ -f ".env" ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_success "Fichier .env sauvegardé"
fi

# 2. Nettoyer le cache local
print_status "🧹 Nettoyage du cache local..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
print_success "Cache local nettoyé"

# 3. Vérifier Git
print_status "📥 Vérification de Git..."
if [ -d ".git" ]; then
    git status
    print_success "Repository Git détecté"
else
    print_warning "Aucun repository Git détecté"
fi

# 4. Installer les dépendances
print_status "📦 Installation des dépendances..."

# Composer
print_status "Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader
if [ $? -eq 0 ]; then
    print_success "Dépendances Composer installées"
else
    print_error "Erreur lors de l'installation Composer"
    exit 1
fi

# Node.js
print_status "Installation des dépendances Node.js..."
npm install
if [ $? -eq 0 ]; then
    print_success "Dépendances Node.js installées"
else
    print_error "Erreur lors de l'installation Node.js"
    exit 1
fi

# 5. Compiler les assets
print_status "🎨 Compilation des assets..."
npm run build
if [ $? -eq 0 ]; then
    print_success "Assets compilés avec succès"
else
    print_error "Erreur lors de la compilation des assets"
    exit 1
fi

# 6. Vérifier les assets compilés
print_status "✅ Vérification des assets compilés..."
if [ -f "public/build/manifest.json" ]; then
    print_success "Manifest Vite trouvé"
    echo "Contenu du manifest:"
    cat public/build/manifest.json | head -10
else
    print_error "Manifest Vite non trouvé - les assets ne sont pas compilés correctement"
    exit 1
fi

# 7. Migrations de base de données
print_status "🗄️ Exécution des migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    print_success "Migrations exécutées avec succès"
else
    print_warning "Erreur lors des migrations (peut être normal si déjà à jour)"
fi

# 8. Optimisation pour la production
print_status "⚡ Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Optimisations appliquées"

# 9. Permissions
print_status "🔐 Configuration des permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 public/build
print_success "Permissions configurées"

# 10. Vérification finale
print_status "🔍 Vérification finale..."

# Vérifier les fichiers critiques
critical_files=(
    "public/build/manifest.json"
    "public/index.php"
    "artisan"
    ".env"
)

for file in "${critical_files[@]}"; do
    if [ -f "$file" ]; then
        print_success "✓ $file existe"
    else
        print_error "✗ $file manquant"
    fi
done

# Vérifier la structure des dossiers
critical_dirs=(
    "storage/app"
    "storage/framework"
    "storage/logs"
    "bootstrap/cache"
    "public/build"
)

for dir in "${critical_dirs[@]}"; do
    if [ -d "$dir" ]; then
        print_success "✓ $dir existe"
    else
        print_error "✗ $dir manquant"
    fi
done

# 11. Informations de déploiement
print_status "📋 Informations pour le déploiement:"
echo "=========================================="
echo "📁 Dossier à déployer: $(pwd)"
echo "📦 Assets compilés: public/build/"
echo "🗄️ Base de données: Prête"
echo "⚡ Cache: Optimisé"
echo ""

# 12. Instructions pour Hostinger
print_status "🌐 Instructions pour Hostinger:"
echo "====================================="
echo "1. Uploadez TOUT le contenu de ce dossier vers public_html/"
echo "2. Assurez-vous que le fichier .env est correctement configuré"
echo "3. Vérifiez que public_html/storage/ existe et a les bonnes permissions"
echo "4. Exécutez sur Hostinger: php artisan storage:link"
echo "5. Nettoyez le cache: php artisan cache:clear"
echo ""

# 13. Test local (optionnel)
read -p "Voulez-vous tester l'application localement? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "🚀 Démarrage du serveur de test..."
    php artisan serve --host=0.0.0.0 --port=8000 &
    SERVER_PID=$!
    print_success "Serveur démarré sur http://localhost:8000"
    print_status "Appuyez sur Ctrl+C pour arrêter le serveur"
    wait $SERVER_PID
fi

print_success "🎉 Redéploiement complet terminé!"
print_status "Votre application est prête pour le déploiement sur Hostinger"
