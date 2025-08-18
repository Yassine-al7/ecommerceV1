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
        'prix_admin',
        'prix_vente',
        'quantite_stock',
        'categorie_id',
        'vendeur_id',
    ];

    protected $casts = [
        'tailles' => 'array',
        'prix_admin' => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'product_user', 'product_id', 'user_id')
            ->withPivot('prix_admin', 'prix_vente', 'visible');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'produits');
    }
}
