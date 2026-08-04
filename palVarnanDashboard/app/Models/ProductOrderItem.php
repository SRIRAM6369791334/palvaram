<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderItem extends Model
{
    use HasFactory;

    protected $table = 'product_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'quantity',
        'price',
        'gst_rate',
        'gst_amount',
        'total'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
