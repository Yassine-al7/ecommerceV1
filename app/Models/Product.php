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
        'stock_couleurs',
        'tailles',
        'image',
        'prix_admin',
        'prix_vente',
        'quantite_stock',
        'categorie_id',
    ];

    protected $casts = [
        'tailles' => 'array',
        'couleur' => 'array',
        'stock_couleurs' => 'array',
        'prix_admin' => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];

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

    public function orders()
    {
        return $this->hasMany(Order::class, 'produits');
    }

    /**
     * Vérifier si une couleur spécifique est en stock
     */
    public function isColorInStock($colorName)
    {
        if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
            return false;
        }

        foreach ($this->stock_couleurs as $colorStock) {
            if (is_array($colorStock) && isset($colorStock['name']) && $colorStock['name'] === $colorName) {
                return isset($colorStock['quantity']) && $colorStock['quantity'] > 0;
            } elseif (is_string($colorStock) && $colorStock === $colorName) {
                // Fallback pour l'ancien format
                return true;
            }
        }

        return false;
    }

    /**
     * Obtenir la quantité en stock pour une couleur spécifique
     */
    public function getColorStockQuantity($colorName)
    {
        if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
            return 0;
        }

        foreach ($this->stock_couleurs as $colorStock) {
            if (is_array($colorStock) && isset($colorStock['name']) && $colorStock['name'] === $colorName) {
                return $colorStock['quantity'] ?? 0;
            }
        }

        return 0;
    }

    /**
     * Obtenir toutes les couleurs en rupture de stock
     */
    public function getOutOfStockColors()
    {
        if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
            return [];
        }

        $outOfStockColors = [];
        foreach ($this->stock_couleurs as $colorStock) {
            if (is_array($colorStock) && isset($colorStock['name'])) {
                $quantity = $colorStock['quantity'] ?? 0;
                if ($quantity <= 0) {
                    $outOfStockColors[] = [
                        'name' => $colorStock['name'],
                        'hex' => $colorStock['hex'] ?? null,
                        'quantity' => $quantity
                    ];
                }
            }
        }

        return $outOfStockColors;
    }

    /**
     * Obtenir toutes les couleurs avec stock faible (≤5)
     */
    public function getLowStockColors()
    {
        if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
            return [];
        }

        $lowStockColors = [];
        foreach ($this->stock_couleurs as $colorStock) {
            if (is_array($colorStock) && isset($colorStock['name'])) {
                $quantity = $colorStock['quantity'] ?? 0;
                if ($quantity > 0 && $quantity <= 5) {
                    $lowStockColors[] = [
                        'name' => $colorStock['name'],
                        'hex' => $colorStock['hex'] ?? null,
                        'quantity' => $quantity
                    ];
                }
            }
        }

        return $lowStockColors;
    }

    /**
     * Mettre à jour le stock d'une couleur spécifique
     */
    public function updateColorStock($colorName, $quantity)
    {
        if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
            $this->stock_couleurs = [];
        }

        $updated = false;
        foreach ($this->stock_couleurs as $key => $colorStock) {
            if (is_array($colorStock) && isset($colorStock['name']) && $colorStock['name'] === $colorName) {
                $this->stock_couleurs[$key]['quantity'] = max(0, $quantity);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            // Ajouter une nouvelle couleur si elle n'existe pas
            $this->stock_couleurs[] = [
                'name' => $colorName,
                'quantity' => max(0, $quantity)
            ];
        }

        $this->save();
        return $this;
    }

    /**
     * Vérifier si le produit a des couleurs en rupture
     */
    public function hasOutOfStockColors()
    {
        return count($this->getOutOfStockColors()) > 0;
    }

    /**
     * Vérifier si le produit a des couleurs avec stock faible
     */
    public function hasLowStockColors()
    {
        return count($this->getLowStockColors()) > 0;
    }

    /**
     * Obtenir le statut global du stock (considérant toutes les couleurs)
     */
    public function getGlobalStockStatus()
    {
        if (!$this->stock_couleurs || !is_array($this->stock_couleurs)) {
            return 'unknown';
        }

        $totalQuantity = 0;
        $hasStock = false;

        foreach ($this->stock_couleurs as $colorStock) {
            if (is_array($colorStock) && isset($colorStock['quantity'])) {
                $totalQuantity += $colorStock['quantity'];
                if ($colorStock['quantity'] > 0) {
                    $hasStock = true;
                }
            }
        }

        if (!$hasStock) {
            return 'out_of_stock';
        } elseif ($totalQuantity <= 5) {
            return 'low';
        } elseif ($totalQuantity <= 20) {
            return 'medium';
        } else {
            return 'good';
        }
    }
}
