<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'pharmacy_id',
        'category_id',
        'category',
        'title',
        'description',
        'rating',
        'ratings_number',
        'price',
        'discount',
        'about',
        'status',
        'name',
        'type',
        'product_origin',
        'effective_material',
        'color',
        'shape',
        'code',
        'image',
    ];

    public function Category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    //order table relation
    // public function Order()
    // {
    //     return $this->belongsTo(Order::class);
    // }

}
