<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rider extends Model
{
    protected $fillable = [
        'user_id', 'is_online', 'current_lat', 'current_lng', 'vehicle_type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
