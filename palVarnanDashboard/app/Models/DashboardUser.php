<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class DashboardUser extends Authenticatable
{
    use  HasFactory, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'empl_num',
        'email',
        "phone_number",
        "role",
        "status",
        "enc_password",
        'password',
        'country_id'
    ];

    protected $guard_name = 'web';
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
        'country_id' => 'array'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
}
