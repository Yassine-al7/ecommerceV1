# 🚀 Configuration CI/CD GitHub Actions

Ce guide explique comment configurer le déploiement automatique avec GitHub Actions pour votre application Laravel déjà déployée.

## 📋 Prérequis

1. **Repository GitHub** : Votre code doit être sur GitHub
2. **Accès SSH** : Accès SSH à votre serveur de production
3. **Clé SSH** : Clé privée pour la connexion SSH

## 🔧 Configuration des Secrets GitHub

Allez dans votre repository GitHub → **Settings** → **Secrets and variables** → **Actions** et ajoutez ces secrets :

### Secrets Obligatoires pour SSH (Déploiement Traditionnel)

| Secret | Description | Exemple |
|--------|-------------|---------|
| `SERVER_HOST` | Adresse IP ou domaine de votre serveur | `votre-domaine.com` |
| `SERVER_USER` | Nom d'utilisateur SSH | `root` ou `ubuntu` |
| `SSH_PRIVATE_KEY` | Clé privée SSH (contenu complet) | Voir ci-dessous |
| `PROJECT_PATH` | Chemin absolu vers votre projet | `/var/www/html/ecommerce` |

### Secrets Obligatoires pour Hostinger (FTP/SCP)

| Secret | Description | Exemple |
|--------|-------------|---------|
| `FTP_SERVER` | Adresse FTP Hostinger | `ftp.votre-domaine.com` |
| `FTP_USERNAME` | Nom d'utilisateur FTP | `votre_username@domain.com` |
| `FTP_PASSWORD` | Mot de passe FTP | `votre_mot_de_passe` |
| `FTP_REMOTE_DIR` | Dossier distant (optionnel) | `/public_html` ou `./` |

### Secrets Optionnels

| Secret | Description | Défaut |
|--------|-------------|---------|
| `SERVER_PORT` | Port SSH | `22` |
| `SLACK_WEBHOOK_URL` | Webhook Slack pour notifications | - |

## 🔑 Génération de la Clé SSH

Sur votre machine locale :

```bash
# Générer une nouvelle clé SSH (si vous n'en avez pas)
ssh-keygen -t rsa -b 4096 -C "github-actions@yourdomain.com"

# Afficher la clé publique
cat ~/.ssh/id_rsa.pub
```

### Ajout de la clé publique sur le serveur

Connectez-vous à votre serveur et ajoutez la clé publique :

```bash
# Créer le dossier .ssh s'il n'existe pas
mkdir -p ~/.ssh

# Ajouter la clé publique
echo "votre_clé_publique_ici" >> ~/.ssh/authorized_keys

# Définir les bonnes permissions
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

### Configuration du Secret SSH_PRIVATE_KEY

1. Copiez le contenu de votre clé privée :
   ```bash
   cat ~/.ssh/id_rsa
   ```

2. Dans GitHub → Settings → Secrets → Actions :
   - **Name**: `SSH_PRIVATE_KEY`
   - **Value**: Collez tout le contenu de la clé privée (avec `-----BEGIN OPENSSH PRIVATE KEY-----` et `-----END OPENSSH PRIVATE KEY-----`)

## 📁 Fichiers de Configuration

### 1. Fichier de Test (.env.ci)

Créez un fichier `.env.ci` dans votre repository pour les tests CI :

```bash
cp .env.example .env.ci
```

Modifiez les valeurs pour la base de données de test :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_db
DB_USERNAME=root
DB_PASSWORD=root
```

### 2. Structure des Fichiers

Assurez-vous que ces fichiers existent :
```
.github/
  workflows/
    deploy.yml          # Workflow principal
.env.ci                 # Configuration pour les tests CI
.env.example           # Template pour .env.ci
```

## 🌐 Configuration Spécifique Hostinger

### Via FTP (Recommandé pour Hostinger)

Pour déployer sur Hostinger via FTP, utilisez le workflow `deploy-simple.yml` qui est déjà configuré.

**Informations Hostinger nécessaires :**
- **Adresse FTP** : `ftp.votre-domaine.com`
- **Nom d'utilisateur** : Généralement `votre_username@domain.com`
- **Mot de passe** : Votre mot de passe FTP (différent du mot de passe cPanel)
- **Dossier distant** : `/public_html` (racine du site web)

### Étapes pour Hostinger :

