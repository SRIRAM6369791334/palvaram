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
    ];

    /**
     * Always return country_id as an array of integers,
     * regardless of how it is stored in the DB (int, JSON, string, array, null).
     */
    public function getCountryIdAttribute($value)
    {
        if (is_array($value)) {
            return array_values(array_unique(array_map('intval', $value)));
        }
        if (is_numeric($value)) {
            return [(int)$value];
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_unique(array_map('intval', $decoded)));
            }
            if (is_numeric($decoded)) {
                return [(int)$decoded];
            }
            $parts = array_filter(explode(',', $value));
            return array_values(array_unique(array_map('intval', $parts)));
        }
        return [];
    }

    /**
     * Always save country_id as valid JSON.
     */
    public function setCountryIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['country_id'] = json_encode(array_values(array_unique(array_map('intval', $value))));
        } elseif (is_numeric($value)) {
            $this->attributes['country_id'] = json_encode([(int)$value]);
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $this->attributes['country_id'] = json_encode(array_values(array_unique(array_map('intval', $decoded))));
            } elseif (is_numeric($decoded)) {
                $this->attributes['country_id'] = json_encode([(int)$decoded]);
            } else {
                $parts = array_filter(explode(',', $value));
                $this->attributes['country_id'] = json_encode(array_values(array_unique(array_map('intval', $parts))));
            }
        } else {
            $this->attributes['country_id'] = json_encode([]);
        }
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
}
