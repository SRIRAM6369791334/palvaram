<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDetail extends Model
{
    use HasFactory;

    protected $table = 'supplier_details';

    protected $fillable = ['name', 'shop', 'location', 'phone', 'country_id', 'is_gift'];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}
