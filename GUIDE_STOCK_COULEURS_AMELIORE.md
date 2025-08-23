# Guide : Améliorations du Système de Gestion des Couleurs et du Stock

## Vue d'Ensemble

Ce guide décrit les améliorations apportées au système de gestion des produits pour une meilleure gestion des couleurs et du stock par couleur.

## 🎯 Fonctionnalités Implémentées

### 1. **Gestion du Stock par Couleur**
- Le stock total d'un produit est maintenant la somme des quantités de chaque couleur
- Chaque couleur peut avoir sa propre quantité en stock
- Calcul automatique du stock total

### 2. **Interface Admin Améliorée**
- Formulaire de création/édition avec gestion du stock par couleur
- Stock total calculé automatiquement
- Validation des stocks par couleur

### 3. **Interface Vendeur Améliorée**
- Sélection de couleur obligatoire lors de la création de commande
- Vérification de la disponibilité des couleurs
- Couleurs indisponibles grisées automatiquement

## 🔧 Modifications Techniques

### Modèle Product (`app/Models/Product.php`)

#### Nouvelles Méthodes Ajoutées

```php
/**
 * Calculer le stock total en additionnant toutes les couleurs
 */
public function getTotalStockAttribute()
{
    if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
        return $this->quantite_stock ?? 0;
    }

    $total = 0;
    foreach ($this->stock_couleurs as $colorStock) {
        if (is_array($colorStock) && isset($colorStock['quantity'])) {
            $total += (int) $colorStock['quantity'];
        } elseif (is_numeric($colorStock)) {
            $total += (int) $colorStock;
        }
    }
    return $total;
}

/**
 * Obtenir le stock disponible pour une couleur spécifique
 */
public function getStockForColor($colorName)
{
    if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
        return 0;
    }

    foreach ($this->stock_couleurs as $colorStock) {
        if (is_array($colorStock) && isset($colorStock['name']) && $colorStock['name'] === $colorName) {
            return (int) ($colorStock['quantity'] ?? 0);
        }
    }
    return 0;
}

/**
 * Vérifier si une couleur est en stock faible (moins de 5 unités)
 */
public function isColorLowStock($colorName)
{
    $stock = $this->getStockForColor($colorName);
    return $stock > 0 && $stock < 5;
}

/**
 * Vérifier si une couleur est en rupture de stock
 */
public function isColorOutOfStock($colorName)
{
    return $this->getStockForColor($colorName) <= 0;
}

/**
 * Obtenir toutes les couleurs avec leur stock
 */
public function getColorsWithStock()
{
    if (!$this->couleur || !is_array($this->couleur)) {
        return [];
    }

    $colorsWithStock = [];
    foreach ($this->couleur as $color) {
        $colorName = is_array($color) ? $color['name'] : $color;
        $stock = $this->getStockForColor($colorName);
        
        $colorsWithStock[] = [
            'name' => $colorName,
            'hex' => is_array($color) ? ($color['hex'] ?? null) : null,
            'stock' => $stock,
            'is_low_stock' => $this->isColorLowStock($colorName),
            'is_out_of_stock' => $this->isColorOutOfStock($colorName),
            'is_available' => $stock > 0
        ];
    }

    return $colorsWithStock;
}

/**
 * Mettre à jour le stock d'une couleur
 */
public function updateColorStock($colorName, $quantity)
{
    if (!$this->stock_couleurs) {
        $this->stock_couleurs = [];
    }

    $found = false;
    foreach ($this->stock_couleurs as &$colorStock) {
        if (is_array($colorStock) && isset($colorStock['name']) && $colorStock['name'] === $colorName) {
            $colorStock['quantity'] = $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $this->stock_couleurs[] = [
            'name' => $colorName,
            'quantity' => $quantity
        ];
    }

    // Mettre à jour le stock total
    $this->quantite_stock = $this->getTotalStockAttribute();
    $this->save();
}
```

### Contrôleur Admin (`app/Http/Controllers/Admin/ProductController.php`)

#### Méthode `store()` Modifiée

