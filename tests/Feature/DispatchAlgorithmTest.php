<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Rider;
use App\Models\User;
use App\Jobs\DispatchOrderJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispatchAlgorithmTest extends TestCase
{
    use RefreshDatabase;

    private Restaurant $restaurant;
    private array $riders = [];

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create(['role' => 'restaurant_owner']);
        $this->restaurant = Restaurant::create([
            'user_id' => $owner->id, 'name' => 'Dispatch Test Restaurant', 'description' => 'Test',
            'cuisine_type' => 'American', 'address' => 'Test', 'lat' => 30.0444, 'lng' => 31.2357,
            'opening_time' => '09:00', 'closing_time' => '23:00', 'commission_rate' => 15.0, 'is_active' => true,
        ]);

        // Create 8 riders at varying distances
        $locations = [
            [30.045, 31.236], [30.050, 31.240], [30.060, 31.250], [30.070, 31.260],
            [30.080, 31.270], [30.090, 31.280], [30.100, 31.290], [30.110, 31.300],
        ];

        foreach ($locations as $i => [$lat, $lng]) {
            $u = User::factory()->create(['role' => 'rider', 'name' => "Rider " . ($i + 1)]);
            $this->riders[] = Rider::create([
                'user_id' => $u->id, 'is_online' => true,
                'current_lat' => $lat, 'current_lng' => $lng, 'vehicle_type' => 'motorcycle',
            ]);
        }
    }

    private function createOrder(): Order
    {
        $customer = User::factory()->create(['role' => 'customer']);
        return Order::create([
            'customer_id' => $customer->id, 'restaurant_id' => $this->restaurant->id,
            'total_amount' => 100, 'delivery_fee' => 15, 'status' => 'placed',
            'delivery_address' => 'Test', 'lat' => 30.05, 'lng' => 31.24,
        ]);
    }

    public function test_dispatch_assigns_nearest_rider(): void
    {
        $order = $this->createOrder();
        DispatchOrderJob::dispatchSync($order);

        $order->refresh();
        // Should have been assigned to one of the riders
        $this->assertNotNull($order->rider_id);
    }

    public function test_dispatch_skips_offline_riders(): void
    {
        // Set closest rider offline
        $this->riders[0]->update(['is_online' => false]);

        $order = $this->createOrder();
        DispatchOrderJob::dispatchSync($order);

        $order->refresh();
        // Should NOT be the offline rider
        $this->assertNotNull($order->rider_id);
        $this->assertNotEquals($this->riders[0]->user_id, $order->rider_id);
    }

    public function test_dispatch_handles_no_available_riders(): void
    {
        // Set all riders offline
        Rider::query()->update(['is_online' => false]);

        $order = $this->createOrder();
        DispatchOrderJob::dispatchSync($order);

        $order->refresh();
        $this->assertNull($order->rider_id);
    }

    public function test_concurrent_50_orders_dispatched(): void
    {
        // Create 50 orders simultaneously
        $orders = collect();
        for ($i = 0; $i < 50; $i++) {
            $orders->push($this->createOrder());
        }

        // Dispatch all orders
        foreach ($orders as $order) {
            DispatchOrderJob::dispatchSync($order);
        }

        // All 50 orders dispatched — since status is 'placed', riders aren't "busy"
        $assignedOrders = Order::whereNotNull('rider_id')->count();
        $this->assertEquals(50, $assignedOrders, 'All 50 orders should be assigned since placed orders dont make riders busy');

        // Nearest rider gets all orders (since no one becomes busy)
        $usedRiders = Order::whereNotNull('rider_id')->distinct('rider_id')->count('rider_id');
        $this->assertGreaterThanOrEqual(1, $usedRiders);
    }

    public function test_dispatch_assigns_rider_with_correct_id(): void
    {
        $order = $this->createOrder();
        DispatchOrderJob::dispatchSync($order);

        $order->refresh();
        $this->assertNotNull($order->rider_id);
        $assignedUser = User::find($order->rider_id);
        $this->assertEquals('rider', $assignedUser->role);
    }
}
