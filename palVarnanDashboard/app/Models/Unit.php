<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['unit_name', 'short_name', 'country_id'];

    public function variants()
    {
        return $this->hasMany(ProductVarient::class);
    }
}
