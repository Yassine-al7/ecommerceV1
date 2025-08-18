<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Spécifier la table française
    protected $table = 'produits';

    protected $fillable = [
        'name',
        'couleur',
        'tailles',
        'image',
        'prix_admin',
        'prix_vente',
        'quantite_stock',
        'categorie_id',
        'vendeur_id',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function assignedSellers()
    {
        return $this->belongsToMany(User::class, 'product_user', 'product_id', 'user_id')
            ->withPivot(['prix_admin', 'prix_vente', 'visible'])
            ->withTimestamps();
    }
}
