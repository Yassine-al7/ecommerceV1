# Guide de Test : Formulaire de Création de Commande

## 🎯 Objectif du Test

Vérifier que le formulaire de création de commande fonctionne correctement avec :
- ✅ Affichage des couleurs
- ✅ Affichage des tailles
- ✅ Gestion des accessoires
- ✅ Validation des données

## 🧪 Étapes de Test

### **1. Préparation du Test**

1. **Démarrer le serveur Laravel**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

2. **Se connecter en tant que vendeur**
   - Aller sur `http://127.0.0.1:8000/login`
   - Se connecter avec un compte vendeur

3. **Accéder au formulaire de commande**
   - Aller sur `http://127.0.0.1:8000/seller/orders/create`

### **2. Test de Base**

#### **A. Vérification de l'Interface**
- [ ] Le formulaire se charge sans erreur
- [ ] Les champs client sont présents (nom, ville, adresse, téléphone)
- [ ] La section produits est visible
- [ ] Le bouton "Ajouter le premier produit" est présent

#### **B. Vérification des Données**
- [ ] Le select des produits contient des options
- [ ] Chaque produit a des données (nom, image, prix)

### **3. Test de Sélection de Produit**

#### **A. Produit avec Tailles (ex: DJELLABA)**
1. **Sélectionner le produit**
   - Choisir "DJELLABA" dans le select
   - Vérifier que l'image s'affiche
   - Vérifier que le prix d'achat s'affiche

2. **Vérifier les Couleurs**
   - [ ] Le select des couleurs se remplit
   - [ ] Les couleurs disponibles sont affichées
   - [ ] Les couleurs en rupture sont grisées

3. **Vérifier les Tailles**
   - [ ] Le select des tailles se remplit
   - [ ] Les tailles XS, S, M, L, XL, XXL sont disponibles
   - [ ] La section tailles est active (non grisée)

#### **B. Produit Accessoire (ex: Kits)**
1. **Sélectionner le produit**
   - Choisir "Kits" dans le select
   - Vérifier que l'image s'affiche
   - Vérifier que le prix d'achat s'affiche

2. **Vérifier les Couleurs**
   - [ ] Le select des couleurs se remplit
   - [ ] Les couleurs "Rouge" et "tk loun" sont disponibles
   - [ ] Les couleurs ont des valeurs hexadécimales

3. **Vérifier les Tailles**
   - [ ] Le select des tailles affiche "Pas de tailles pour les accessoires"
   - [ ] La section tailles est grisée (désactivée)
   - [ ] Le message "ℹ️ Accessoire - Pas de tailles requises" s'affiche

### **4. Test de Validation**

#### **A. Test de Validation des Couleurs**
1. **Sélectionner un produit**
2. **Ne pas sélectionner de couleur**
3. **Essayer de soumettre le formulaire**
4. **Vérifier** : Message d'erreur "La couleur est obligatoire"

#### **B. Test de Validation des Tailles**
1. **Sélectionner un produit avec tailles**
2. **Ne pas sélectionner de taille**
3. **Essayer de soumettre le formulaire**
4. **Vérifier** : Message d'erreur "La taille est obligatoire"

#### **C. Test de Validation des Accessoires**
1. **Sélectionner un accessoire**
2. **Ne pas sélectionner de taille**
3. **Soumettre le formulaire**
4. **Vérifier** : Le formulaire se soumet sans erreur (taille optionnelle)

### **5. Test de Création de Commande**

#### **A. Commande Complète**
1. **Remplir les informations client**
   - Nom : "Test Client"
   - Ville : "Casablanca"
   - Adresse : "123 Rue Test"
   - Téléphone : "0612345678"

2. **Ajouter un produit**
   - Produit : "DJELLABA"
   - Couleur : "Rouge" (ou autre couleur disponible)
   - Taille : "M"
   - Quantité : 1
   - Prix de vente : 200

3. **Soumettre la commande**
   - [ ] La commande est créée avec succès
   - [ ] Redirection vers la liste des commandes
   - [ ] Message de succès affiché

#### **B. Commande avec Accessoire**
1. **Remplir les informations client** (mêmes données)

2. **Ajouter un accessoire**
   - Produit : "Kits"
   - Couleur : "Rouge"
   - Taille : Pas de sélection (désactivée)
   - Quantité : 2
   - Prix de vente : 250

3. **Soumettre la commande**
   - [ ] La commande est créée avec succès
   - [ ] La taille est enregistrée comme "N/A"

## 🔍 Points de Vérification Techniques

### **Console JavaScript**
Ouvrir la console du navigateur et vérifier :
- [ ] Pas d'erreurs JavaScript
- [ ] Les logs de debug s'affichent
- [ ] Les données des produits sont parsées correctement

### **Réseau (Network)**
Vérifier dans l'onglet Network :
- [ ] La requête vers `/seller/orders/create` retourne 200
- [ ] Les données JSON sont bien formatées
- [ ] Pas d'erreurs 500 ou 404

### **Base de Données**
Après création d'une commande, vérifier :
- [ ] La commande est enregistrée dans la table `commandes`
- [ ] Les détails produits sont enregistrés avec les bonnes valeurs
- [ ] Les couleurs et tailles sont correctement sauvegardées

## 🚨 Problèmes Courants et Solutions

### **Problème 1 : Aucune couleur ne s'affiche**
**Cause possible** : Champ `couleur` NULL dans la base
**Solution** : Le système crée automatiquement "Couleur unique"

### **Problème 2 : Aucune taille ne s'affiche**
**Cause possible** : Champ `tailles` NULL ou vide
**Solution** : Le système détecte automatiquement les accessoires

### **Problème 3 : Erreur JavaScript**
**Cause possible** : Données JSON mal formatées
**Solution** : Vérifier la console et les logs de debug

### **Problème 4 : Validation échoue**
**Cause possible** : Règles de validation trop strictes
**Solution** : Vérifier les messages d'erreur et ajuster si nécessaire

## 📊 Résultats Attendus

### **Succès**
- ✅ Toutes les couleurs s'affichent correctement
- ✅ Les tailles s'affichent pour les produits normaux
- ✅ Les accessoires ont leur section tailles désactivée
- ✅ La validation fonctionne selon le type de produit
- ✅ Les commandes sont créées avec succès

### **Échec**
- ❌ Les couleurs ne s'affichent pas
- ❌ Les tailles ne s'affichent pas
- ❌ Erreurs JavaScript dans la console
- ❌ Validation qui échoue de manière inattendue
- ❌ Erreurs 500 lors de la soumission

## 🔧 Commandes de Debug

### **Vérifier les Logs Laravel**
```bash
tail -f storage/logs/laravel.log
```

### **Vérifier la Base de Données**
```bash
php artisan tinker
>>> App\Models\Product::first()->toArray()
```

### **Vider le Cache**
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## 📝 Notes Importantes

1. **Données de Test** : Utiliser les produits existants (DJELLABA, Kits)
2. **Rôles** : S'assurer d'être connecté en tant que vendeur
3. **Permissions** : Vérifier que l'utilisateur a accès aux produits
4. **Session** : Vérifier que la session est active
5. **Cache** : Vider le cache si les modifications ne s'affichent pas

## 🎯 Critères de Réussite

Le test est réussi si :
- [ ] Toutes les couleurs s'affichent dans le select
- [ ] Toutes les tailles s'affichent pour les produits normaux
- [ ] Les accessoires ont leur section tailles désactivée
- [ ] La validation fonctionne correctement
- [ ] Les commandes sont créées sans erreur
- [ ] Aucune erreur JavaScript dans la console
