<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model {
    use HasFactory;

    protected $fillable = [ 'category_name', 'subcategory_image', 'subcategory_name', 'category_display', 'is_gift', 'country_id' ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}