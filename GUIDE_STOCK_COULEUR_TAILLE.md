# Guide d'utilisation du système de gestion du stock par couleur et taille

## 🎯 Vue d'ensemble

Ce système permet de gérer le stock des produits de manière granulaire, en distinguant :
- **Produits avec tailles** : Vêtements, chaussures, etc. qui nécessitent une gestion par couleur ET taille
- **Accessoires** : Produits sans tailles qui nécessitent seulement une gestion par couleur

## 🏗️ Architecture du système

### 1. Modèle Product amélioré
- Méthode `isAccessory()` : Détecte automatiquement si un produit est un accessoire
- Méthode `isColorAndSizeAvailable()` : Vérifie la disponibilité d'une couleur et taille
- Méthode `getAvailableSizesForColor()` : Obtient les tailles disponibles pour une couleur
- Méthodes de gestion du stock : `decreaseColorStock()`, `increaseColorStock()`, etc.

### 2. Contrôleur ColorStockController
- Gestion complète du stock par couleur et taille
- API pour vérification en temps réel
- Interface d'administration dédiée

### 3. Vues d'administration
- **Index** : Liste de tous les produits avec leur stock
- **Show** : Détail du stock d'un produit
- **Edit** : Modification du stock par couleur et taille

## 👨‍💼 Côté Admin - Gestion des produits

### Ajout d'un nouveau produit

1. **Accéder au formulaire de création**
   ```
   Admin → Produits → Ajouter un produit
   ```

