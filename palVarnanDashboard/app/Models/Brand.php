<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['brand_name', 'brand_slug', 'brand_image', 'status', 'country_id'];

    protected $casts = [
        'country_id' => 'string',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
