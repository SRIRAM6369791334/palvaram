<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftSize extends Model
{
    use HasFactory;

    protected $table = 'gift_size';

    protected $fillable = ['size', 'color', 'country_id'];

    public function variants()
    {
        return $this->hasMany(ProductVarient::class);
    }
}
