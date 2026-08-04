<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlists';

    protected $fillable = [
        'ip_address', 'user_id', 'product_id',
        'product_varient_id', 'product_quantity'
    ];

    public function product() 
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'product_varient_id');
    }

}