1. **Récupérez vos informations FTP :**
   - Connectez-vous à votre cPanel Hostinger
   - Allez dans **Fichiers** → **Gestionnaire de fichiers**
   - Notez l'adresse FTP et vos credentials

2. **Ajoutez les secrets dans GitHub :**
   ```
   FTP_SERVER = ftp.votre-domaine.com
   FTP_USERNAME = votre_username@domain.com
   FTP_PASSWORD = votre_mot_de_passe_ftp
   FTP_REMOTE_DIR = /public_html
   ```

3. **Le workflow FTP exclut automatiquement :**
   - `.git/` et `.github/`
   - `node_modules/`
   - Fichiers de test
   - Fichiers de configuration locaux (`.env*`)

## 🚀 Comment Ça Marche

### Déclencheurs Automatiques

Le workflow se déclenche :
- ✅ **Push** sur `main` ou `master`
- ✅ **Pull Request** vers `main` ou `master`

### Choix du Workflow de Déploiement

Vous avez **deux workflows** disponibles :

#### 🚀 Workflow SSH (`deploy.yml`) - Complet
- **Avantages** : Tests automatiques, migrations DB, optimisations complètes
- **Utilisation** : Serveurs dédiés/VPS avec accès SSH
- **Configuration** : Nécessite clés SSH et accès serveur complet

#### 📁 Workflow FTP (`deploy-simple.yml`) - Hostinger
- **Avantages** : Simple, compatible Hostinger, déploiement rapide
- **Utilisation** : Hébergement partagé comme Hostinger
- **Configuration** : Seulement credentials FTP

### Processus de Déploiement

#### Pour SSH (`deploy.yml`) :
1. **Tests Automatiques** 🧪
   - Installation des dépendances PHP
   - Configuration de la base de données de test
   - Exécution des migrations
   - Lancement des tests PHPUnit

2. **Déploiement** 🚀
   - Connexion SSH au serveur
   - Sauvegarde du `.env` actuel
   - Upload des fichiers
   - Installation des dépendances
   - Migrations de base de données
   - Optimisation du cache
   - Configuration des permissions

#### Pour FTP (`deploy-simple.yml`) :
1. **Installation Dépendances** 📦
   - Installation de PHP et Composer
   - Build des assets (si nécessaire)

2. **Déploiement** 🚀
   - Upload FTP vers Hostinger
   - Exclusion automatique des fichiers inutiles

3. **Notifications** 📢
   - Slack (si configuré)
   - Statut dans GitHub Actions

## 🔧 Personnalisation

### Modifier la Branche de Déploiement

Dans `.github/workflows/deploy.yml`, ligne 4-5 :
```yaml
branches: [ main, master ]  # Changez selon vos besoins
```

### Ajouter des Commandes Personnalisées

Dans la section `script` du job `deploy`, ajoutez vos commandes :
```yaml
script: |
  cd ${{ secrets.PROJECT_PATH }}

  # Vos commandes personnalisées ici
  php artisan custom:command

  echo "Commande exécutée"
```

### Déploiement Conditionnel

Le déploiement ne se fait que sur les branches principales :
```yaml
if: github.ref == 'refs/heads/main' || github.ref == 'refs/heads/master'
```

## 🐛 Dépannage

### Problèmes Courants

1. **Erreur SSH** :
   - Vérifiez que la clé SSH est correcte
   - Assurez-vous que l'utilisateur a les droits sudo
   - Testez la connexion manuellement

2. **Erreur Base de Données** :
   - Vérifiez les credentials dans `.env.ci`
   - Assurez-vous que MySQL est accessible

3. **Erreur Permissions** :
   - Le workflow essaie automatiquement de corriger les permissions
   - Si ça ne marche pas, ajustez manuellement sur le serveur

### Logs et Debugging

- Allez dans **Actions** tab de votre repository GitHub
- Cliquez sur le workflow qui a échoué
- Vérifiez les logs détaillés de chaque étape

## 📞 Support

Si vous rencontrez des problèmes :

1. Vérifiez les logs GitHub Actions
2. Testez les connexions SSH manuellement
3. Vérifiez la configuration des secrets
4. Consultez la documentation GitHub Actions

---

🎉 **Votre CI/CD est maintenant configuré !** Chaque push sur votre branche principale déclenchera automatiquement les tests et le déploiement.
