<?php

namespace App\Strategies;

class FlatSurgeStrategy implements SurgePricingStrategy
{
    private float $flatAmount;

    public function __construct(float $flatAmount)
    {
        $this->flatAmount = $flatAmount;
    }

    public function calculateSurgeFee(float $baseDeliveryFee): float
    {
        return $baseDeliveryFee + $this->flatAmount;
    }
}
