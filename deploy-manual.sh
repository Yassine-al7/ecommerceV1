#!/bin/bash

# 🚀 Script de déploiement manuel pour transition CI/CD
# Utilisez ce script pendant que vous mettez en place GitHub Actions

set -e  # Arrêter en cas d'erreur

# Configuration - MODIFIEZ CES VALEURS
SERVER_HOST="votre-serveur.com"
SERVER_USER="votre-user"
PROJECT_PATH="/var/www/html/ecommerce"
SSH_KEY_PATH="~/.ssh/id_rsa"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Déploiement manuel - EcommerceApp${NC}"
echo "========================================"

# Vérification des prérequis
echo -e "${YELLOW}📋 Vérification des prérequis...${NC}"

if ! command -v git &> /dev/null; then
    echo -e "${RED}❌ Git n'est pas installé${NC}"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer n'est pas installé${NC}"
    exit 1
fi

# Vérifier les changements non committés
if ! git diff-index --quiet HEAD --; then
    echo -e "${YELLOW}⚠️  Vous avez des changements non committés${NC}"
    read -p "Voulez-vous continuer ? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

echo -e "${GREEN}✅ Prérequis OK${NC}"

# Étape 1: Tests locaux (optionnel)
echo -e "${BLUE}🧪 Étape 1: Tests locaux${NC}"
read -p "Voulez-vous lancer les tests ? (Y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Nn]$ ]]; then
    if [ -f "phpunit.xml" ]; then
        echo "Lancement des tests..."
        php artisan test
        echo -e "${GREEN}✅ Tests réussis${NC}"
    else
        echo -e "${YELLOW}⚠️  Aucun test configuré${NC}"
    fi
fi

# Étape 2: Installation des dépendances
echo -e "${BLUE}📦 Étape 2: Installation des dépendances${NC}"
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✅ Dépendances installées${NC}"

# Étape 3: Build des assets (si Vite)
echo -e "${BLUE}🎨 Étape 3: Build des assets${NC}"
if [ -f "package.json" ]; then
    npm install
    npm run build
    echo -e "${GREEN}✅ Assets buildés${NC}"
else
    echo -e "${YELLOW}⚠️  Aucun package.json trouvé${NC}"
fi

# Étape 4: Déploiement
echo -e "${BLUE}🚀 Étape 4: Déploiement sur le serveur${NC}"

# Créer un tarball des fichiers à déployer
echo "Création de l'archive de déploiement..."
tar -czf deploy.tar.gz \
    --exclude='.git' \
    --exclude='.github' \
    --exclude='node_modules' \
    --exclude='tests' \
    --exclude='*.md' \
    --exclude='.env*' \
    --exclude='phpunit.xml' \
    --exclude='vite.config.js' \
    --exclude='package-lock.json' \
    --exclude='deploy-manual.sh' \
    --exclude='deploy.tar.gz' \
    .

# Upload via SCP
echo "Upload vers le serveur..."
scp -i "$SSH_KEY_PATH" deploy.tar.gz "$SERVER_USER@$SERVER_HOST:$PROJECT_PATH/"

# Nettoyer
rm deploy.tar.gz

# Exécution des commandes sur le serveur
echo "Exécution des commandes sur le serveur..."
ssh -i "$SSH_KEY_PATH" "$SERVER_USER@$SERVER_HOST" << EOF
    cd $PROJECT_PATH

    echo "🔄 Extraction des fichiers..."
    tar -xzf deploy.tar.gz
    rm deploy.tar.gz

    echo "🔧 Installation des dépendances..."
    composer install --no-dev --optimize-autoloader

    echo "🔑 Génération de la clé..."
    php artisan key:generate

    echo "🗂️ Nettoyage du cache..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear

    echo "🗃️ Migrations..."
    php artisan migrate --force

    echo "⚡ Optimisation..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "🔒 Permissions..."
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache

    echo "✅ Déploiement terminé !"
EOF

echo -e "${GREEN}🎉 Déploiement réussi !${NC}"
echo "========================================"
echo -e "${BLUE}📊 Résumé du déploiement:${NC}"
echo "- Fichiers déployés sur $SERVER_HOST"
echo "- Dépendances mises à jour"
echo "- Cache optimisé"
echo "- Base de données migrée"
echo ""
echo -e "${YELLOW}💡 Prochaine étape: Configurez GitHub Actions pour automatiser cela !${NC}"
