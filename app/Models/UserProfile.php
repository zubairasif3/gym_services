<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'bio', 'profile_picture', 'is_provider', 'phone', 'country', 'city', 'address', 'cap', 'date_of_birth', 'languages', 'provider_level', 'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subcategories()
    {
        return $this->hasManyThrough(
            \App\Models\Subcategory::class,
            \App\Models\UserSubcategory::class,
            'user_id',
            'id',
            'user_id',
            'subcategory_id'
        );
    }
}
