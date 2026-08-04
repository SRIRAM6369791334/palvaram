<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockDistribution extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'warehouse_id',
        'branch_id',
        'quantity',
        'distributed_by',
        'distributed_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'variant_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouse_id');
    }

    public function branch()
    {
        return $this->belongsTo(WareHouseBranch::class, 'branch_id');
    }
}