<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressType extends Model
{
    use HasFactory;
    protected $fillable = ["address_type_name"];



    public function  user()
    {
        return $this->belongsTo(User::class, "address_type_id");
    }
}
