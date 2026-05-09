<?php

namespace App\Strategies;

use App\Models\Order;

interface SurgePricingStrategy
{
    /**
     * Calculate the new delivery fee based on the active surge pricing.
     */
    public function calculateSurgeFee(float $baseDeliveryFee): float;
}
