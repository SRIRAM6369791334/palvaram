<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $table = 'productstocks';

    protected $fillable = [
        'productid',
        'category_id',
        'subcategory_id',
        'pro_ver_id',
        'productname',
        'overallstock',
        'availablestock',
        'salestock',
        'low_stocks',
        'last_stockupdate_date',
        'country_id',
        'warehouse_id',
        'branch_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'productid');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'pro_ver_id');
    }
}
