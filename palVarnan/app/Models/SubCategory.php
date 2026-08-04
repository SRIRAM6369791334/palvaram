<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [ 'category_name', 'subcategory_image', 'subcategory_name', 'category_display', 'is_gift', 'country_id' ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
