<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'coupon_name',
        'codename',
        'discounttype',
        'discount',
        'mini_amt',
        'start_date',
        'end_date'
    ];
}
