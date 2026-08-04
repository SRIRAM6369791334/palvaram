<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'product_quantity', 'dispatch_date', 'pre_order', 'product_varient_id', 'ip_addresscart'];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }
}
