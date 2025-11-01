<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigImage extends Model
{
    protected $fillable = [
        'gig_id', 'image_path', 'display_order'
    ];
    public function gig()
    {
        return $this->belongsTo(Gig::class);
    }
}
