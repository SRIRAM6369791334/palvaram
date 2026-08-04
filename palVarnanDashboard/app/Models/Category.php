<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ["category_name", "category_image", "category_banner", "category_icon", "is_gift", "country_id"];

    protected $casts = [
        'country_id' => 'string',
    ];


    public function  products()
    {
        return $this->hasMany(Product::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
}
