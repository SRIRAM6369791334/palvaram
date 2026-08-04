<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilkSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'delivery_date', 'order_id', 'delivery_status', 'order_delivered_time', 'deliver_person_id', 'is_cancelled', 'cancel_reason', 'created_at', 'updated_at'
    ];


    public function order()
    {
        return $this->belongsTo(MilkOrder::class, "order_id", "order_id");
    }
}
