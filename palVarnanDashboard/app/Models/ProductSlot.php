<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_date', 'order_id', 'product_id', 'product_varient_id', 'quantity', 'product_name', 'product_rate', 'product_total', 'gst_amt', 'gst_per','product_value','delivery_status', 'order_delivered_time', 'deliver_person_id', 'preorder', 'dispatch_date', 'is_cancelled', 'cancel_reason', 'created_at', 'updated_at'
    ];


    public function productOrder()
    {
        return $this->belongsTo(ProductOrder::class, "order_id", "order_id");
    }

    public function order()
    {
        return $this->belongsTo(ProductOrder::class, "order_id", "order_id");
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function productVarient()
    {
        return $this->belongsTo(ProductVarient::class);
    }
}
