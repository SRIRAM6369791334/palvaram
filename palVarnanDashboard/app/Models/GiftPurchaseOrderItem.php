<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftPurchaseOrderItem extends Model
{
    protected $table = 'gift_purchase_order_items';

    protected $fillable = [
        'gift_purchase_order_id',
        'product_id',
        'variant_id',
        'barcode',
        'item_code',
        'value',
        'color_id',
        'size_id',
        'category_id',
        'subcategory_id',
        'unit_id',
        'quantity',
        'price',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'variant_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}