<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTracking extends Model
{
    use HasFactory;

    protected $table = "product_tracking";
    
    protected $fillable = [
        "user_id",
        "order_id",
        "delivery_status",
        "status", // set to 'cancelled' by ProfileController::cancelOrder - was silently dropped
        "return_requested", // 1 = requested (dashboard /returnrequests lists these), 3 = rejected
    ];
}
