<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'bio', 'profile_picture', 'is_provider', 'phone', 'country', 'city', 'languages', 'provider_level', 'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
