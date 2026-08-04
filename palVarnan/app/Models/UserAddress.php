<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'address_username',
        'address_first_name',
        'address_last_name',
        'phone_code',
        'address_line_one',
        'address_line_two',
        'landmark',
        'pincode',
        'area_id',
        'city',
        'area_name',
        'state',
        'address_phone_number',
        'address_type_id',
        'address_type_name',
        'address_type_others_name',
        'email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, "area_id", "id");
    }

    // In UserAddress.php model
public function country()
{
    return $this->belongsTo(Country::class, 'area_id');
}

}
