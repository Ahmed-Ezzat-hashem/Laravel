<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'pharmacy_id',
        'user_name',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'role',

        'google_id',
        'google_token',
        'facebook_id',
        'facebook_token',

        'company_name',
        'company_phone',
        'delivary_area',
        'company_working_hours',
        'company_manager_name',
        'company_manager_phone',
        'commercial_register',
        'tax_card',
        'company_license',
        'remember_token'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
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

    public function favoriteUsers()
    {
        return $this->hasMany(FavoriteProduct::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function pharmacy()
    {
        return $this->hasOne(Pharmacy::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function favoriteProducts()
    {
        return $this->hasMany(FavoriteProduct::class);
    }
}
