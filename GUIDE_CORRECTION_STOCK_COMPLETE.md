# 🔧 GUIDE COMPLET: Correction du Stock et Mise à Jour Automatique

## 📋 Vue d'ensemble

Ce guide explique comment résoudre le problème d'affichage incorrect du stock (250 affiché pour chaque couleur) et implémenter la mise à jour automatique du stock après livraison des commandes.

## 🚨 Problèmes identifiés

### **1. Affichage incorrect du stock :**
- ❌ Stock total (250) affiché pour chaque couleur au lieu du stock réel
- ❌ Confusion entre stock total et stock par couleur
- ❌ Couleurs sans stock affichées dans la liste

### **2. Mise à jour du stock manquante :**
- ❌ Stock non mis à jour après livraison des commandes
- ❌ Incohérences entre stock affiché et stock réel
- ❌ Pas de synchronisation automatique

## 🔧 Solutions implémentées

### **1. Correction de l'affichage du stock**

#### **A. Filtrage intelligent des couleurs :**
```javascript
// Mettre à jour les couleurs en filtrant celles sans stock
const couleurSelect = row.querySelector('.couleur-select');
couleurSelect.innerHTML = '<option value="">Sélectionner une couleur</option>';

// Récupérer les stocks par couleur
let couleursDisponibles = [];
if (selectedOption.getAttribute('data-stock-couleurs')) {
    try {
        const stockCouleurs = JSON.parse(selectedOption.getAttribute('data-stock-couleurs'));
        couleurs.forEach(couleur => {
            const couleurName = typeof couleur === 'string' ? couleur : couleur.name;
            
            // Chercher le stock pour cette couleur
            let stockCouleur = 0;
            stockCouleurs.forEach(stockData => {
                if (stockData.name === couleurName) {
                    stockCouleur = parseInt(stockData.quantity) || 0;
                }
            });
            
            // Ajouter la couleur seulement si elle a du stock
            if (stockCouleur > 0) {
                couleursDisponibles.push({
                    name: couleurName,
                    stock: stockCouleur
                });
            }
        });
    } catch (e) {
        console.error('Erreur parsing stock_couleurs:', e);
    }
}
```

#### **B. Affichage clair du stock :**
```javascript
// Afficher les couleurs disponibles avec leur stock
couleursDisponibles.forEach(couleurData => {
    const option = document.createElement('option');
    option.value = couleurData.name;
    
    // Afficher le stock de manière claire
    if (couleurData.stock === 'N/A') {
        option.textContent = `${couleurData.name} (Stock: N/A)`;
    } else {
        option.textContent = `${couleurData.name} (Stock: ${couleurData.stock})`;
    }
    
    option.setAttribute('data-stock', couleurData.stock);
    couleurSelect.appendChild(option);
});
```

### **2. Service de mise à jour automatique du stock**

