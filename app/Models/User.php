<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable // implements MustVerifyEmail  // TEMPORAIREMENT DÉSACTIVÉ
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'numero_telephone',
        'phone',
        'address',
        'store_name',
        'rib',
        'email_verified_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSeller(): bool
    {
        return $this->hasRole('seller');
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function assignedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_user', 'user_id', 'product_id')
            ->withPivot(['prix_admin', 'prix_vente', 'visible'])
            ->withTimestamps();
    }

    /**
     * Relation avec les commandes du vendeur
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    /**
     * Relation avec les commandes livrées du vendeur
     */
    public function deliveredOrders()
    {
        return $this->hasMany(Order::class, 'seller_id')->where('status', 'livré');
    }

    protected static function booted(): void
{
    static::created(function (User $user) {
        if ($user->role === 'seller') {
            $user->assignAllProductsByDefault();
        }
    });

    static::updated(function (User $user) {
        if ($user->wasChanged('role') && $user->role === 'seller') {
            $user->assignAllProductsByDefault();
        }
    });
}

public function assignAllProductsByDefault(): void
{
    if ($this->role !== 'seller') return;

    $already = $this->assignedProducts()->pluck('produits.id')->toArray();
    $products = Product::select('id', 'prix_admin', 'prix_vente')->get();

    $attach = [];
    foreach ($products as $product) {
        if (!in_array($product->id, $already, true)) {
            $arr = json_decode($product->prix_admin, true);
            if (is_array($arr) && !empty($arr)) {
                $nums = array_filter($arr, fn($v) => is_numeric($v));
                $avg = !empty($nums) ? array_sum($nums) / count($nums) : (float) $product->prix_vente;
            } else {
                $avg = is_numeric($product->prix_admin) ? (float) $product->prix_admin : (float) $product->prix_vente;
            }

            $attach[$product->id] = [
                'prix_admin' => $avg,
                'prix_vente' => $product->prix_vente,
                'visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }
    if (!empty($attach)) {
        $this->assignedProducts()->attach($attach);
    }
}
}
