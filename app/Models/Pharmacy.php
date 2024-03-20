<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function calculateRating()
    {
        $totalRatings = $this->ratings()->count();
        if ($totalRatings > 0) {
            $totalRatingSum = $this->ratings()->sum('rating');
            return $totalRatingSum / $totalRatings;
        } else {
            return 0; // Default rating when there are no ratings yet
        }
    }

    // Accessor for average_rating attribute
    public function getAverageRatingAttribute()
    {
        return $this->calculateRating();
    }
}
