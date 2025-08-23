# Guide de Test : Produits avec Données Complètes

## 🎯 Objectif

Tester le formulaire de création de commande avec des produits qui ont des données complètes de stock par couleur et de tailles.

## 📦 Produits de Test Créés

### **1. T-Shirt Premium Test (ID: 38)**
- **Type** : Produit normal avec tailles
- **Couleurs** : 4 couleurs avec stock détaillé
  - 🔴 **Rouge** : 25 unités en stock
  - 🔵 **Bleu** : 18 unités en stock  
  - 🟢 **Vert** : 0 unité (en rupture de stock)
  - ⚫ **Noir** : 12 unités en stock
- **Tailles** : XS, S, M, L, XL, XXL
- **Stock Total** : 55 unités
- **Prix** : Admin 80 DH, Vente 120 DH

### **2. Bracelet Élégant Test (ID: 39)**
- **Type** : Accessoire (pas de tailles)
- **Couleurs** : 3 couleurs avec stock détaillé
  - 🟡 **Or** : 15 unités en stock
  - 🟤 **Argent** : 8 unités en stock
  - 🟤 **Cuir Marron** : 22 unités en stock
- **Tailles** : Aucune (accessoire)
- **Stock Total** : 45 unités
- **Prix** : Admin 45 DH, Vente 75 DH

## 🧪 Étapes de Test

### **Étape 1 : Préparation**
1. **Démarrer le serveur** (déjà fait)
2. **Se connecter en tant que vendeur**
   - Email : `Yassine.en37@gmail.com` (ou autre vendeur existant)
   - Ces produits sont assignés au vendeur "Yassine Alahy"

### **Étape 2 : Test du T-Shirt Premium**

#### **A. Sélection du Produit**
1. Aller sur `http://127.0.0.1:8000/seller/orders/create`
2. Sélectionner "T-Shirt Premium Test" dans le select des produits
3. **Vérifier** :
   - ✅ L'image s'affiche
   - ✅ Le prix d'achat s'affiche (80 DH)
   - ✅ Le prix de vente s'affiche (120 DH)

#### **B. Test des Couleurs**
1. **Vérifier le select des couleurs** :
   - ✅ 4 couleurs disponibles
   - ✅ **Rouge** : 25 unités (disponible)
   - ✅ **Bleu** : 18 unités (disponible)
   - ❌ **Vert** : 0 unité (grisé, "Rupture de stock")
   - ✅ **Noir** : 12 unités (disponible)

2. **Sélectionner une couleur** :
   - Choisir "Rouge" ou "Bleu" ou "Noir"
   - **Ne pas choisir "Vert"** (en rupture)

#### **C. Test des Tailles**
1. **Vérifier le select des tailles** :
   - ✅ Section tailles active (non grisée)
   - ✅ 6 tailles disponibles : XS, S, M, L, XL, XXL
   - ✅ Pas de message "Accessoire"

2. **Sélectionner une taille** :
   - Choisir "M" (ou autre taille)

### **Étape 3 : Test du Bracelet Élégant**

#### **A. Sélection du Produit**
1. **Ajouter un nouveau produit** (bouton "+")
2. Sélectionner "Bracelet Élégant Test"
3. **Vérifier** :
   - ✅ L'image s'affiche
   - ✅ Le prix d'achat s'affiche (45 DH)
   - ✅ Le prix de vente s'affiche (75 DH)

#### **B. Test des Couleurs**
1. **Vérifier le select des couleurs** :
   - ✅ 3 couleurs disponibles
   - ✅ **Or** : 15 unités (disponible)
   - ✅ **Argent** : 8 unités (disponible)
   - ✅ **Cuir Marron** : 22 unités (disponible)

2. **Sélectionner une couleur** :
   - Choisir "Or" ou "Argent" ou "Cuir Marron"

#### **C. Test des Tailles (Accessoire)**
1. **Vérifier le select des tailles** :
   - 🔒 Section tailles désactivée (grisée)
   - ✅ Message : "Pas de tailles pour les accessoires"
   - ✅ Note explicative : "ℹ️ Accessoire - Pas de tailles requises"
   - ✅ Impossible de sélectionner une taille

## 🔍 Points de Vérification Techniques

