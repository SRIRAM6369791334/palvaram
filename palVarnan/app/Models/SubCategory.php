<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [ 'category_name', 'subcategory_image', 'subcategory_name', 'category_display', 'is_gift', 'country_id' ];

    public function category()
    {
        // sub_categories stores the parent by name (`category_name`), there is no `category_id`
        return $this->belongsTo(Category::class, 'category_name', 'category_name');
    }

    public function getSlugAttribute()
    {
        return \Illuminate\Support\Str::slug($this->subcategory_name);
    }
}
