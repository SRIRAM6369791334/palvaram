<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouseBranch extends Model
{
    use HasFactory;

    protected $table = 'warehouse_branch';

    protected $fillable = [
        'country_id',
        'warehouse_id',
        'branch_name', 'phone', 'address', 'pincode', 'city', 'state'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class);
    }
}
