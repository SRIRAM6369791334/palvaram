<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouse extends Model
{
    use HasFactory;

    protected $table = 'warehouse';

    protected $fillable = [
        'warehouse_name',
        'country_id', 'phone', 'address', 'pincode', 'city', 'state'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function branches()
    {
        return $this->hasMany(WareHouseBranch::class);
    }
}
