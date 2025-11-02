<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubcategory extends Model
{
    protected $fillable = [
        'user_id',
        'subcategory_id',
        'priority',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
