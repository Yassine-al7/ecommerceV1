<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Spécifier la table française
    protected $table = 'commandes';

    protected $fillable = [
        'reference',
        'nom_client',
        'ville',
        'adresse_client',
        'numero_telephone_client',
        'produits',
        'taille_produit',
        'quantite_produit',
        'prix_produit',
        'prix_commande',
        'status',
        'seller_id',
        'facturation_status',
        'commentaire',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }

    public function calculateTotalAmount()
    {
        return $this->orderItems->sum('price');
    }
}
