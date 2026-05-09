<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_category_id', 'name', 'description', 'image', 'base_price',
        'preparation_time', 'is_available'
    ];

    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MenuItemVariant::class);
    }
}
