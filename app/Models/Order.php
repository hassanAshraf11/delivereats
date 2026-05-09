<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'customer_id', 'restaurant_id', 'rider_id', 'total_amount',
        'delivery_fee', 'status', 'delivery_address', 'lat', 'lng',
        'instructions'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stateLogs(): HasMany
    {
        return $this->hasMany(OrderStateLog::class);
    }

    public function payoutSplit(): HasOne
    {
        return $this->hasOne(PayoutSplit::class);
    }
}