### **Console JavaScript**
Ouvrir la console du navigateur et vérifier les logs :
```
🎨 Gestion des couleurs:
  - Couleurs raw: [{"name":"Rouge","hex":"#FF0000"},...]
  - Stock couleurs raw: [{"name":"Rouge","quantity":25},...]
  - Couleurs parsées: [Object, Object, Object, Object]
  - Stock couleurs parsé: [Object, Object, Object, Object]
🔍 Produit accessoire: NON
🎨 Couleurs disponibles: Rouge, Bleu, Vert, Noir
📏 Produit avec tailles - Section tailles activée
```

### **Données Transmises**
Vérifier dans l'onglet Network que les données sont bien formatées :
- `data-couleurs` : JSON valide avec nom et hex
- `data-stock-couleurs` : JSON valide avec nom et quantité
- `data-tailles` : JSON valide avec array de tailles

## 🚨 Scénarios de Test

### **Scénario 1 : Commande Complète T-Shirt**
1. **Informations client** :
   - Nom : "Client Test T-Shirt"
   - Ville : "Casablanca"
   - Adresse : "123 Rue Test"
   - Téléphone : "0612345678"

2. **Produit** :
   - Produit : "T-Shirt Premium Test"
   - Couleur : "Rouge" (25 unités disponibles)
   - Taille : "M"
   - Quantité : 2
   - Prix de vente : 150 DH

3. **Validation** :
   - ✅ Couleur obligatoire (remplie)
   - ✅ Taille obligatoire (remplie)
   - ✅ Quantité ≤ stock disponible (2 ≤ 25)

### **Scénario 2 : Commande Accessoire Bracelet**
1. **Informations client** (mêmes données)

2. **Produit** :
   - Produit : "Bracelet Élégant Test"
   - Couleur : "Or" (15 unités disponibles)
   - Taille : Pas de sélection (désactivée)
   - Quantité : 3
   - Prix de vente : 80 DH

3. **Validation** :
   - ✅ Couleur obligatoire (remplie)
   - ✅ Taille optionnelle (accessoire)
   - ✅ Quantité ≤ stock disponible (3 ≤ 15)

### **Scénario 3 : Test de Rupture de Stock**
1. **Essayer de commander le T-Shirt en Vert** :
   - Produit : "T-Shirt Premium Test"
   - Couleur : "Vert" (0 unité - grisé)
   - **Résultat attendu** : Impossible de sélectionner

## 📊 Résultats Attendus

### **Succès**
- ✅ Toutes les couleurs s'affichent avec leur stock
- ✅ Les couleurs en rupture sont grisées
- ✅ Les tailles s'affichent pour le T-Shirt
- ✅ La section tailles est désactivée pour le bracelet
- ✅ La validation fonctionne selon le type de produit
- ✅ Les commandes sont créées avec succès

### **Échec**
- ❌ Les couleurs ne s'affichent pas
- ❌ Les tailles ne s'affichent pas
- ❌ Les accessoires n'ont pas leur section tailles désactivée
- ❌ Erreurs JavaScript dans la console
- ❌ Validation qui échoue

## 🔧 Debug en Cas de Problème

### **Vérifier les Données en Base**
```bash
php artisan tinker
>>> App\Models\Product::find(38)->toArray()
>>> App\Models\Product::find(39)->toArray()
```

### **Vérifier la Relation Vendeur-Produits**
```bash
php artisan tinker
>>> $user = App\Models\User::where('role', 'seller')->first()
>>> $user->assignedProducts()->get()->pluck('name', 'id')
```

### **Vérifier les Logs**
```bash
tail -f storage/logs/laravel.log
```

## 📝 Notes Importantes

1. **Stock Réel** : Les produits ont maintenant des données de stock réelles
2. **Couleurs en Rupture** : Le "Vert" du T-Shirt est en rupture (0 unité)
3. **Accessoire** : Le bracelet n'a pas de tailles (section désactivée)
4. **Prix** : Les prix admin et vente sont configurés
5. **Images** : Les images pointent vers des chemins fictifs (peuvent être 404)

## 🎯 Critères de Réussite

Le test est réussi si :
- [ ] Toutes les couleurs s'affichent avec leur stock
- [ ] Les couleurs en rupture sont grisées
- [ ] Le T-Shirt a ses 6 tailles disponibles
- [ ] Le bracelet a sa section tailles désactivée
- [ ] La validation fonctionne correctement
- [ ] Les commandes sont créées sans erreur
