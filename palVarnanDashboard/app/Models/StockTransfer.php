<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $table = 'stock_transfers';

    protected $fillable = [
        'product_stock_id',
        'from_warehouse_id',
        'from_branch_id',
        'to_warehouse_id',
        'to_branch_id',
        'quantity',
        'transfer_code'
    ];
}
