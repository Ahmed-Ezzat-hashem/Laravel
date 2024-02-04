<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category',
        'title',
        'description',
        'rating',
        'ratings_number',
        'price',
        'discount',
        'About',
        'status',
        'name',
        'type',
        'product_origin',
        'effective_material',
        'color',
        'shap',
        'code',
    ];

    public function Category()
    {
        return $this->belongsTo(Category::class);
    }
    //order table relation
    // public function Order()
    // {
    //     return $this->belongsTo(Order::class);
    // }


    public function Images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
