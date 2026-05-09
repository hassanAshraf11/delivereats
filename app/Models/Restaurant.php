<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'logo', 'cover', 'cuisine_type',
        'address', 'lat', 'lng', 'opening_time', 'closing_time',
        'commission_rate', 'is_active'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function menuItems()
    {
        return $this->hasManyThrough(MenuItem::class, MenuCategory::class);
    }
}
