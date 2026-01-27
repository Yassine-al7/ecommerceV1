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
        'prix_commande',
        'marge_benefice',
        'status',
        'facturation_status',
        'commentaire',
    ];

    protected $casts = [
        'produits' => 'array',
        'prix_commande' => 'decimal:2',
        'marge_benefice' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'produits');
    }

    /**
     * Relation avec la facturation (utilise le champ facturation_status)
     */

}
