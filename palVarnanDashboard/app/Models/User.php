<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id','is_guest_user', 'user_token', 'name', 'email', 'phone_number', 'first_name', 'last_name', 'gender', 'profile_image', 'user_default_address_id', 'area_id', 'address_type_id', 'email_verified_at', 'password', 'enc_password', 'remember_token'
    ];

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

    public function  milkOrder(): HasOne
    {
        return $this->hasOne(MilkOrder::class, "user_id");
    }
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function user_addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, "user_id", "user_id");
    }


    public function defaultAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_default_address_id', "id");
    }
}
