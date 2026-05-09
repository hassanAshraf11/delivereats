<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutSplit extends Model
{
    protected $fillable = [
        'order_id', 'platform_amount', 'restaurant_amount', 'rider_amount', 'is_paid'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
