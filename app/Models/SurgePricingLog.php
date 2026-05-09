<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurgePricingLog extends Model
{
    protected $fillable = [
        'strategy', 'multiplier', 'flat_amount', 'active_from', 'active_to', 'is_active'
    ];

    protected $casts = [
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'is_active' => 'boolean',
    ];
}
