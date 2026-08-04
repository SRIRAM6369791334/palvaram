<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_code',
        'supplier_name',
        'total_amount',
        'status', 
        'country_id',
        'warehouse_id',
        'branch_id',
        'supplier_id',
    ];


    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function branch()
    {
        return $this->belongsTo(WarehouseBranch::class, 'branch_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierDetail::class, 'supplier_id');
    }
    
}