<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'numero_telephone',
        'store_name',
        'rib',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function assignedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_user', 'user_id', 'product_id')
            ->withPivot('prix_admin', 'prix_vente', 'visible');
    }

    public function scopeSellers($query)
    {
        return $query->where('role', 'seller');
    }
}