```php
// Traiter les couleurs avec leurs valeurs hexadécimales et stocks
$couleurs = $request->input('couleurs', []);
$couleursHex = $request->input('couleurs_hex', []);
$stockCouleurs = [];

// Créer un mapping couleur-hex-stock pour la sauvegarde
$couleursWithHex = [];
foreach ($couleurs as $index => $couleur) {
    $hex = $couleursHex[$index] ?? null;
    $stock = $request->input("stock_couleur_{$index}", 0);
    
    if ($hex) {
        $couleursWithHex[] = [
            'name' => $couleur,
            'hex' => $hex
        ];
    } else {
        $couleursWithHex[] = $couleur;
    }
    
    // Stocker le stock par couleur
    $stockCouleurs[] = [
        'name' => $couleur,
        'quantity' => (int) $stock
    ];
}

// Convertir les couleurs en JSON (pour stockage en base)
$data['couleur'] = json_encode($couleursWithHex);
$data['stock_couleurs'] = json_encode($stockCouleurs);
```

### Contrôleur Vendeur (`app/Http/Controllers/Seller/OrderController.php`)

#### Méthode `create()` Modifiée

```php
// Produits assignés au vendeur avec plus d'informations
$products = auth()->user()->assignedProducts()
    ->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin', 'produits.couleur', 'produits.stock_couleurs')
    ->get();
```

#### Méthode `store()` Modifiée

```php
// Validation incluant la couleur
$data = $request->validate([
    'nom_client' => 'required|string',
    'ville' => 'required|string',
    'adresse_client' => 'required|string',
    'numero_telephone_client' => 'required|string',
    'products' => 'required|array|min:1',
    'products.*.product_id' => 'required|exists:produits,id',
    'products.*.couleur_produit' => 'required|string', // NOUVEAU
    'products.*.taille_produit' => 'required|string',
    'products.*.quantite_produit' => 'required|integer|min:1',
    'products.*.prix_vente_client' => 'required|numeric|min:0.01',
    'commentaire' => 'nullable|string',
]);

// Vérification de la disponibilité de la couleur
$couleurSelectionnee = $productData['couleur_produit'];
$couleurDisponible = false;
$stockCouleur = 0;

foreach ($stockCouleurs as $stockColor) {
    if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $couleurSelectionnee) {
        $stockCouleur = (int) ($stockColor['quantity'] ?? 0);
        $couleurDisponible = $stockCouleur > 0;
        break;
    }
}

if (!$couleurDisponible) {
    return back()->withErrors(['couleur_produit' => "La couleur '{$couleurSelectionnee}' n'est pas disponible pour le produit '{$product->name}' ou est en rupture de stock"])->withInput();
}

// Vérification de la quantité disponible
if ($stockCouleur < (int) $productData['quantite_produit']) {
    return back()->withErrors(['quantite_produit' => "La quantité demandée ({$productData['quantite_produit']}) dépasse le stock disponible ({$stockCouleur}) pour la couleur '{$couleurSelectionnee}'"])->withInput();
}
```

## 🎨 Interface Utilisateur

### Vue de Création de Produits (`resources/views/admin/products/create.blade.php`)

#### Stock Total Automatique

```html
<!-- Quantité en Stock Total (Calculée automatiquement) -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en Stock Total</label>
    <input type="number" id="stockTotal" value="0" min="0" readonly
           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-semibold">
    <p class="text-xs text-gray-500 mt-1">💡 Calculé automatiquement : somme des stocks de toutes les couleurs</p>
    <input type="hidden" name="quantite_stock" id="stockTotalHidden" value="0">
</div>
```

#### JavaScript pour le Calcul Automatique

```javascript
// Fonction pour calculer le stock total
function calculateTotalStock() {
    let total = 0;
    
    // Calculer le stock des couleurs prédéfinies
    const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]');
    predefinedStockInputs.forEach(input => {
        const checkbox = input.closest('.flex').querySelector('input[name="couleurs[]"]');
        if (checkbox && checkbox.checked) {
            total += parseInt(input.value) || 0;
        }
    });
    
    // Calculer le stock des couleurs personnalisées
    const customStockInputs = document.querySelectorAll('input[name^="stock_couleur_custom_"]');
    customStockInputs.forEach(input => {
        total += parseInt(input.value) || 0;
    });
    
    // Mettre à jour l'affichage
    const stockTotal = document.getElementById('stockTotal');
    const stockTotalHidden = document.getElementById('stockTotalHidden');
    
    if (stockTotal) stockTotal.value = total;
    if (stockTotalHidden) stockTotalHidden.value = total;
    
    console.log('Stock total calculé:', total);
    return total;
}
```

