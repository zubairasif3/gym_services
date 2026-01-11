<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'display_order', 'is_active', 'image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function gigs()
    {
        return $this->hasMany(Gig::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'sub_category_id');
    }
}
