# 🚨 GUIDE: Alertes de Stock par Couleur et Produit

## 📋 Vue d'ensemble

Ce guide vous explique comment implémenter un système d'alertes de stock intelligent qui vérifie :
- ✅ Le stock total du produit
- 🎨 Le stock disponible par couleur spécifique
- 📏 La disponibilité par taille
- ⚠️ Les ruptures de stock en temps réel

## 🎯 Fonctionnalités implémentées

### 1. **Vérification du stock par couleur**
- Détection des couleurs non disponibles dans le stock
- Alertes pour les couleurs en rupture (0 disponible)
- Alertes pour les stocks insuffisants

### 2. **Vérification du stock total**
- Rupture totale du produit
- Stock total insuffisant pour la quantité demandée

### 3. **Interface utilisateur intuitive**
- Badges colorés selon le niveau d'alerte
- Messages d'erreur clairs avec solutions
- Modal détaillé pour plus d'informations

## 🛠️ Composants créés

### 1. **`stock-alert.blade.php`**
Composant général pour afficher les alertes de stock

**Utilisation :**
```blade
<x-stock-alert 
    :product="$product"
    :couleur="$couleur"
    :taille="$taille"
    :quantite="$quantite" />
```

### 2. **`order-product-stock-check.blade.php`**
Composant spécialisé pour les commandes avec interface détaillée

**Utilisation :**
```blade
<x-order-product-stock-check 
    :product="$product"
    :couleur="$couleur"
    :taille="$taille"
    :quantite="$quantite"
    :showDetails="true" />
```

## 🔧 Implémentation dans votre formulaire

### Étape 1: Ajouter la vérification du stock

Dans votre formulaire d'édition de commande, ajoutez cette section :

```blade
{{-- Vérification du stock en temps réel --}}
<div class="bg-gray-50 rounded-lg p-3">
    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
        <i class="fas fa-boxes mr-2 text-blue-600"></i>
        Vérification du stock
    </h5>
    
    <x-order-product-stock-check 
        :product="$product"
        :couleur="$productData['couleur']"
        :taille="$productData['taille']"
        :quantite="$productData['qty']"
        :showDetails="true" />
</div>
```

### Étape 2: Section des alertes globales

Ajoutez une section pour afficher toutes les alertes de la commande :

```blade
{{-- Section des alertes globales --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
        Alertes de stock
    </h3>
    
    @php
        $hasAlerts = false;
        $allAlerts = [];
        
        foreach($orderProducts as $productData) {
            $product = App\Models\Product::find($productData['product_id']);
            $alertes = generateStockAlert(
                $product, 
                $productData['couleur'], 
                $productData['taille'], 
                $productData['qty']
            );
            
            if (!empty($alertes)) {
                $hasAlerts = true;
                $allAlerts = array_merge($allAlerts, $alertes);
            }
        }
    @endphp
    
    @if($hasAlerts)
        {{-- Affichage des alertes --}}
        <div class="space-y-3">
            @foreach($allAlerts as $alerte)
                <div class="rounded-lg border p-4 {{ $alerte['type'] === 'danger' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">{{ $alerte['icon'] }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium {{ $alerte['type'] === 'danger' ? 'text-red-800' : 'text-yellow-800' }}">
                                {{ $alerte['message'] }}
                            </h4>
                            <div class="mt-2 text-sm {{ $alerte['type'] === 'danger' ? 'text-red-700' : 'text-yellow-700' }}">
                                <p class="flex items-center">
                                    <i class="fas fa-lightbulb mr-2 text-{{ $alerte['type'] === 'danger' ? 'red' : 'yellow' }}-600"></i>
                                    {{ $alerte['solution'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Message de succès --}}
        <div class="rounded-lg border border-green-200 bg-green-50 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        Aucun problème de stock détecté
                    </p>
                    <p class="text-sm text-green-700 mt-1">
                        Tous les produits de cette commande ont un stock suffisant
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
```

## 🎨 Types d'alertes et couleurs

### 🚨 **Danger (Rouge)**
- Couleur non disponible dans le stock
- Couleur en rupture (0 disponible)
- Produit en rupture totale

### ⚠️ **Warning (Jaune)**
- Stock insuffisant pour la quantité demandée
- Stock total insuffisant

### ✅ **Success (Vert)**
- Stock suffisant
- Aucun problème détecté

## 🔄 Mise à jour en temps réel

Pour une expérience utilisateur optimale, vous pouvez ajouter une logique AJAX :

```javascript
// Mise à jour en temps réel des alertes
document.addEventListener('DOMContentLoaded', function() {
    const productForms = document.querySelectorAll('[name*="[couleur_produit]"], [name*="[taille_produit]"], [name*="[quantite_produit]"]');
    
    productForms.forEach(input => {
        input.addEventListener('change', function() {
            // Ici vous pourriez ajouter une logique AJAX pour mettre à jour les alertes
            // sans recharger la page
            updateStockAlerts();
        });
    });
});

function updateStockAlerts() {
    // Logique AJAX pour mettre à jour les alertes
    // Vous pouvez utiliser fetch() ou axios pour appeler votre contrôleur
}
```

## 📱 Responsive et accessibilité

### **Mobile-first design**
- Composants adaptés aux petits écrans
- Boutons et interactions tactiles
- Modal responsive

### **Accessibilité**
- Contraste des couleurs respecté
- Icônes avec texte alternatif
- Navigation au clavier

## 🚀 Fonctionnalités avancées

### 1. **Suggestions automatiques**
- Proposer des couleurs alternatives disponibles
- Recommander des produits similaires
- Calcul automatique des quantités maximales

### 2. **Notifications push**
- Alertes en temps réel
- Notifications par email
- Historique des alertes

### 3. **Gestion des réapprovisionnements**
- Lien direct vers la gestion du stock
- Calcul des quantités à commander
- Suivi des commandes fournisseurs

## 🔍 Test et débogage

### **Tester votre implémentation :**

1. **Lancez le fichier de test :**
   ```bash
   php test_admin_order_edit.php
   ```

2. **Vérifiez les sections :**
   - ✅ Test des alertes de stock par couleur et produit
   - ✅ Test de la fonction utilitaire d'alertes de stock

3. **Testez dans le navigateur :**
   - Allez sur votre page d'édition de commande
   - Modifiez les couleurs, tailles ou quantités
   - Vérifiez que les alertes s'affichent correctement

## 📝 Exemple complet d'utilisation

Voir le fichier `resources/views/examples/stock-alert-usage.blade.php` pour un exemple complet d'implémentation dans un formulaire de commande.

## 🎯 Prochaines étapes

1. **Intégrez les composants** dans votre formulaire d'édition
2. **Testez les alertes** avec différents scénarios de stock
3. **Personnalisez les messages** selon vos besoins
4. **Ajoutez la logique AJAX** pour les mises à jour en temps réel
5. **Implémentez les suggestions** automatiques

---

**💡 Conseil :** Commencez par tester avec le fichier `test_admin_order_edit.php` pour vérifier que tout fonctionne, puis intégrez progressivement dans votre interface utilisateur.