### Vue de Création de Commandes (`resources/views/seller/order_form.blade.php`)

#### Sélection de Couleur

```html
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur *</label>
    <select name="products[0][couleur_produit]" class="color-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        <option value="">Sélectionnez d'abord un produit</option>
    </select>
</div>
```

#### Données des Produits Enrichies

```html
@foreach(($products ?? []) as $p)
    <option value="{{ $p->id }}"
            data-image="{{ $p->image }}"
            data-prix-admin="{{ optional($p->pivot)->prix_vente ?? $p->prix_admin }}"
            data-tailles="{{ $p->tailles ? json_encode($p->tailles) : '[]' }}"
            data-couleurs="{{ $p->couleur ? json_encode($p->couleur) : '[]' }}"
            data-stock-couleurs="{{ $p->stock_couleurs ? json_encode($p->stock_couleurs) : '[]' }}">
        {{ $p->name }}
    </option>
@endforeach
```

## 📊 Structure des Données

### Stock par Couleur

```json
{
  "stock_couleurs": [
    {
      "name": "Rouge",
      "quantity": 15
    },
    {
      "name": "Bleu",
      "quantity": 8
    },
    {
      "name": "Noir",
      "quantity": 0
    }
  ]
}
```

### Couleurs avec Métadonnées

```json
{
  "couleur": [
    {
      "name": "Rouge",
      "hex": "#ff0000"
    },
    {
      "name": "Bleu",
      "hex": "#0000ff"
    },
    {
      "name": "Noir",
      "hex": "#000000"
    }
  ]
}
```

## 🔍 Validation et Vérifications

### Côté Admin
- Stock total calculé automatiquement
- Validation des quantités par couleur
- Gestion des couleurs personnalisées

### Côté Vendeur
- Vérification de la disponibilité des couleurs
- Vérification des quantités disponibles
- Couleurs indisponibles grisées
- Validation de la couleur sélectionnée

## 🚀 Avantages du Système

1. **Gestion Granulaire** : Stock géré par couleur et non globalement
2. **Précision** : Quantités exactes disponibles pour chaque couleur
3. **Automatisation** : Calcul automatique du stock total
4. **Validation** : Vérifications côté serveur et client
5. **Expérience Utilisateur** : Interface intuitive avec feedback en temps réel
6. **Traçabilité** : Suivi précis des stocks par couleur

## 🧪 Tests Recommandés

### Test de Création de Produit
1. Créer un produit avec plusieurs couleurs
2. Définir des quantités différentes pour chaque couleur
3. Vérifier que le stock total est calculé automatiquement
4. Sauvegarder et vérifier en base

### Test de Création de Commande
1. Créer une commande avec un produit
2. Sélectionner une couleur disponible
3. Vérifier que la validation fonctionne
4. Tester avec une couleur indisponible (doit échouer)

### Test de Gestion des Stocks
1. Modifier les quantités de couleurs existantes
2. Vérifier que le stock total se met à jour
3. Tester les alertes de stock faible
4. Vérifier la cohérence des données

## 🔧 Maintenance et Évolutions

### Prochaines Étapes Suggérées
1. **Alertes de Stock** : Notifications automatiques pour stock faible
2. **Historique des Mouvements** : Traçabilité des changements de stock
3. **Gestion des Fournisseurs** : Lien entre couleurs et fournisseurs
4. **Rapports Avancés** : Statistiques par couleur et par vendeur
5. **API REST** : Endpoints pour la gestion des stocks

### Optimisations Possibles
1. **Cache Redis** : Mise en cache des stocks pour améliorer les performances
2. **Indexation** : Optimisation des requêtes de base de données
3. **Validation Asynchrone** : Vérification en temps réel de la disponibilité
4. **Interface Mobile** : Adaptation pour les appareils mobiles

## 📝 Notes Importantes

- Le système maintient la compatibilité avec l'ancien format de données
- Les migrations existantes ne sont pas affectées
- La validation côté serveur est renforcée
- Les erreurs sont gérées de manière user-friendly
- Le système est extensible pour de futures fonctionnalités
