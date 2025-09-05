# Instructions de déploiement sur Hostinger

## Problème identifié
La page admin/products s'affiche mal sur Hostinger car elle utilise des assets statiques au lieu de Vite.

## Solutions appliquées
1. ✅ Corrigé la vue `admin/products.blade.php` pour utiliser le layout principal
2. ✅ Supprimé les références aux CSS statiques

## Étapes de déploiement

### 1. Compiler les assets localement
```bash
# Windows PowerShell
.\build-production.ps1

# Ou manuellement
npm install
npm run build
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Déployer sur Hostinger
1. **Uploader tous les fichiers** sauf :
   - `node_modules/`
   - `.git/`
   - `storage/logs/`
   - `.env` (créer un nouveau sur Hostinger)

2. **IMPORTANT : Copier le dossier build dans public_html**
   ```bash
   # Sur Hostinger, copier les assets compilés
   cp -r public/build/ public_html/build/
   ```

3. **Créer le fichier .env sur Hostinger** avec :
```env
APP_NAME=Affilook
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_ICI
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=votre_db
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

# Autres configurations...
```

4. **Générer la clé d'application** :
```bash
php artisan key:generate
```

5. **Exécuter les migrations** :
```bash
php artisan migrate --force
```

6. **Créer le lien symbolique pour le storage** :
```bash
php artisan storage:link
```

### 4. Vérifier les permissions
- `storage/` : 755
- `bootstrap/cache/` : 755
- `public/` : 755

### 5. Tester
- Aller sur `https://votre-domaine.com/admin/products`
- Vérifier que l'affichage est correct

## Fichiers modifiés
- `resources/views/admin/products.blade.php` : Corrigé pour utiliser le layout principal
- `build-production.ps1` : Script de compilation pour Windows
- `build-production.sh` : Script de compilation pour Linux/Mac

## Notes importantes
- Les assets sont maintenant compilés avec Vite
- Le layout principal utilise Tailwind CSS via CDN
- Tous les styles sont maintenant cohérents entre localhost et Hostinger
