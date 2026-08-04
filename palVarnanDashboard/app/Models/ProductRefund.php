<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRefund extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'slot_id', 'cancelled_by', 'refund_status'];


    public function product_order()
    {
        return  $this->belongsTo(ProductOrder::class, "order_id", "order_id");
    }

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function productverient()
    {
        return $this->belongsTo(ProductVarient::class, "product_varient_id", "id");
    }


    public function product_slot()
    {
        return $this->belongsTo(ProductSlot::class, "slot_id", "id");
    }
}
