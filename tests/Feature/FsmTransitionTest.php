<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Rider;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\StateMachines\OrderStateMachine;
use App\Exceptions\InvalidOrderTransitionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FsmTransitionTest extends TestCase
{
    use RefreshDatabase;

    private function createOrder(string $status = 'placed'): Order
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $owner = User::factory()->create(['role' => 'restaurant_owner']);
        $restaurant = Restaurant::create([
            'user_id' => $owner->id, 'name' => 'Test Restaurant', 'description' => 'Test',
            'cuisine_type' => 'American', 'address' => '123 Test St', 'lat' => 30.0, 'lng' => 31.2,
            'opening_time' => '09:00', 'closing_time' => '23:00', 'commission_rate' => 15.0, 'is_active' => true,
        ]);
        return Order::create([
            'customer_id' => $customer->id, 'restaurant_id' => $restaurant->id,
            'total_amount' => 100, 'delivery_fee' => 15, 'status' => $status,
            'delivery_address' => '456 Delivery Ave', 'lat' => 30.05, 'lng' => 31.25,
        ]);
    }

    public function test_valid_transition_placed_to_confirmed(): void
    {
        $order = $this->createOrder('placed');
        $sm = new OrderStateMachine($order);
        $sm->transitionTo('confirmed', 'restaurant', 1);
        $this->assertEquals('confirmed', $order->fresh()->status);
    }

    public function test_valid_transition_confirmed_to_preparing(): void
    {
        $order = $this->createOrder('confirmed');
        $sm = new OrderStateMachine($order);
        $sm->transitionTo('preparing', 'restaurant', 1);
        $this->assertEquals('preparing', $order->fresh()->status);
    }

    public function test_valid_transition_preparing_to_on_the_way(): void
    {
        $order = $this->createOrder('preparing');
        $sm = new OrderStateMachine($order);
        $sm->transitionTo('on_the_way', 'rider', 1);
        $this->assertEquals('on_the_way', $order->fresh()->status);
    }

    public function test_valid_transition_on_the_way_to_delivered(): void
    {
        $order = $this->createOrder('on_the_way');
        $sm = new OrderStateMachine($order);
        $sm->transitionTo('delivered', 'rider', 1);
        $this->assertEquals('delivered', $order->fresh()->status);
    }

    public function test_invalid_transition_delivered_to_preparing_throws(): void
    {
        $order = $this->createOrder('delivered');
        $sm = new OrderStateMachine($order);
        $this->expectException(InvalidOrderTransitionException::class);
        $sm->transitionTo('preparing', 'restaurant', 1);
    }

    public function test_invalid_transition_cancelled_to_confirmed_throws(): void
    {
        $order = $this->createOrder('cancelled');
        $sm = new OrderStateMachine($order);
        $this->expectException(InvalidOrderTransitionException::class);
        $sm->transitionTo('confirmed', 'restaurant', 1);
    }

    public function test_invalid_transition_placed_to_delivered_throws(): void
    {
        $order = $this->createOrder('placed');
        $sm = new OrderStateMachine($order);
        $this->expectException(InvalidOrderTransitionException::class);
        $sm->transitionTo('delivered', 'rider', 1);
    }

    public function test_invalid_transition_confirmed_to_delivered_throws(): void
    {
        $order = $this->createOrder('confirmed');
        $sm = new OrderStateMachine($order);
        $this->expectException(InvalidOrderTransitionException::class);
        $sm->transitionTo('delivered', 'rider', 1);
    }

    public function test_transition_creates_state_log(): void
    {
        $order = $this->createOrder('placed');
        $sm = new OrderStateMachine($order);
        $sm->transitionTo('confirmed', 'restaurant', 99);

        $this->assertDatabaseHas('order_state_logs', [
            'order_id' => $order->id,
            'previous_state' => 'placed',
            'new_state' => 'confirmed',
            'actor_type' => 'restaurant',
            'actor_id' => 99,
        ]);
    }

    public function test_cancellation_allowed_from_placed(): void
    {
        $order = $this->createOrder('placed');
        $sm = new OrderStateMachine($order);
        $sm->transitionTo('cancelled', 'customer', 1);
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    public function test_full_lifecycle_happy_path(): void
    {
        $order = $this->createOrder('placed');
        $sm = new OrderStateMachine($order);

        $sm->transitionTo('confirmed', 'restaurant', 1);
        $this->assertEquals('confirmed', $order->fresh()->status);

        $sm = new OrderStateMachine($order->fresh());
        $sm->transitionTo('preparing', 'restaurant', 1);
        $this->assertEquals('preparing', $order->fresh()->status);

        $sm = new OrderStateMachine($order->fresh());
        $sm->transitionTo('on_the_way', 'rider', 1);
        $this->assertEquals('on_the_way', $order->fresh()->status);

        $sm = new OrderStateMachine($order->fresh());
        $sm->transitionTo('delivered', 'rider', 1);
        $this->assertEquals('delivered', $order->fresh()->status);

        // Verify all 4 transitions were logged
        $this->assertEquals(4, $order->stateLogs()->count());
    }
}
