<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SurgePricingLog;
use App\Strategies\SurgePricingStrategy;
use App\Strategies\FlatSurgeStrategy;
use App\Strategies\MultiplierSurgeStrategy;
use App\Strategies\TimeBasedSurgeStrategy;

class SurgePricingService
{
    private SurgePricingStrategy $strategy;

    public function setStrategy(SurgePricingStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * Calculate the final delivery fee and log if a surge was applied.
     */
    public function calculateFee(float $baseFee): float
    {
        if (!isset($this->strategy)) {
            $this->determineAndSetStrategy();
        }

        if (!isset($this->strategy)) {
            return $baseFee; // No surge active
        }

        $newFee = $this->strategy->calculateSurgeFee($baseFee);

        // Here we could log the surge application to the database if required.
        return $newFee;
    }

    /**
     * Determines the active strategy based on system conditions.
     * This checks the active surge pricing logs from the DB.
     */
    private function determineAndSetStrategy(): void
    {
        $activeSurge = SurgePricingLog::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('active_to')
                      ->orWhere('active_to', '>=', now());
            })
            ->latest()
            ->first();

        if (!$activeSurge) {
            return;
        }

        switch ($activeSurge->strategy) {
            case 'flat':
                $this->setStrategy(new FlatSurgeStrategy((float)$activeSurge->flat_amount));
                break;
            case 'multiplier':
                $this->setStrategy(new MultiplierSurgeStrategy((float)$activeSurge->multiplier));
                break;
            case 'time_based':
                // For simplicity, we pass a default peak hour window. In production, this would be configurable.
                $this->setStrategy(new TimeBasedSurgeStrategy((float)$activeSurge->multiplier, [
                    ['start' => '17:00', 'end' => '21:00']
                ]));
                break;
        }
    }
}
