<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductOrder extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'order_number',
        'order_id', // Dashboard compatibility
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_door_no',
        'billing_street',
        'billing_area',
        'billing_city',
        'billing_state',
        'billing_pincode',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_door_no',
        'shipping_street',
        'shipping_area',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'subtotal',
        'gst_amount',
        'shipping_charge',
        'total_amount',
        'payment_status',
        'payment_method',
        'razorpay_order_id',
        'razorpay_payment_id',
        'status',
        'delivery_person_id',
        'is_delivery_assigned',
        'shiprocket_order_id',
        'shiprocket_shipment_id',
        'awb_code',
        'courier_name',
        'pickup_scheduled',
        'manifest_url',
        'label_url',
        'invoice_url',
        'shiprocket_status',
        'cancellation_reason',
        'created_at',
        'updated_at',
        'reason',
        'unavailable_count',
        'unavailable_amount',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function area()
    {
        return $this->customer->area();
    }

    public function transactionLog()
    {
        return $this->hasOne(ProductTransactionLog::class, "order_id", "order_id");
    }


    public function orderAddress(): HasOne
    {
        return $this->hasOne(ProductOrderUserAddress::class, "order_id", "order_id");
    }

    protected $appends = ['order_id_display', 'delivery_status', 'is_cancelled', 'date_ordered_on', 'delivery_charge', 'grand_total_amount'];

    public function getOrderIdDisplayAttribute()
    {
        return !empty($this->order_id) ? $this->order_id : $this->order_number;
    }


    public function getDeliveryStatusAttribute()
    {
        $mapping = [
            'pending' => 0,
            'processing' => 1,
            'shipped' => 2,
            'out_for_delivery' => 3,
            'delivered' => 4,
            'cancelled' => 6,
            'return' => 6,
        ];
        return $mapping[$this->status] ?? 0;
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled' ? 1 : 0;
    }

    public function getDateOrderedOnAttribute()
    {
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }

    public function getDeliveryChargeAttribute()
    {
        return $this->shipping_charge;
    }

    public function getGrandTotalAmountAttribute()
    {
        return $this->total_amount;
    }

    public function items()
    {
        return $this->hasMany(ProductOrderItem::class, 'order_id', 'id');
    }
}
