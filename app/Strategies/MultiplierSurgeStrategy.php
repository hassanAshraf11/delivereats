<?php

namespace App\Strategies;

class MultiplierSurgeStrategy implements SurgePricingStrategy
{
    private float $multiplier;
    private float $maxMultiplier;

    public function __construct(float $multiplier, float $maxMultiplier = 2.5)
    {
        $this->multiplier = min($multiplier, $maxMultiplier);
        $this->maxMultiplier = $maxMultiplier;
    }

    public function calculateSurgeFee(float $baseDeliveryFee): float
    {
        return $baseDeliveryFee * $this->multiplier;
    }
}
