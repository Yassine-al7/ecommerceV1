# 🔐 Vérification des Secrets GitHub pour CI/CD

## 📋 Secrets Requis

Votre CI/CD nécessite ces secrets dans GitHub Actions :

### Pour SSH Deployment (Recommandé)
```
SERVER_HOST=votre-domaine.com
SERVER_USER=root
SSH_PRIVATE_KEY=-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
PROJECT_PATH=/var/www/html/ecommerce
SERVER_PORT=22
```

### Pour FTP Deployment (Hostinger)
```
FTP_SERVER=ftp.votre-domaine.com
FTP_USERNAME=votre_username@domain.com
FTP_PASSWORD=votre_mot_de_passe_ftp
FTP_REMOTE_DIR=/public_html
```

## 🔍 Comment Vérifier

### 1. Aller dans GitHub
1. Allez sur votre repository GitHub
2. Cliquez sur **Settings** (en haut à droite)
3. Dans le menu de gauche, cliquez sur **Secrets and variables** → **Actions**

### 2. Vérifier les Secrets
Vous devriez voir une liste de secrets. Vérifiez que tous les secrets requis sont présents.

### 3. Tester la Connexion
Pour tester SSH :
```bash
ssh -i ~/.ssh/id_rsa root@votre-domaine.com
```

Pour tester FTP :
```bash
ftp ftp.votre-domaine.com
```

## 🚀 Déclencher le Déploiement

### Méthode 1 : Push automatique
```bash
git add .
git commit -m "Redéploiement complet"
git push origin main
```

### Méthode 2 : Déploiement manuel
1. Allez sur **Actions** dans GitHub
2. Cliquez sur **🚀 SSH Deploy Only**
3. Cliquez sur **Run workflow**
4. Sélectionnez la branche **main**
5. Cliquez sur **Run workflow**

## 🔧 Dépannage

### Erreur SSH
- Vérifiez que la clé SSH est correcte
- Vérifiez que l'utilisateur a les droits sudo
- Testez la connexion manuellement

### Erreur FTP
- Vérifiez les credentials FTP
- Vérifiez que le serveur FTP est accessible
- Vérifiez les permissions du dossier distant

### Erreur de Permissions
- Vérifiez que l'utilisateur peut écrire dans le dossier de destination
- Vérifiez les permissions des fichiers

## 📊 Monitoring

### Vérifier les Logs
1. Allez sur **Actions** dans GitHub
2. Cliquez sur le workflow qui a échoué
3. Vérifiez les logs détaillés de chaque étape

### Statuts Possibles
- ✅ **Succès** : Déploiement réussi
- ❌ **Échec** : Erreur à corriger
- ⏳ **En cours** : Déploiement en cours
- ⚠️ **Annulé** : Déploiement annulé

## 🎯 Prochaines Étapes

1. **Vérifiez** que tous les secrets sont configurés
2. **Testez** la connexion SSH/FTP
3. **Déclenchez** le déploiement
4. **Surveillez** les logs pour détecter les erreurs
5. **Vérifiez** que le site fonctionne après déploiement

---

**Votre CI/CD est prêt ! 🚀**
