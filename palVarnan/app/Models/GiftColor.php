<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftColor extends Model
{
    use HasFactory;

    protected $table = 'gift_color';

    protected $fillable = ['color', 'country_id'];

    public function variants()
    {
        return $this->hasMany(ProductVarient::class, 'color_id');
    }
}
