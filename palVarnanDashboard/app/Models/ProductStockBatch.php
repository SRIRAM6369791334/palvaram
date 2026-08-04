<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockBatch extends Model
{
    use HasFactory;

    protected $table = 'product_stock_batches';

    protected $fillable = ['product_stock_id', 'batch_no', 'manufacturing_date', 'expiry_date', 'quantity', 'remaining_qty', 'offer_price'];
}
