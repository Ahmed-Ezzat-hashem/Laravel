<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pharmacy_id',
        'total_amount',
        'status',
        'customer',
        'tracking_number',
        'country',
        'street_name',
        'city',
        'state_province',
        'zip_code',
        'phone_number',
        'coupon_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
    public function Products()
    {
        return $this->hasMany(Product::class);
    }

}
