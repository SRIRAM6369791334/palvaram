<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newcoupon extends Model
{
    use HasFactory;
    protected $table = 'newcoupons';
     protected $fillable = [
        'code', 'type', 'value', 'min_order_amount',
        'start_date', 'end_date', 'is_active'
    ];
}
