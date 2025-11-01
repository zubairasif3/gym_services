<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'gig_id',
        'rate_per_impression',
        'impressions',
        'is_active',
    ];

    public function gig()
    {
        return $this->belongsTo(Gig::class);
    }
}
