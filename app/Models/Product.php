<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'produits';

    protected $fillable = [
        'name',
        'couleur',
        'tailles',
        'image',
        'color_images',
        'hidden_colors',
        'quantite_stock',
        'prix_admin',
        'prix_vente',
        'categorie_id',
    ];

    protected $casts = [
        'tailles' => 'array',
        'couleur' => 'array',
        'color_images' => 'array',
        'hidden_colors' => 'array',
        'quantite_stock' => 'integer',
        'prix_admin' => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];



    /**
     * Mutateur pour couleur - encode le tableau en JSON
     */
    public function setCouleurAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['couleur'] = json_encode($value);
        } else {
            $this->attributes['couleur'] = $value;
        }
    }

    /**
     * Accesseur pour quantite_stock - retourne directement la valeur
     */
    public function getQuantiteStockAttribute($value)
    {
        return (int) $value;
    }

    /**
     * Obtenir les couleurs visibles (non masquées)
     */
    public function getVisibleColorsAttribute()
    {
        // S'assurer que couleur est un tableau
        $allColors = $this->couleur ?? [];
        if (is_string($allColors)) {
            $allColors = json_decode($allColors, true) ?? [];
        }

        $hiddenColors = $this->hidden_colors ?? [];
        if (is_string($hiddenColors)) {
            $hiddenColors = json_decode($hiddenColors, true) ?? [];
        }

        // S'assurer que allColors est un tableau avant array_filter
        if (!is_array($allColors)) {
            return [];
        }

        return array_filter($allColors, function($color) use ($hiddenColors) {
            $colorName = is_array($color) ? $color['name'] : $color;
            return !in_array($colorName, $hiddenColors);
        });
    }

    /**
     * Accesseur pour tailles - décode le JSON en tableau
     */
    public function getTaillesAttribute($value)
    {
        // Toujours récupérer la valeur brute pour éviter le cache
        $rawValue = $this->getRawOriginal('tailles');

        if (is_array($rawValue)) {
            return $rawValue;
        }

        if (is_string($rawValue)) {
            // Nettoyer la chaîne JSON des caractères échappés
            $cleanedValue = str_replace('\\"', '"', $rawValue);
            $cleanedValue = str_replace('\\\\', '\\', $cleanedValue);

            // Si la chaîne commence et se termine par des guillemets, les supprimer
            if (strlen($cleanedValue) >= 2 && $cleanedValue[0] === '"' && $cleanedValue[-1] === '"') {
                $cleanedValue = substr($cleanedValue, 1, -1);
            }

            $decoded = json_decode($cleanedValue, true);

            // Debug: afficher les étapes de nettoyage
            \Log::info("Tailles Accesseur Debug", [
                'raw' => $rawValue,
                'cleaned' => $cleanedValue,
                'decoded' => $decoded,
                'json_error' => json_last_error_msg()
            ]);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Mutateur pour tailles - encode le tableau en JSON
     */
    public function setTaillesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tailles'] = json_encode($value);
        } else {
            $this->attributes['tailles'] = $value;
        }
    }

    /**
     * Accesseur pour les couleurs filtrées (sans stock = 0)
     */
    public function getCouleursFiltreesAttribute()
    {
        $couleurs = $this->couleur;
        $stockCouleurs = $this->stock_couleurs;

        if (!is_array($couleurs) || !is_array($stockCouleurs)) {
            return $couleurs;
        }

        $couleursFiltrees = [];

        foreach ($stockCouleurs as $index => $stock) {
            if ($stock['quantity'] > 0 && isset($couleurs[$index])) {
                $couleursFiltrees[] = $couleurs[$index];
            }
        }

        return $couleursFiltrees;
    }

    /**
     * Accesseur pour les stocks filtrés (sans stock = 0)
     */
    public function getStockCouleursFiltresAttribute()
    {
        $stockCouleurs = $this->stock_couleurs;

        if (!is_array($stockCouleurs)) {
            return $stockCouleurs;
        }

        return array_filter($stockCouleurs, function($stock) {
            return $stock['quantity'] > 0;
        });
    }

    /**
     * Accesseur pour color_images - décode le JSON en tableau
     */
    public function getColorImagesAttribute($value)
    {
        // Toujours récupérer la valeur brute pour éviter le cache
        $rawValue = $this->getRawOriginal('color_images');

        if (is_array($rawValue)) {
            return $rawValue;
        }

        if (is_string($rawValue)) {
            // Nettoyer la chaîne JSON des caractères échappés
            $cleanedValue = str_replace('\\"', '"', $rawValue);
            $cleanedValue = str_replace('\\\\', '\\', $cleanedValue);

            // Si la chaîne commence et se termine par des guillemets, les supprimer
            if (strlen($cleanedValue) >= 2 && $cleanedValue[0] === '"' && $cleanedValue[-1] === '"') {
                $cleanedValue = substr($cleanedValue, 1, -1);
            }

            $decoded = json_decode($cleanedValue, true);

            // Debug: afficher les étapes de nettoyage
            \Log::info("ColorImages Accesseur Debug", [
                'raw' => $rawValue,
                'cleaned' => $cleanedValue,
                'decoded' => $decoded,
                'json_error' => json_last_error_msg()
            ]);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Mutateur pour color_images - encode le tableau en JSON
     */
    public function setColorImagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['color_images'] = json_encode($value);
        } else {
            $this->attributes['color_images'] = $value;
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    // Note: vendeur_id relationship removed - using assignedUsers() pivot table instead

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'product_user', 'product_id', 'user_id')
            ->withPivot('prix_admin', 'prix_vente', 'visible');
    }

    public function assignedSellers()
    {
        return $this->belongsToMany(User::class, 'product_user', 'product_id', 'user_id')
            ->withPivot('prix_admin', 'prix_vente', 'visible')
            ->where('role', 'seller');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id');
    }

    /**
     * Vérifier si le produit est un accessoire
     */
    public function isAccessory()
    {
        if (!$this->category) {
            return false;
        }

        $categoryName = strtolower($this->category->name);
        return strpos($categoryName, 'accessoire') !== false;
    }

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
     * Obtenir les images pour une couleur spécifique
     */
    public function getImagesForColor($colorName)
    {
        if (!$this->color_images || !is_array($this->color_images)) {
            return [];
        }

        foreach ($this->color_images as $colorImage) {
            if (is_array($colorImage) && isset($colorImage['color']) && $colorImage['color'] === $colorName) {
                $images = $colorImage['images'] ?? [];
                // Nettoyer les chemins d'images des backslashes
                return array_map(function($image) {
                    return str_replace('\\/', '/', $image);
                }, $images);
            }
        }
        return [];
    }

    /**
     * Obtenir l'image principale pour une couleur spécifique
     */
    public function getMainImageForColor($colorName)
    {
        $images = $this->getImagesForColor($colorName);
        return !empty($images) ? $images[0] : $this->image;
    }

    /**
     * Ajouter une image pour une couleur spécifique
     */
    public function addImageForColor($colorName, $imagePath)
    {
        $colorImages = $this->color_images ?: [];

        // Chercher si la couleur existe déjà
        $colorIndex = -1;
        foreach ($colorImages as $index => $colorImage) {
            if (is_array($colorImage) && isset($colorImage['color']) && $colorImage['color'] === $colorName) {
                $colorIndex = $index;
                break;
            }
        }

        if ($colorIndex >= 0) {
            // Ajouter l'image à la couleur existante
            if (!isset($colorImages[$colorIndex]['images'])) {
                $colorImages[$colorIndex]['images'] = [];
            }
            $colorImages[$colorIndex]['images'][] = $imagePath;
        } else {
            // Créer une nouvelle entrée pour cette couleur
            $colorImages[] = [
                'color' => $colorName,
                'images' => [$imagePath]
            ];
        }

        $this->color_images = $colorImages;
        return $this;
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
     * Vérifier si une couleur et une taille sont disponibles
     */
    public function isColorAndSizeAvailable($colorName, $sizeName = null)
    {
        // Vérifier d'abord si la couleur existe et a du stock
        if ($this->isColorOutOfStock($colorName)) {
            return false;
        }

        // Si c'est un accessoire, pas besoin de vérifier la taille
        if ($this->isAccessory()) {
            return true;
        }

        // Vérifier si la taille existe dans les tailles du produit
        if ($sizeName && $this->tailles) {
            $tailles = is_array($this->tailles) ? $this->tailles : json_decode($this->tailles, true);
            if (!in_array($sizeName, $tailles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtenir les tailles disponibles pour une couleur
     */
    public function getAvailableSizesForColor($colorName)
    {
        // Si c'est un accessoire, retourner un tableau vide
        if ($this->isAccessory()) {
            return [];
        }

        // Vérifier si la couleur a du stock
        if ($this->isColorOutOfStock($colorName)) {
            return [];
        }

        // Retourner toutes les tailles du produit
        $tailles = is_array($this->tailles) ? $this->tailles : json_decode($this->tailles, true);
        return $tailles ?: [];
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

    /**
     * Diminuer le stock d'une couleur
     */
    public function decreaseColorStock($colorName, $quantity)
    {
        $currentStock = $this->getStockForColor($colorName);
        $newStock = max(0, $currentStock - $quantity);
        $this->updateColorStock($colorName, $newStock);
        return $newStock;
    }

    /**
     * Augmenter le stock d'une couleur
     */
    public function increaseColorStock($colorName, $quantity)
    {
        $currentStock = $this->getStockForColor($colorName);
        $newStock = $currentStock + $quantity;
        $this->updateColorStock($colorName, $newStock);
        return $newStock;
    }

    /**
     * Obtenir le résumé du stock par couleur et taille
     */
    public function getStockSummary()
    {
        $summary = [];

        if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
            return $summary;
        }

        foreach ($this->stock_couleurs as $colorStock) {
            if (is_array($colorStock) && isset($colorStock['name'])) {
                $colorName = $colorStock['name'];
                $quantity = (int) ($colorStock['quantity'] ?? 0);

                $summary[] = [
                    'color' => $colorName,
                    'quantity' => $quantity,
                    'available_sizes' => $this->getAvailableSizesForColor($colorName),
                    'is_low_stock' => $this->isColorLowStock($colorName),
                    'is_out_of_stock' => $this->isColorOutOfStock($colorName)
                ];
            }
        }

        return $summary;
    }
}
