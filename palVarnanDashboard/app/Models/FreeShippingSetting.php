<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeShippingSetting extends Model
{
    use HasFactory;
    protected $fillable = ['is_enabled', 'minimum_order_amount', 'country'];
}