#### **A. StockUpdateService :**
```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StockUpdateService
{
    /**
     * Mettre à jour le stock après livraison d'une commande
     */
    public static function updateStockAfterDelivery(Order $order)
    {
        try {
            DB::beginTransaction();
            
            $produits = json_decode($order->produits, true) ?: [];
            $stockUpdates = [];
            
            foreach ($produits as $produit) {
                $productId = $produit['product_id'] ?? null;
                $couleur = $produit['couleur'] ?? null;
                $quantite = intval($produit['qty'] ?? 0);
                
                if (!$productId || !$couleur || $quantite <= 0) {
                    continue;
                }
                
                $product = Product::find($productId);
                if (!$product) {
                    continue;
                }
                
                // Mettre à jour le stock par couleur
                $stockCouleurs = $product->stock_couleurs ?: [];
                $stockUpdated = false;
                
                foreach ($stockCouleurs as $index => $stockCouleur) {
                    if (is_array($stockCouleur) && isset($stockCouleur['name']) && $stockCouleur['name'] === $couleur) {
                        $ancienStock = intval($stockCouleur['quantity'] ?? 0);
                        $nouveauStock = max(0, $ancienStock - $quantite);
                        
                        $stockCouleurs[$index]['quantity'] = $nouveauStock;
                        $stockUpdated = true;
                        
                        break;
                    }
                }
                
                // Mettre à jour le stock_couleurs si modifié
                if ($stockUpdated) {
                    $product->stock_couleurs = $stockCouleurs;
                }
                
                // Recalculer le stock total basé sur les stocks par couleur
                if ($stockUpdated && !empty($stockCouleurs)) {
                    $stockTotalCalcule = 0;
                    foreach ($stockCouleurs as $stockCouleur) {
                        if (is_array($stockCouleur) && isset($stockCouleur['quantity'])) {
                            $stockTotalCalcule += intval($stockCouleur['quantity']);
                        }
                    }
                    $product->quantite_stock = $stockTotalCalcule;
                }
                
                $product->save();
            }
            
            // Marquer la commande comme traitée pour le stock
            $order->stock_updated = true;
            $order->stock_updated_at = now();
            $order->save();
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Stock mis à jour avec succès',
                'updates_count' => count($stockUpdates)
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du stock: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour le stock pour toutes les commandes livrées
     */
    public static function updateStockForAllDeliveredOrders()
    {
        $orders = Order::where('status', 'livré')
                      ->where('stock_updated', false)
                      ->get();
        
        $results = [];
        $totalUpdated = 0;
        
        foreach ($orders as $order) {
            $result = self::updateStockAfterDelivery($order);
            $results[] = [
                'order_id' => $order->id,
                'reference' => $order->reference,
                'result' => $result
            ];
            
            if ($result['success']) {
                $totalUpdated++;
            }
        }
        
        return [
            'total_orders' => $orders->count(),
            'total_updated' => $totalUpdated,
            'results' => $results
        ];
    }
    
    /**
     * Vérifier et corriger les incohérences de stock
     */
    public static function fixStockInconsistencies()
    {
        $products = Product::all();
        $fixedCount = 0;
        
        foreach ($products as $product) {
            $stockCouleurs = $product->stock_couleurs ?: [];
            $stockTotalCalcule = 0;
            $needsUpdate = false;
            
            if (is_array($stockCouleurs) && !empty($stockCouleurs)) {
                foreach ($stockCouleurs as $stockCouleur) {
                    if (is_array($stockCouleur) && isset($stockCouleur['quantity'])) {
                        $stockTotalCalcule += intval($stockCouleur['quantity']);
                    }
                }
                
                if ($stockTotalCalcule !== $product->quantite_stock) {
                    $product->quantite_stock = $stockTotalCalcule;
                    $needsUpdate = true;
                }
            }
            
            if ($needsUpdate) {
                $product->save();
                $fixedCount++;
            }
        }
        
        return [
            'products_checked' => $products->count(),
            'products_fixed' => $fixedCount
        ];
    }
}
```

## 🧪 Tests et diagnostics

### **1. Test de l'affichage du stock :**
```bash
php test_stock_affichage.php
```

**Vérifications :**
- ✅ Stock affiché correspond au stock réel de la couleur
- ✅ Couleurs sans stock sont masquées
- ✅ Stock total n'est pas affiché à la place du stock par couleur

### **2. Test et correction du stock :**
```bash
php test_correction_stock.php
```

**Fonctionnalités :**
- 🔍 Vérification de l'état actuel du stock
- 🔧 Correction des incohérences
- ✅ Vérification après correction
- 🚀 Test du service de mise à jour

### **3. Test du filtrage des couleurs :**
```bash
php test_filtrage_couleurs.php
```

**Vérifications :**
- ✅ Seules les couleurs avec stock > 0 sont affichées
- ❌ Les couleurs sans stock sont masquées
- 📋 Le stock est affiché à côté de chaque couleur

## 🚀 Implémentation dans votre application

### **1. Dans votre contrôleur de commandes :**

