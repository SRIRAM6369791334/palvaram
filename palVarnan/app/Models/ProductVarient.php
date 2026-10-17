<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVarient extends Model
{

    protected $table = 'product_varient';

    protected $fillable = [ 'country_id', 'categoryid', 'subcategoryid', 'product_id', 'sku', 'barcode', 'unit_id', 'varient', 'value', 'offer_price', 'mrp_price', 'product_qty', 'product_gst','low_stock', 'hot_deals', 'Popular_products', 'pre_order', 'pre_note','product_gst', 'subcatename', 'varient_img', 'varient_name', 'size_value', 'flash_sale', 'flash_sale_date',
                            'color_id', 'size_id' ];

    public function country() {
        return $this->belongsTo( Country::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function category() {
        return $this->belongsTo( Category::class );
    }

    public function unit() {
        return $this->belongsTo( Unit::class );
    }

    public function size() {
        return $this->belongsTo( GiftSize::class, 'size_id' );
    }

    public function color() {
        return $this->belongsTo( GiftColor::class, 'color_id' );
    }
}