2. **Remplir les informations de base**
   - Nom du produit
   - Catégorie (détermine automatiquement si c'est un accessoire)
   - Prix admin et prix de vente
   - Image du produit

3. **Configurer les couleurs**
   - Sélectionner des couleurs prédéfinies ou ajouter des couleurs personnalisées
   - Pour chaque couleur, spécifier la quantité en stock
   - Le système calcule automatiquement le stock total

4. **Configurer les tailles** (si ce n'est pas un accessoire)
   - Sélectionner les tailles disponibles (XS, S, M, L, XL, XXL, etc.)
   - Les tailles sont automatiquement associées à toutes les couleurs

### Gestion du stock existant

1. **Accéder à la gestion du stock**
   ```
   Admin → Stock par couleur → Sélectionner un produit
   ```

2. **Modifier le stock par couleur**
   - Ajuster les quantités pour chaque couleur
   - Ajouter/supprimer des couleurs
   - Modifier les tailles disponibles

3. **Actions rapides**
   - Augmenter/diminuer le stock d'une couleur
   - Exporter les données de stock
   - Voir les alertes de stock faible/rupture

## 🛍️ Côté Vendeur - Gestion des commandes

### Ajout d'un produit à une commande

1. **Sélectionner le produit**
   - Le système affiche automatiquement les couleurs disponibles
   - Pour les produits avec tailles, les tailles sont proposées selon la couleur

2. **Vérification automatique**
   - Le système vérifie si la couleur existe
   - Le système vérifie si la taille est disponible pour cette couleur
   - Le système vérifie si le stock est suffisant

3. **Gestion des erreurs**
   - Si la couleur n'existe pas : erreur bloquante
   - Si la taille n'est pas disponible : erreur bloquante
   - Si le stock est insuffisant : avertissement (commande autorisée)

### Exemple concret

**Commande d'une Djellaba Rouge Taille M :**
1. ✅ Vérification que la couleur "Rouge" existe
2. ✅ Vérification que la taille "M" est disponible pour "Rouge"
3. ✅ Vérification que le stock Rouge est suffisant
4. ✅ Produit ajouté à la commande

**Commande d'un Turban Or (accessoire) :**
1. ✅ Vérification que la couleur "Or" existe
2. ✅ Pas de vérification de taille (accessoire)
3. ✅ Vérification que le stock Or est suffisant
4. ✅ Produit ajouté à la commande

## 🔧 API et intégrations

### Endpoints disponibles

```php
// Vérifier la disponibilité d'une couleur et taille
POST /admin/color-stock/check-availability
{
    "product_id": 1,
    "color": "Rouge",
    "size": "M"
}

// Obtenir les tailles disponibles pour une couleur
POST /admin/color-stock/get-sizes
{
    "product_id": 1,
    "color": "Rouge"
}

// Augmenter le stock d'une couleur
POST /admin/color-stock/increase
{
    "product_id": 1,
    "color": "Rouge",
    "quantity": 10
}

// Diminuer le stock d'une couleur
POST /admin/color-stock/decrease
{
    "product_id": 1,
    "color": "Rouge",
    "quantity": 5
}
```

### Réponses API

```json
// Vérification de disponibilité
{
    "available": true,
    "stock_quantity": 95,
    "available_sizes": ["XS", "S", "M", "L", "XL", "XXL"],
    "is_accessory": false
}

// Augmentation de stock
{
    "success": true,
    "new_stock": 105,
    "total_stock": 450
}
```

## 📊 Gestion des alertes et notifications

### Types d'alertes

1. **Stock faible** : Moins de 5 unités
   - Affichage en jaune dans l'interface
   - Notification dans le tableau de bord

2. **Rupture de stock** : 0 unité
   - Affichage en rouge dans l'interface
   - Notification urgente

3. **Stock normal** : 5 unités ou plus
   - Affichage en vert dans l'interface

### Configuration des seuils

Les seuils sont configurables dans le modèle Product :
```php
// Seuil de stock faible (par défaut : 5)
public function isColorLowStock($colorName)
{
    $stock = $this->getStockForColor($colorName);
    return $stock > 0 && $stock < 5; // Modifier cette valeur si nécessaire
}
```

## 🧪 Tests et validation

### Fichier de test

Utilisez le fichier `test_stock_couleur_taille.php` pour tester le système :

```bash
php test_stock_couleur_taille.php
```

Ce fichier teste :
- Création de produits avec et sans tailles
- Configuration du stock par couleur
- Vérification de disponibilité
- Mise à jour du stock
- Détection des alertes

### Tests manuels

1. **Créer un produit test**
   - Ajouter plusieurs couleurs avec différents stocks
   - Configurer des tailles variées

2. **Tester les commandes**
   - Essayer d'ajouter une couleur inexistante
   - Essayer d'ajouter une taille non disponible
   - Vérifier les messages d'erreur

3. **Tester la gestion du stock**
   - Augmenter/diminuer le stock
   - Vérifier le calcul automatique du stock total

## 🚀 Fonctionnalités avancées

### Export des données

- **Export individuel** : Depuis la page de détail d'un produit
- **Export global** : Depuis la liste des produits
- **Format** : JSON avec toutes les informations de stock

### Filtres et recherche

- **Par catégorie** : Filtrer les produits par type
- **Par statut** : Stock normal, faible, rupture
- **Par nom** : Recherche textuelle
- **Par type** : Produits avec tailles vs accessoires

### Statistiques en temps réel

- Nombre total de produits
- Nombre total de couleurs
- Couleurs en rupture de stock
- Couleurs avec stock faible

## 🔒 Sécurité et permissions

### Accès admin

- **Gestion complète** : Création, modification, suppression
- **API complète** : Toutes les opérations de stock
- **Export des données** : Accès à toutes les informations

### Accès vendeur

- **Lecture seule** : Consultation du stock disponible
- **Validation** : Vérification lors des commandes
- **Pas de modification** : Impossible de changer le stock

## 📝 Maintenance et support

### Logs et monitoring

Le système génère des logs détaillés pour :
- Modifications de stock
- Vérifications de disponibilité
- Erreurs de validation
- Actions des utilisateurs

### Sauvegarde

- **Base de données** : Sauvegarde automatique des modifications
- **Historique** : Traçabilité des changements de stock
- **Rollback** : Possibilité de revenir en arrière

### Support technique

En cas de problème :
1. Vérifier les logs Laravel
2. Tester avec le fichier de test
3. Vérifier la configuration de la base de données
4. Contacter l'équipe technique

## 🎉 Conclusion

Ce système offre une gestion complète et flexible du stock par couleur et taille, avec :
- ✅ Interface intuitive pour les admins
- ✅ Validation automatique pour les vendeurs
- ✅ API robuste pour les intégrations
- ✅ Alertes et notifications en temps réel
- ✅ Export et reporting complet

Le système est prêt pour la production et peut être étendu selon les besoins futurs.
