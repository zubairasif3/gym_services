<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigTag extends Model
{
    protected $fillable = [
        'gig_id', 'tag'
    ];

    public function gig()
    {
        return $this->belongsTo(Gig::class);
    }
}
