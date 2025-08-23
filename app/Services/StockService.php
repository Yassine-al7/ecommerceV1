<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Diminue le stock d'un produit pour une commande
     *
     * @param int $productId
     * @param string $couleur
     * @param int $quantite
     * @return bool
     */
    public static function decreaseStock(int $productId, string $couleur, int $quantite): bool
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                Log::error("Produit non trouvé pour la diminution de stock: ID {$productId}");
                return false;
            }

            // Vérifier que le stock est suffisant
            if ($product->quantite_stock < $quantite) {
                Log::warning("Stock insuffisant pour {$product->name} (ID: {$productId}) - Demande: {$quantite}, Disponible: {$product->quantite_stock}");
                // Note: On permet la commande même en rupture de stock
            }

            // Diminuer le stock total du produit
            $product->quantite_stock = max(0, $product->quantite_stock - $quantite);

            // Diminuer le stock de la couleur spécifique
            $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
            $stockUpdated = false;

            foreach ($stockCouleurs as &$stockColor) {
                if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $couleur) {
                    $stockColor['quantity'] = max(0, ($stockColor['quantity'] ?? 0) - $quantite);
                    $stockUpdated = true;
                    break;
                }
            }

            // Sauvegarder les modifications
            $product->stock_couleurs = json_encode($stockCouleurs);
            $product->save();

            Log::info("Stock diminué pour {$product->name} (ID: {$productId}) - Couleur: {$couleur} - Quantité: {$quantite} - Nouveau stock total: {$product->quantite_stock}");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la diminution du stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Augmente le stock d'un produit (pour annulation de commande)
     *
     * @param int $productId
     * @param string $couleur
     * @param int $quantite
     * @return bool
     */
    public static function increaseStock(int $productId, string $couleur, int $quantite): bool
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                Log::error("Produit non trouvé pour l'augmentation de stock: ID {$productId}");
                return false;
            }

            // Augmenter le stock total du produit
            $product->quantite_stock += $quantite;

            // Augmenter le stock de la couleur spécifique
            $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
            $stockUpdated = false;

            foreach ($stockCouleurs as &$stockColor) {
                if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $couleur) {
                    $stockColor['quantity'] = ($stockColor['quantity'] ?? 0) + $quantite;
                    $stockUpdated = true;
                    break;
                }
            }

            // Sauvegarder les modifications
            $product->stock_couleurs = json_encode($stockCouleurs);
            $product->save();

            Log::info("Stock augmenté pour {$product->name} (ID: {$productId}) - Couleur: {$couleur} - Quantité: {$quantite} - Nouveau stock total: {$product->quantite_stock}");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'augmentation du stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajuste le stock d'un produit (pour modification de commande)
     *
     * @param int $productId
     * @param string $couleur
     * @param int $oldQuantite
     * @param int $newQuantite
     * @return bool
     */
    public static function adjustStock(int $productId, string $couleur, int $oldQuantite, int $newQuantite): bool
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                Log::error("Produit non trouvé pour l'ajustement de stock: ID {$productId}");
                return false;
            }

            $difference = $newQuantite - $oldQuantite;

            if ($difference > 0) {
                // Nouvelle quantité plus grande, diminuer le stock
                return self::decreaseStock($productId, $couleur, $difference);
            } elseif ($difference < 0) {
                // Nouvelle quantité plus petite, augmenter le stock
                return self::increaseStock($productId, $couleur, abs($difference));
            }

            // Aucun changement
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajustement du stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie la disponibilité du stock pour une couleur
     *
     * @param int $productId
     * @param string $couleur
     * @param int $quantite
     * @return array
     */
    public static function checkStockAvailability(int $productId, string $couleur, int $quantite): array
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return [
                    'available' => false,
                    'message' => 'Produit non trouvé',
                    'stock_total' => 0,
                    'stock_couleur' => 0
                ];
            }

            $stockTotal = $product->quantite_stock;
            $stockCouleur = 0;

            // Récupérer le stock de la couleur spécifique
            $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
            foreach ($stockCouleurs as $stockColor) {
                if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $couleur) {
                    $stockCouleur = (int) ($stockColor['quantity'] ?? 0);
                    break;
                }
            }

            $sufficientStock = $stockCouleur >= $quantite;
            $sufficientTotalStock = $stockTotal >= $quantite;

            return [
                'available' => $sufficientStock && $sufficientTotalStock,
                'message' => $sufficientStock ? 'Stock suffisant' : 'Stock insuffisant pour cette couleur',
                'stock_total' => $stockTotal,
                'stock_couleur' => $stockCouleur,
                'requested' => $quantite,
                'deficit' => max(0, $quantite - $stockCouleur)
            ];
        } catch (\Exception $e) {
            Log::error("Erreur lors de la vérification du stock: " . $e->getMessage());
            return [
                'available' => false,
                'message' => 'Erreur lors de la vérification du stock',
                'stock_total' => 0,
                'stock_couleur' => 0
            ];
        }
    }
}
