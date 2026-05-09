<?php

namespace App\Strategies;

use Carbon\Carbon;

class TimeBasedSurgeStrategy implements SurgePricingStrategy
{
    private float $peakMultiplier;
    private array $peakHours; // Format: [['start' => '18:00', 'end' => '21:00']]

    public function __construct(float $peakMultiplier, array $peakHours = [])
    {
        $this->peakMultiplier = $peakMultiplier;
        $this->peakHours = $peakHours;
    }

    public function calculateSurgeFee(float $baseDeliveryFee): float
    {
        $now = Carbon::now();
        $isPeak = false;

        foreach ($this->peakHours as $window) {
            $start = Carbon::createFromTimeString($window['start']);
            $end = Carbon::createFromTimeString($window['end']);
            
            if ($now->between($start, $end)) {
                $isPeak = true;
                break;
            }
        }

        if ($isPeak) {
            return $baseDeliveryFee * $this->peakMultiplier;
        }

        return $baseDeliveryFee;
    }
}
