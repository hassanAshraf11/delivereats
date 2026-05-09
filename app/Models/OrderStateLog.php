<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStateLog extends Model
{
    protected $fillable = [
        'order_id', 'previous_state', 'new_state', 'actor_type', 'actor_id'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
