<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = "coupons";
    protected $fillable = ['codename','mini_amt', 'discounttype', 'discount', 'start_date', 'end_date','default_id'];

}