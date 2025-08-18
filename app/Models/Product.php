<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'supplier_id',
        'stock_quantity',
        'color',
        'size',
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
}
