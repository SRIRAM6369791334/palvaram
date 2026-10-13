<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';

    protected $fillable = [
        'name',
        'code',
        'shipping_charge'
    ];

    public function districts()
    {
        return $this->hasMany(District::class, 'state_name', 'name');
    }
}
