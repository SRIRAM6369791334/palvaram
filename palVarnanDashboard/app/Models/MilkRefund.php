<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilkRefund extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'slot_id', 'cancelled_by', 'refund_status'];


    public function milk_order()
    {
        return  $this->belongsTo(MilkOrder::class, "order_id", "order_id");
    }
}
