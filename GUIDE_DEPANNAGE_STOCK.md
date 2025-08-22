# Guide de Dépannage - Système de Stock et Messages

## 🚨 **Problème Détecté**

Lors de l'exécution du test, vous avez rencontré cette erreur :
```
PHP Fatal error: Call to a member function connection() on null
```

## 🔍 **Cause du Problème**

Le test essaie d'utiliser les modèles Laravel sans initialiser correctement l'application Laravel. Cela se produit quand :
1. L'application Laravel n'est pas démarrée
2. La connexion à la base de données n'est pas établie
3. Les services ne sont pas initialisés

## 🛠️ **Solutions**

### **Solution 1: Test via Artisan (Recommandé)**

Exécutez le test qui utilise Artisan :
```bash
php test_stock_artisan.php
```

Ce test vérifie la structure sans accéder directement à la base de données.

### **Solution 2: Test via Interface Web**

Testez directement via votre navigateur :

1. **Vérifier le stock** : Allez sur `/admin/stock`
2. **Créer un message** : Allez sur `/admin/messages/create`
3. **Voir les messages** : Allez sur `/seller/dashboard`

### **Solution 3: Test via Tinker**

Utilisez Laravel Tinker pour tester les modèles :
```bash
php artisan tinker
```

Puis testez :
```php
// Vérifier les produits
App\Models\Product::count();

// Vérifier les messages
App\Models\AdminMessage::count();

// Vérifier le stock faible
App\Models\Product::where('quantite_stock', '<=', 5)->count();
```

## 🔧 **Vérifications Préalables**

### **1. Vérifier la Base de Données**
```bash
# Vérifier l'état des migrations
php artisan migrate:status

# Vérifier la configuration
php artisan config:show database

# Tester la connexion
php artisan tinker
```

### **2. Vérifier les Variables d'Environnement**
Assurez-vous que votre fichier `.env` contient :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### **3. Vérifier la Connexion**
```bash
# Tester la connexion à la base
php artisan db:show

# Vérifier les tables
php artisan db:table
```

## 📋 **Tests à Effectuer**

### **Test 1: Vérification de Base**
```bash
php test_stock_artisan.php
```

### **Test 2: Test via Interface**
1. Ouvrez votre navigateur
2. Allez sur `http://localhost:8000/admin/stock`
3. Vérifiez que les indicateurs de stock s'affichent

### **Test 3: Test des Messages**
1. Allez sur `http://localhost:8000/admin/messages/create`
2. Créez un message d'alerte stock
3. Vérifiez l'affichage sur `/seller/dashboard`

## 🎯 **Ce que vous devriez voir**

### **Sur `/admin/stock` :**
- Liste des produits avec leur stock
- Indicateurs colorés :
  - 🔴 **Faible** (≤5 unités)
  - 🟡 **Moyen** (6-20 unités)
  - 🟢 **Bon** (>20 unités)
- Statistiques en haut de page

### **Sur `/admin/messages/create` :**
- Formulaire de création de message
- Champs : titre, message, type, priorité, rôles cibles
- Bouton de sauvegarde

### **Sur `/seller/dashboard` :**
- Messages admin affichés en haut
- Indicateurs de stock si configurés
- Interface vendeur

## 🚨 **En cas de Problème Persistant**

### **1. Vérifier les Logs**
```bash
tail -f storage/logs/laravel.log
```

### **2. Vérifier les Permissions**
```bash
# Donner les bonnes permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### **3. Vider le Cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### **4. Redémarrer l'Application**
```bash
# Si vous utilisez un serveur local
php artisan serve

# Ou redémarrez votre serveur web (Apache/Nginx)
```

## 📊 **Résultats Attendus**

Après avoir résolu les problèmes, vous devriez voir :

✅ **Tests de structure** : Tous les fichiers et routes sont trouvés
✅ **Interface web** : Les pages s'affichent correctement
✅ **Indicateurs de stock** : Couleurs et badges fonctionnent
✅ **Système de messages** : Création et affichage des messages

## 💡 **Prochaines Étapes**

Une fois les tests de base passés :

1. **Implémenter les notifications automatiques**
2. **Créer le système d'alerte en temps réel**
3. **Ajouter les rapports quotidiens**
4. **Optimiser les performances**

---

**Dernière mise à jour** : $(date)
**Statut** : Dépannage en cours 🔧
**Priorité** : Résoudre les problèmes de connexion ⚠️
