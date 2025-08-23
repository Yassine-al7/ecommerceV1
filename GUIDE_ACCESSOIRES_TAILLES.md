# Guide : Gestion des Accessoires et des Tailles

## Vue de Création de Produits (`/admin/products/create`)

### Fonctionnalité Implémentée

Le système détecte automatiquement si un produit est un accessoire et masque/désactive la section des tailles en conséquence.

### Détection des Accessoires

**Logique de détection :**
- Une catégorie est considérée comme "accessoire" si son nom contient le mot "accessoire" (insensible à la casse)
- Exemples de catégories accessoires :
  - "Accessoire"
  - "Accessoires" 
  - "Bijoux et Accessoires"
  - "Accessoires de Mode"

### Comportement JavaScript

#### 1. Au chargement de la page
- `toggleTaillesSection()` est appelée automatiquement
- Vérifie la catégorie sélectionnée par défaut
- Applique la logique appropriée

#### 2. Si la catégorie est un accessoire
```javascript
// Masquer la section des tailles
taillesSection.style.display = 'none';
taillesRequired.style.display = 'none';

// Désactiver toutes les checkboxes de tailles
tailleCheckboxes.forEach(checkbox => {
    checkbox.checked = false;
    checkbox.removeAttribute('name');  // Évite l'envoi des données
    checkbox.disabled = true;
});

// Vider et désactiver les tailles personnalisées
customSizesContainer.innerHTML = '';
customSizeInput.value = '';
customSizeInput.disabled = true;
addSizeButton.disabled = true;
```

#### 3. Si la catégorie n'est pas un accessoire
```javascript
// Afficher la section des tailles
taillesSection.style.display = 'block';
taillesRequired.style.display = 'inline';

// Réactiver toutes les checkboxes
tailleCheckboxes.forEach(checkbox => {
    checkbox.setAttribute('name', 'tailles[]');
    checkbox.disabled = false;
});

// Réactiver les inputs personnalisés
customSizeInput.disabled = false;
addSizeButton.disabled = false;
```

#### 4. Gestion des événements
- `categorieSelect.addEventListener('change', toggleTaillesSection)`
- La fonction est appelée à chaque changement de catégorie
- L'interface se met à jour dynamiquement

### Validation Côté Serveur

#### Dans `ProductController::store()`

```php
// Récupérer la catégorie pour vérifier si c'est un accessoire
$categorie = \App\Models\Category::find($request->categorie_id);
$isAccessoire = $categorie && strtolower($categorie->name) === 'accessoire';

$data = $request->validate([
    'name' => 'required|string|max:255',
    'couleurs' => 'required|array|min:1',
    'couleurs_hex' => 'array',
    'stock_couleurs' => 'nullable|array',
    'tailles' => $isAccessoire ? 'nullable|array' : 'required|array|min:1',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    'quantite_stock' => 'required|integer|min:0',
    'categorie_id' => 'required|exists:categories,id',
    'prix_admin' => 'required|numeric|min:0',
    'prix_vente' => 'required|numeric|min:0',
]);

// Sauvegarde des tailles selon le type de catégorie
if ($isAccessoire) {
    $data['tailles'] = json_encode([]);  // Tableau vide pour les accessoires
} else {
    $data['tailles'] = json_encode($data['tailles'] ?? []);
}
```

### Règles de Validation

| Type de Catégorie | Règle de Validation | Comportement |
|-------------------|---------------------|--------------|
| **Accessoire** | `tailles => 'nullable|array'` | Tailles non requises, sauvegardées comme tableau vide |
| **Autres** | `tailles => 'required|array|min:1'` | Tailles obligatoires, au moins une taille requise |

### Avantages du Système

1. **Interface Dynamique** : L'interface s'adapte automatiquement selon la catégorie
2. **Validation Intelligente** : Les règles de validation s'adaptent au contexte
3. **Expérience Utilisateur** : Pas de confusion entre produits avec/sans tailles
4. **Cohérence des Données** : Les accessoires sont toujours sauvegardés sans tailles
5. **Maintenance Facile** : Logique centralisée et réutilisable

### Cas d'Usage

#### Création d'un Accessoire
1. L'utilisateur sélectionne une catégorie "Accessoire"
2. La section des tailles disparaît automatiquement
3. L'utilisateur remplit les autres champs (nom, couleurs, prix, etc.)
4. Le produit est sauvegardé avec un tableau de tailles vide

#### Création d'un Produit avec Tailles
1. L'utilisateur sélectionne une catégorie "Chaussures" ou "Vêtements"
2. La section des tailles est visible et active
3. L'utilisateur doit sélectionner au moins une taille
4. Le produit est sauvegardé avec les tailles sélectionnées

### Dépannage

#### Problèmes Courants

1. **Section des tailles ne se masque pas**
   - Vérifier que la fonction `toggleTaillesSection()` est bien définie
   - Vérifier que l'ID `categorie_id` correspond au select
   - Vérifier la console JavaScript pour les erreurs

2. **Validation côté serveur échoue**
   - Vérifier que la logique de détection des accessoires fonctionne
   - Vérifier que les règles de validation sont bien appliquées

3. **Tailles envoyées pour un accessoire**
   - Vérifier que l'attribut `name` est bien retiré des checkboxes
   - Vérifier que les inputs personnalisés sont désactivés

### Tests Recommandés

1. **Test de création d'accessoire**
   - Sélectionner une catégorie accessoire
   - Vérifier que la section tailles disparaît
   - Créer le produit sans tailles
   - Vérifier qu'il est sauvegardé correctement

2. **Test de changement de catégorie**
   - Passer d'une catégorie normale à une catégorie accessoire
   - Vérifier que la section tailles se masque
   - Revenir à une catégorie normale
   - Vérifier que la section tailles réapparaît

3. **Test de validation**
   - Tenter de créer un produit normal sans tailles (doit échouer)
   - Tenter de créer un accessoire avec tailles (doit fonctionner)
   - Vérifier les messages d'erreur appropriés
