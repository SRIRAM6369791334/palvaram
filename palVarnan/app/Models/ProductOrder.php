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

    // Order status values shared with the admin dashboard (palVarnanDashboard
    // ProductController::upadetstatus status_map and its list filters). Store
    // these exact lowercase codes; use statusLabel() for customer-facing text.
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_RETURN_REQUESTED = 'return_requested';
    public const STATUS_RETURN = 'return';

    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_PENDING = 'pending';

    public static function statusLabel(?string $status): string
    {
        $labels = [
            self::STATUS_PENDING => 'Order Placed',
            self::STATUS_PROCESSING => 'Packing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_OUT_FOR_DELIVERY => 'Out for Delivery',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_RETURN_REQUESTED => 'Return Requested',
            self::STATUS_RETURN => 'Returned',
        ];
        $key = strtolower(trim((string) $status));
        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key ?: self::STATUS_PENDING));
    }

    protected $fillable = [
        'user_id',
        'country_id',
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
        'coupon_discount',
        'total_amount',
        'payment_status',
        'payment_method',
        'razorpay_order_id',
        'razorpay_payment_id',
        'status',
        'delivery_status',
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
        'reason', // return reason from ProfileController::returnOrder
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
