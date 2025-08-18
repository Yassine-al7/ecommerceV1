<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'commandes';

    protected $fillable = [
        'seller_id',
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
        'facturation_status',
        'commentaire',
    ];

    protected $casts = [
        'produits' => 'array',
        'prix_produit' => 'decimal:2',
        'prix_commande' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'produits');
    }
}
