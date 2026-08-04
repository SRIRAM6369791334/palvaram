<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $fillable = [
        'otp',
        'phone_number',
        'validity_time',
    ];



    public function scopeValid($query)
    {
        return $query->where('validity_time', '>', now());
    }
}
