<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'ip_addresscart',
        'user_id',
        'product_id',
        'product_varient_id',
        'product_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'product_varient_id');
    }
}