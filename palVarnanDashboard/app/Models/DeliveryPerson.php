<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DeliveryPerson extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['delivery_person_id', 'name', 'email', 'phone_number', 'email_verified_at', 'password', 'enc_password', 'remember_token'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        "enc_password",
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];




    public function areaAssigns(): HasMany
    {
        return $this->hasMany(AreaAssign::class, 'id', 'id');
    }

    public function milkOrders(): HasMany
    {
        return $this->hasMany(MilkOrder::class, 'delivery_person_id', "delivery_person_id");
    }


    public function productOrders(): HasMany
    {
        return $this->hasMany(ProductOrder::class, 'delivery_person_id', "delivery_person_id");
    }
}
