<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_text',
        'main_title',
        'highlight_text',
        'bg_image',
        'link_url',
        'sort_order',
        'status', 
        'country_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
}
