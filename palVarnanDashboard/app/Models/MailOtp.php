<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'otp',
        'email',
        'validity_time',
    ];



    public function scopeValid($query)
    {
        return $query->where('validity_time', '>', now());
    }
}
