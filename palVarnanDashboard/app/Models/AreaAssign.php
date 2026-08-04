<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaAssign extends Model
{
    use HasFactory;


    protected $fillable = ["area_id", "delivery_people_id"];


    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, "area_id");
    }


    public function deliveryPerson(): BelongsTo
    {
        return $this->belongsTo(DeliveryPerson::class, 'delivery_people_id');
    }
}
