<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'prod_id',
        'prod_var_id',
        'review',
        'ratings',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'prod_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
