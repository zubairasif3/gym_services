<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gig extends Model
{
    protected $fillable = [
        'user_id', 'subcategory_id', 'title', 'slug', 'description', 'about',
        'starting_price', 'thumbnail', 'delivery_time', 'requirements', 'skills',
        'experience_level', 'what_included', 'what_not_included',
        'is_featured', 'is_active', 'impressions', 'clicks', 'rating', 'ratings_count'
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(GigImage::class);
    }
    public function packages()
    {
        return $this->hasMany(GigPackage::class);
    }
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
