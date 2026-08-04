<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftPurchaseOrder extends Model
{
    protected $table = 'gift_purchase_orders';
    
    protected $fillable = [
        'po_code', 'supplier_id', 'supplier_name',
        'country_id', 'warehouse_id', 'total_amount', 'status',
    ];

    public function supplier()  { return $this->belongsTo(SupplierDetail::class, 'supplier_id'); }
    public function country()   { return $this->belongsTo(Country::class, 'country_id'); }
    public function warehouse() { return $this->belongsTo(WareHouse::class, 'warehouse_id'); }
    public function items()     { return $this->hasMany(GiftPurchaseOrderItem::class, 'gift_purchase_order_id'); }
}