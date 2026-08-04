<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'country_id', 'category_id', 'brand_id', 'product_name', 'slug', 'product_quantity', 'offer_price', 'mrp_price', 'product_mrp_price', 'product_regular_price', 'product_description', 'product_image', 'product_specification', 'unit_value', 'product_value', 'subcategory_id', 'cate_name', 'subcate_name', 'brand_name', 'brand_material', 'brand_type','approval_days', 'is_gift', 
                           
                            'warehouse_id',  
                            'branch_id',  ];

    public function country() {
        return $this->belongsTo( Country::class );
    }

    public function category() {
        return $this->belongsTo( Category::class );
    }

    public function brand() {
        return $this->belongsTo( Brand::class );
    }

    public function variants() {
        return $this->hasMany( ProductVarient::class );
    }

    public function productvari() {
        return $this->hasMany( ProductVarient::class );
    }
}