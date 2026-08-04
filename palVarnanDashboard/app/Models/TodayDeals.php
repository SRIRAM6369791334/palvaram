<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodayDeals extends Model {
    use HasFactory;
    protected $fillable = [ 'product_id', 'product_name', 'offer_value', 'variant_id' ];
}