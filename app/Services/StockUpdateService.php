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
                    Log::warning("Produit non trouvé pour la mise à jour du stock", [
                        'product_id' => $productId,
                        'order_id' => $order->id
                    ]);
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

                        $stockUpdates[] = [
                            'product_id' => $productId,
                            'product_name' => $product->name,
                            'couleur' => $couleur,
                            'ancien_stock' => $ancienStock,
                            'nouveau_stock' => $nouveauStock,
                            'quantite_livree' => $quantite
                        ];

                        Log::info("Stock mis à jour pour {$product->name} - {$couleur}", [
                            'ancien_stock' => $ancienStock,
                            'nouveau_stock' => $nouveauStock,
                            'quantite_livree' => $quantite,
                            'order_id' => $order->id
                        ]);

                        break;
                    }
                }

                // Si le stock par couleur n'a pas été trouvé, mettre à jour le stock total
                if (!$stockUpdated) {
                    $ancienStockTotal = $product->quantite_stock;
                    $nouveauStockTotal = max(0, $ancienStockTotal - $quantite);

                    $product->quantite_stock = $nouveauStockTotal;

                    $stockUpdates[] = [
                        'product_id' => $productId,
                        'product_name' => $product->name,
                        'couleur' => $couleur,
                        'ancien_stock' => $ancienStockTotal,
                        'nouveau_stock' => $nouveauStockTotal,
                        'quantite_livree' => $quantite,
                        'type' => 'stock_total'
                    ];

                    Log::info("Stock total mis à jour pour {$product->name}", [
                        'ancien_stock' => $ancienStockTotal,
                        'nouveau_stock' => $nouveauStockTotal,
                        'quantite_livree' => $quantite,
                        'order_id' => $order->id
                    ]);
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

            Log::info("Stock mis à jour avec succès pour la commande {$order->reference}", [
                'order_id' => $order->id,
                'updates_count' => count($stockUpdates),
                'stock_updates' => $stockUpdates
            ]);

            return [
                'success' => true,
                'message' => 'Stock mis à jour avec succès',
                'updates_count' => count($stockUpdates),
                'stock_updates' => $stockUpdates
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erreur lors de la mise à jour du stock", [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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

                    Log::info("Stock total corrigé pour {$product->name}", [
                        'ancien_stock' => $product->getRawOriginal('quantite_stock'),
                        'nouveau_stock' => $stockTotalCalcule
                    ]);
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
