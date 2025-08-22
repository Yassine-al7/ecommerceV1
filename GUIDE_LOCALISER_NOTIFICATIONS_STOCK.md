# 🔍 Guide pour Localiser et Tester le Système de Notifications de Stock

## 📍 **Où se trouvent les Notifications de Stock ?**

### **1. 🎯 Interface Web - Gestion du Stock par Couleur**
```
URL : http://127.0.0.1:8000/admin/color-stock
Accès : Menu Admin → Gestion du Stock par Couleur
```

### **2. 📱 Interface Web - Gestion du Stock Général**
```
URL : http://127.0.0.1:8000/admin/stock
Accès : Menu Admin → Stock
```

### **3. 🔔 Composant d'Alertes de Stock**
```
Fichier : resources/views/components/stock-alerts.blade.php
Affichage : Sur le dashboard admin
```

## 🧪 **Comment Tester les Notifications de Stock ?**

### **Test 1 : Accéder à la Gestion du Stock par Couleur**
```bash
# 1. Aller sur http://127.0.0.1:8000/admin/color-stock
# 2. Vérifier que la page se charge
# 3. Voir la liste des produits avec leur stock par couleur
```

### **Test 2 : Modifier le Stock d'une Couleur**
```bash
# 1. Cliquer sur un produit dans la liste
# 2. Modifier la quantité d'une couleur (mettre à 0 pour tester l'alerte)
# 3. Sauvegarder
# 4. Vérifier que la notification s'affiche
```

### **Test 3 : Vérifier les Alertes sur le Dashboard**
```bash
# 1. Aller sur http://127.0.0.1:8000/admin/dashboard
# 2. Chercher le composant "stock-alerts"
# 3. Vérifier qu'il affiche les alertes de stock
```

### **Test 4 : Utiliser la Commande Artisan**
```bash
# Dans le terminal, exécuter :
php artisan stock:check-critical

# Cette commande vérifie tous les stocks et envoie des notifications
```

## 📁 **Fichiers du Système de Notifications**

### **1. 🎨 Notification de Stock**
```
Fichier : app/Notifications/ColorStockAlertNotification.php
Fonction : Envoie des emails et notifications en base de données
```

### **2. 🔧 Service de Notifications**
```
Fichier : app/Services/ColorStockNotificationService.php
Fonction : Gère toute la logique des notifications de stock
```

### **3. 🎮 Contrôleur de Gestion**
```
Fichier : app/Http/Controllers/Admin/ColorStockController.php
Fonction : Interface web pour gérer le stock par couleur
```

### **4. ⚡ Commande Artisan**
```
Fichier : app/Console/Commands/CheckCriticalStockLevels.php
Fonction : Vérification automatique des stocks critiques
```

### **5. 🎭 Vues d'Interface**
```
- resources/views/admin/color_stock/index.blade.php (Liste des produits)
- resources/views/admin/color_stock/show.blade.php (Détail d'un produit)
- resources/views/components/stock-alerts.blade.php (Alertes sur dashboard)
```

## 🚀 **Test Complet du Système**

### **Étape 1 : Vérifier l'Accès**
```bash
# 1. Se connecter en tant qu'admin
# 2. Aller sur http://127.0.0.1:8000/admin/color-stock
# 3. Vérifier que la page se charge sans erreur
```

### **Étape 2 : Créer une Alerte de Stock**
```bash
# 1. Cliquer sur un produit
# 2. Modifier le stock d'une couleur à 0
# 3. Sauvegarder
# 4. Vérifier que la notification apparaît
```

### **Étape 3 : Vérifier les Notifications**
```bash
# 1. Aller sur http://127.0.0.1:8000/admin/messages
# 2. Chercher les messages d'alerte de stock
# 3. Vérifier qu'ils sont bien créés
```

### **Étape 4 : Tester la Commande Automatique**
```bash
# Dans le terminal :
php artisan stock:check-critical --force

# Vérifier que :
# - Les stocks critiques sont détectés
# - Les notifications sont envoyées
# - Les messages admin sont créés
```

## 🔍 **Vérifications à Faire**

### **1. ✅ Routes Accessibles**
```bash
# Vérifier que ces routes fonctionnent :
GET  /admin/color-stock           → Liste des produits
GET  /admin/color-stock/{id}      → Détail d'un produit
POST /admin/color-stock/{id}/update → Mise à jour du stock
GET  /admin/color-stock/statistics → Statistiques
GET  /admin/color-stock/search     → Recherche par couleur
GET  /admin/color-stock/export     → Export CSV
```

### **2. ✅ Notifications Fonctionnelles**
```bash
# Vérifier que :
- Les emails sont envoyés (si configuré)
- Les messages admin sont créés
- Les logs sont écrits
- Les alertes s'affichent sur le dashboard
```

### **3. ✅ Interface Responsive**
```bash
# Tester sur :
- Desktop (1920x1080)
- Tablette (768x1024)
- Mobile (375x667)
```

## 🐛 **Débogage des Problèmes**

### **Si la page ne se charge pas :**
```bash
# 1. Vérifier les logs Laravel : storage/logs/laravel.log
# 2. Vérifier que l'utilisateur est admin
# 3. Vérifier que les routes sont bien définies
```

### **Si les notifications ne s'envoient pas :**
```bash
# 1. Vérifier la configuration email dans .env
# 2. Vérifier que la base de données fonctionne
# 3. Vérifier les permissions des fichiers
```

### **Si la commande artisan ne fonctionne pas :**
```bash
# 1. Vérifier que Laravel est bien installé
# 2. Vérifier que les modèles existent
# 3. Vérifier la configuration de la base de données
```

## 📊 **Fonctionnalités Disponibles**

### **1. 🎨 Gestion du Stock par Couleur**
- Affichage du stock pour chaque couleur
- Mise à jour en temps réel
- Alertes visuelles (rouge = rupture, jaune = faible, vert = normal)

### **2. 📧 Notifications Automatiques**
- Email aux admins et vendeurs
- Messages admin dans l'interface
- Logs dédiés au stock

### **3. 📈 Statistiques et Rapports**
- Vue d'ensemble des stocks
- Recherche par couleur
- Export des données

### **4. ⚡ Vérification Automatique**
- Commande artisan pour vérifier les stocks
- Détection des ruptures et stocks faibles
- Notifications immédiates

## 🎯 **Résumé des Tests à Effectuer**

1. **✅ Accès à l'interface** : `/admin/color-stock`
2. **✅ Modification du stock** : Mettre une couleur à 0
3. **✅ Création de notifications** : Vérifier les messages admin
4. **✅ Affichage des alertes** : Sur le dashboard
5. **✅ Commande artisan** : `php artisan stock:check-critical`
6. **✅ Responsivité** : Tester sur différents écrans

## 🚀 **Prochaines Étapes**

Une fois que vous avez testé le système :

1. **Configurer les emails** si nécessaire
2. **Personnaliser les seuils** d'alerte
3. **Automatiser les vérifications** avec un cron job
4. **Ajouter des notifications push** si souhaité
5. **Créer des rapports** personnalisés

---

## 💡 **Conseils d'Utilisation**

- **Testez d'abord** avec des produits de test
- **Vérifiez les logs** pour le débogage
- **Utilisez la commande artisan** pour les tests
- **Surveillez le dashboard** pour les alertes
- **Testez sur mobile** pour la responsivité

**Le système de notifications de stock est maintenant à votre disposition !** 🎉
