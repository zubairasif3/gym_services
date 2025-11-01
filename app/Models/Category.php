<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'display_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function gigs()
    {
        return $this->hasManyThrough(Gig::class, Subcategory::class);
    }
}
