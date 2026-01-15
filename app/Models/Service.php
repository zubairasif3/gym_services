<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'price',
        'description',
        'delivery',
        'category_id',
        'sub_category_id',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'delivery' => 'integer',
    ];

    /**
     * Get the user who owns this service
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category this service belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory this service belongs to
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'sub_category_id');
    }

    /**
     * Get the promotion for this service
     */
    public function promotion()
    {
        return $this->hasOne(Promotion::class);
    }

    /**
     * Scope to get active services only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
