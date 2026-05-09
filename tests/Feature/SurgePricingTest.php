<?php

namespace Tests\Feature;

use App\Services\SurgePricingService;
use App\Models\SurgePricingLog;
use App\Strategies\FlatSurgeStrategy;
use App\Strategies\MultiplierSurgeStrategy;
use App\Strategies\TimeBasedSurgeStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurgePricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_surge_returns_base_fee(): void
    {
        $service = new SurgePricingService();
        $fee = $service->calculateFee(15.00);
        $this->assertEquals(15.00, $fee);
    }

    public function test_multiplier_surge_strategy(): void
    {
        $strategy = new MultiplierSurgeStrategy(1.5);
        $result = $strategy->calculateSurgeFee(100.00);
        $this->assertEquals(150.00, $result);
    }

    public function test_multiplier_surge_caps_at_maximum(): void
    {
        // Default max is 2.5, so 5.0 should be capped
        $strategy = new MultiplierSurgeStrategy(5.0);
        $result = $strategy->calculateSurgeFee(100.00);
        $this->assertEquals(250.00, $result); // Capped at 2.5x
    }

    public function test_flat_surge_strategy(): void
    {
        $strategy = new FlatSurgeStrategy(10.00);
        $result = $strategy->calculateSurgeFee(15.00);
        $this->assertEquals(25.00, $result);
    }

    public function test_time_based_strategy_peak_hours(): void
    {
        $strategy = new TimeBasedSurgeStrategy(1.3, [['start' => '00:00', 'end' => '23:59']]);
        $result = $strategy->calculateSurgeFee(100.00);
        // Always peak with 00:00-23:59 window
        $this->assertEquals(130.00, $result);
    }

    public function test_active_surge_modifies_delivery_fee(): void
    {
        SurgePricingLog::create([
            'strategy' => 'multiplier',
            'multiplier' => 2.0,
            'is_active' => true,
            'active_from' => now(),
        ]);

        $service = new SurgePricingService();
        $fee = $service->calculateFee(15.00);
        $this->assertEquals(30.00, $fee);
    }

    public function test_deactivated_surge_returns_base_fee(): void
    {
        SurgePricingLog::create([
            'strategy' => 'multiplier',
            'multiplier' => 2.0,
            'is_active' => false,
            'active_from' => now()->subHour(),
            'active_to' => now(),
        ]);

        $service = new SurgePricingService();
        $fee = $service->calculateFee(15.00);
        $this->assertEquals(15.00, $fee);
    }

    public function test_flat_surge_active(): void
    {
        SurgePricingLog::create([
            'strategy' => 'flat',
            'flat_amount' => 20.00,
            'is_active' => true,
            'active_from' => now(),
        ]);

        $service = new SurgePricingService();
        $fee = $service->calculateFee(15.00);
        $this->assertEquals(35.00, $fee);
    }

    public function test_surge_rollback_on_deactivation(): void
    {
        $log = SurgePricingLog::create([
            'strategy' => 'multiplier',
            'multiplier' => 2.0,
            'is_active' => true,
            'active_from' => now(),
        ]);

        $service = new SurgePricingService();
        $this->assertEquals(30.00, $service->calculateFee(15.00));

        // Deactivate (simulating demand drop)
        $log->update(['is_active' => false, 'active_to' => now()]);

        $service2 = new SurgePricingService();
        $this->assertEquals(15.00, $service2->calculateFee(15.00));
    }
}
