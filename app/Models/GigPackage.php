<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigPackage extends Model
{
    protected $fillable = [
        'gig_id', 'package_type', 'title', 'description', 'price', 'delivery_time',
        'revision_limit', 'features'
    ];
    public function gig()
    {
        return $this->belongsTo(Gig::class);
    }
}
