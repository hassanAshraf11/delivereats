<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\PayoutCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutSplitTest extends TestCase
{
    use RefreshDatabase;

    private function createDeliveredOrder(float $total, float $deliveryFee, float $commissionRate): Order
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $owner = User::factory()->create(['role' => 'restaurant_owner']);
        $rider = User::factory()->create(['role' => 'rider']);
        $restaurant = Restaurant::create([
            'user_id' => $owner->id, 'name' => 'Test', 'description' => 'Test',
            'cuisine_type' => 'American', 'address' => 'Test', 'lat' => 30.0, 'lng' => 31.2,
            'opening_time' => '09:00', 'closing_time' => '23:00',
            'commission_rate' => $commissionRate, 'is_active' => true,
        ]);
        return Order::create([
            'customer_id' => $customer->id, 'restaurant_id' => $restaurant->id,
            'rider_id' => $rider->id, 'total_amount' => $total, 'delivery_fee' => $deliveryFee,
            'status' => 'delivered', 'delivery_address' => 'Test', 'lat' => 30.05, 'lng' => 31.25,
        ]);
    }

    public function test_payout_split_with_15_percent_commission(): void
    {
        $order = $this->createDeliveredOrder(115.00, 15.00, 15.00);
        $service = new PayoutCalculatorService();
        $payout = $service->calculateAndSave($order);

        // Subtotal = 115 - 15 = 100
        // Platform = 100 * 0.15 = 15
        // Restaurant = 100 - 15 = 85
        // Rider = 15 (delivery fee)
        $this->assertEquals(15.00, $payout->platform_amount);
        $this->assertEquals(85.00, $payout->restaurant_amount);
        $this->assertEquals(15.00, $payout->rider_amount);
        $this->assertFalse($payout->is_paid);
    }

    public function test_payout_split_with_20_percent_commission(): void
    {
        $order = $this->createDeliveredOrder(220.00, 20.00, 20.00);
        $service = new PayoutCalculatorService();
        $payout = $service->calculateAndSave($order);

        // Subtotal = 220 - 20 = 200
        // Platform = 200 * 0.20 = 40
        // Restaurant = 200 - 40 = 160
        // Rider = 20
        $this->assertEquals(40.00, $payout->platform_amount);
        $this->assertEquals(160.00, $payout->restaurant_amount);
        $this->assertEquals(20.00, $payout->rider_amount);
    }

    public function test_payout_split_with_10_percent_commission(): void
    {
        $order = $this->createDeliveredOrder(110.00, 10.00, 10.00);
        $service = new PayoutCalculatorService();
        $payout = $service->calculateAndSave($order);

        // Subtotal = 100, Platform = 10, Restaurant = 90, Rider = 10
        $this->assertEquals(10.00, $payout->platform_amount);
        $this->assertEquals(90.00, $payout->restaurant_amount);
        $this->assertEquals(10.00, $payout->rider_amount);
    }

    public function test_total_payout_equals_order_total(): void
    {
        $order = $this->createDeliveredOrder(150.00, 15.00, 12.00);
        $service = new PayoutCalculatorService();
        $payout = $service->calculateAndSave($order);

        $payoutTotal = $payout->platform_amount + $payout->restaurant_amount + $payout->rider_amount;
        $this->assertEquals($order->total_amount, $payoutTotal);
    }

    public function test_duplicate_payout_prevented(): void
    {
        $order = $this->createDeliveredOrder(115.00, 15.00, 15.00);
        $service = new PayoutCalculatorService();

        $payout1 = $service->calculateAndSave($order);
        $payout2 = $service->calculate($order);

        // calculate() should return existing, not create duplicate
        $this->assertEquals($payout1->id, $payout2->id);
        $this->assertEquals(1, $order->fresh()->payoutSplit()->count());
    }

    public function test_payout_split_stored_in_database(): void
    {
        $order = $this->createDeliveredOrder(115.00, 15.00, 15.00);
        $service = new PayoutCalculatorService();
        $service->calculateAndSave($order);

        $this->assertDatabaseHas('payout_splits', [
            'order_id' => $order->id,
            'is_paid' => false,
        ]);
    }
}
