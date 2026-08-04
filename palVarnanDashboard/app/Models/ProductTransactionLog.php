<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransactionLog extends Model
{
    use HasFactory;


    protected $fillable = ['order_id', 'slot_id', 'order_date', 'order_amount', 'amount_credited', 'amount_debited', 'user_id'];
}
