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

            // Vérifier le stock disponible pour cette couleur spécifique
            $stockCouleur = $product->getStockForColor($couleur);
            if ($stockCouleur < $quantite) {
                Log::warning("Stock insuffisant pour {$product->name} (ID: {$productId}) - Couleur: {$couleur} - Demande: {$quantite}, Disponible: {$stockCouleur}");
                // Note: On permet la commande même en rupture de stock
            }

            // Si pas de stock_couleurs défini, créer une entrée basée sur le stock total
            if (!$product->stock_couleurs || !is_array($product->stock_couleurs) || empty($product->stock_couleurs)) {
                $product->stock_couleurs = [
                    [
                        'name' => $couleur,
                        'quantity' => $product->quantite_stock
                    ]
                ];
                $product->save();
                Log::info("Stock_couleurs initialisé pour {$product->name} (ID: {$productId}) avec couleur: {$couleur}, quantité: {$product->quantite_stock}");
            }

            // Utiliser la méthode du modèle Product pour diminuer le stock par couleur
            $nouveauStock = $product->decreaseColorStock($couleur, $quantite);

            Log::info("Stock diminué pour {$product->name} (ID: {$productId}) - Couleur: {$couleur} - Quantité: {$quantite} - Nouveau stock couleur: {$nouveauStock} - Nouveau stock total: {$product->quantite_stock}");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la diminution du stock: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());
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

            // Utiliser la méthode du modèle Product pour augmenter le stock par couleur
            $nouveauStock = $product->increaseColorStock($couleur, $quantite);

            Log::info("Stock augmenté pour {$product->name} (ID: {$productId}) - Couleur: {$couleur} - Quantité: {$quantite} - Nouveau stock couleur: {$nouveauStock} - Nouveau stock total: {$product->quantite_stock}");

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
            $stockCouleur = $product->getStockForColor($couleur);

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