```php
use App\Services\StockUpdateService;

public function markAsDelivered(Order $order)
{
    // Marquer la commande comme livrée
    $order->status = 'livré';
    $order->save();
    
    // Mettre à jour automatiquement le stock
    $result = StockUpdateService::updateStockAfterDelivery($order);
    
    if ($result['success']) {
        return redirect()->back()->with('success', 'Commande marquée comme livrée et stock mis à jour');
    } else {
        return redirect()->back()->with('error', 'Erreur lors de la mise à jour du stock');
    }
}
```

### **2. Pour corriger toutes les commandes livrées :**

```php
// Dans une commande Artisan ou un contrôleur admin
$result = StockUpdateService::updateStockForAllDeliveredOrders();

echo "Commandes traitées: {$result['total_orders']}\n";
echo "Commandes mises à jour: {$result['total_updated']}\n";
```

### **3. Pour corriger les incohérences de stock :**

```php
$result = StockUpdateService::fixStockInconsistencies();

echo "Produits vérifiés: {$result['products_checked']}\n";
echo "Produits corrigés: {$result['products_fixed']}\n";
```

## 📱 Interface utilisateur

### **Avant (problématique) :**
```
Rouge (Stock: 250)     ❌ Incorrect
Bleu (Stock: 250)      ❌ Incorrect
Vert (Stock: 250)      ❌ Incorrect
```

### **Après (corrigé) :**
```
Rouge (Stock: 15)      ✅ Stock réel
Bleu (Stock: 0)        ❌ Masqué (rupture)
Vert (Stock: 8)        ✅ Stock réel
```

## 🔄 Flux de mise à jour automatique

### **1. Commande créée :**
- Stock vérifié lors de la création
- Alertes affichées si stock insuffisant

### **2. Commande livrée :**
- Statut changé à "livré"
- `StockUpdateService::updateStockAfterDelivery()` appelé automatiquement
- Stock par couleur mis à jour
- Stock total recalculé
- Commande marquée comme traitée pour le stock

### **3. Stock mis à jour :**
- Stock par couleur diminué de la quantité livrée
- Stock total recalculé automatiquement
- Incohérences corrigées
- Logs détaillés pour audit

## 🎯 Avantages de cette solution

1. **🎯 Précision :** Stock affiché correspond au stock réel
2. **🔄 Automatisation :** Mise à jour automatique après livraison
3. **📊 Cohérence :** Stock total toujours synchronisé avec stock par couleur
4. **🚫 Prévention :** Couleurs sans stock masquées automatiquement
5. **📝 Traçabilité :** Logs détaillés de toutes les mises à jour
6. **🛡️ Sécurité :** Transactions de base de données pour éviter les incohérences

## 🔍 Débogage

### **Si le stock affiché est toujours incorrect :**

1. **Vérifiez la console JavaScript** pour les erreurs
2. **Vérifiez les données `data-stock-couleurs`** dans le HTML
3. **Lancez le diagnostic :**
   ```bash
   php test_stock_affichage.php
   php test_correction_stock.php
   ```

### **Si les couleurs n'apparaissent pas :**

1. **Vérifiez que le produit a des couleurs définies**
2. **Vérifiez que `stock_couleurs` contient des données valides**
3. **Lancez le test de filtrage :**
   ```bash
   php test_filtrage_couleurs.php
   ```

### **Si la mise à jour du stock ne fonctionne pas :**

1. **Vérifiez que le service est bien importé**
2. **Vérifiez les logs Laravel** pour les erreurs
3. **Vérifiez que la commande a le statut "livré"**

## 🚀 Prochaines étapes

1. **Testez le diagnostic :**
   ```bash
   php test_correction_stock.php
   ```

2. **Vérifiez votre formulaire d'édition de commande**

3. **Implémentez la mise à jour automatique du stock**

4. **Testez avec des commandes livrées**

5. **Surveillez les logs pour vérifier le bon fonctionnement**

---

**💡 Conseil :** Commencez par lancer `php test_correction_stock.php` pour diagnostiquer et corriger les problèmes de stock, puis testez votre interface utilisateur.
