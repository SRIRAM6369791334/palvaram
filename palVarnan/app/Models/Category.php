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

    public function subCategories()
    {
        // sub_categories links by `category_name` (no `category_id` column)
        return $this->hasMany(SubCategory::class, 'category_name', 'category_name');
    }

    public function getSlugAttribute()
    {
        return \Illuminate\Support\Str::slug($this->category_name);
    }
}
