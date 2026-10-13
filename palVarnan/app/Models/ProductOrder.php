<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductOrder extends Model
{
    use HasFactory;

    protected $table = 'product_orders';

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
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductOrderItem::class, 'order_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(ProductSlot::class, 'order_id', 'order_id');
    }

    public function orderAddress(): HasOne
    {
        return $this->hasOne(ProductOrderUserAddress::class, 'order_id', 'order_id');
    }

    public function transactionLog(): HasOne
    {
        return $this->hasOne(ProductTransactionLog::class, 'order_id', 'order_id');
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(ProductTracking::class, 'order_id', 'order_id');
    }
}
