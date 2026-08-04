<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = "notifications";
    protected $fillable = ['title', 'content','image','cate_id','pro_id','review','cate_name','pro_name','star','approval'];
}
