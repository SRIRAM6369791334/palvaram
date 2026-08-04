<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MilkOrder extends Model
{
    use HasFactory;

    protected $fillable =  ['id', 'product_id', 'quantity', 'order_id', 'from_date', 'to_date', 'date_to_delivery', 'date_ordered_on', 'no_of_days', 'plan_type', 'delivery_person_id', 'is_delivery_assigned', 'user_id', 'payment_status', 'current_status', 'is_cancelled', 'reason_for_cancel', 'created_at', 'updated_at'];



    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }



    public function area()
    {
        return $this->customer->area();
    }

    public function transactionLog()
    {
        return $this->hasOne(MilkTransactionLog::class, "order_id", "order_id");
    }

    public function getDateOrderedOnAttribute($value)
    {
        return Carbon::parse($value)->format('d-M-Y');
    }

    public function orderAddress(): HasOne
    {
        return $this->hasOne(MilkOrderUserAddress::class, "order_id", "order_id");
    }
}
