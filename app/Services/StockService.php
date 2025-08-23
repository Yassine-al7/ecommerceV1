<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Diminue le stock d'un produit
     */
    public static function decreaseStock(Product $product, int $quantity, string $color = null)
    {
        try {
            // Vérifier que le stock est suffisant
            if ($product->quantite_stock < $quantity) {
                throw new \Exception("Stock insuffisant pour {$product->name}. Stock disponible: {$product->quantite_stock}");
            }

            // Diminuer le stock total
            $product->decrement('quantite_stock', $quantity);

            // Si une couleur est spécifiée, diminuer le stock de cette couleur
            if ($color) {
                $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
                foreach ($stockCouleurs as &$stockColor) {
                    if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $color) {
                        $stockColor['quantity'] = max(0, ($stockColor['quantity'] ?? 0) - $quantity);
                        break;
                    }
                }
                $product->update(['stock_couleurs' => json_encode($stockCouleurs)]);
            }

            Log::info("Stock diminué pour {$product->name} (ID: {$product->id}) - Quantité: {$quantity} - Couleur: {$color} - Nouveau stock: {$product->quantite_stock}");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la diminution du stock: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Augmente le stock d'un produit
     */
    public static function increaseStock(Product $product, int $quantity, string $color = null)
    {
        try {
            // Augmenter le stock total
            $product->increment('quantite_stock', $quantity);

            // Si une couleur est spécifiée, augmenter le stock de cette couleur
            if ($color) {
                $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
                foreach ($stockCouleurs as &$stockColor) {
                    if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $color) {
                        $stockColor['quantity'] = ($stockColor['quantity'] ?? 0) + $quantity;
                        break;
                    }
                }
                $product->update(['stock_couleurs' => json_encode($stockCouleurs)]);
            }

            Log::info("Stock augmenté pour {$product->name} (ID: {$product->id}) - Quantité: {$quantity} - Couleur: {$color} - Nouveau stock: {$product->quantite_stock}");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'augmentation du stock: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifie si le stock est suffisant
     */
    public static function checkStockAvailability(Product $product, int $quantity, string $color = null): bool
    {
        // Vérifier le stock total
        if ($product->quantite_stock < $quantity) {
            return false;
        }

        // Si une couleur est spécifiée, vérifier le stock de cette couleur
        if ($color) {
            $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
            foreach ($stockCouleurs as $stockColor) {
                if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $color) {
                    return ($stockColor['quantity'] ?? 0) >= $quantity;
                }
            }
            return false; // Couleur non trouvée
        }

        return true;
    }

    /**
     * Récupère le stock disponible pour une couleur spécifique
     */
    public static function getColorStock(Product $product, string $color): int
    {
        $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
        foreach ($stockCouleurs as $stockColor) {
            if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $color) {
                return (int) ($stockColor['quantity'] ?? 0);
            }
        }
        return 0;
    }

    /**
     * Met à jour le stock d'une couleur spécifique
     */
    public static function updateColorStock(Product $product, string $color, int $quantity): bool
    {
        try {
            $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
            $colorFound = false;

            foreach ($stockCouleurs as &$stockColor) {
                if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $color) {
                    $stockColor['quantity'] = max(0, $quantity);
                    $colorFound = true;
                    break;
                }
            }

            // Si la couleur n'existe pas, l'ajouter
            if (!$colorFound) {
                $stockCouleurs[] = [
                    'name' => $color,
                    'quantity' => max(0, $quantity)
                ];
            }

            $product->update(['stock_couleurs' => json_encode($stockCouleurs)]);

            Log::info("Stock de couleur mis à jour pour {$product->name} (ID: {$product->id}) - Couleur: {$color} - Nouveau stock: {$quantity}");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour du stock de couleur: " . $e->getMessage());
            throw $e;
        }
    }
}
