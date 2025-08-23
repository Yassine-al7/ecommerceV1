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
}
