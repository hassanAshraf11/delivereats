<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\PayoutSplit;
use App\Models\SurgePricingLog;
use App\Models\OrderStateLog;
use App\StateMachines\OrderStateMachine;
use App\Services\PayoutCalculatorService;

class ControlTower extends Component
{
    use WithPagination;

    public string $activeTab = 'overview';
    public string $orderFilter = 'active';
    public string $searchQuery = '';

    // Surge form
    public string $surgeStrategy = 'multiplier';
    public float $surgeMultiplier = 1.5;
    public float $surgeFlatAmount = 10.0;

    // Payout
    public bool $showPayoutModal = false;
    public ?int $selectedPayoutId = null;

    protected $queryString = ['activeTab'];

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        
        if ($tab === 'livemap') {
            $this->dispatch('init-map');
        }
    }

    public function setOrderFilter(string $filter): void
    {
        $this->orderFilter = $filter;
        $this->resetPage();
    }

    // ── Order Actions ──
    public function cancelOrder(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo('cancelled', 'admin', 1);
            session()->flash('success', "Order #{$orderId} cancelled.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    // ── Rider Actions ──
    public function toggleRiderStatus(int $riderId): void
    {
        $rider = Rider::findOrFail($riderId);
        $rider->is_online = !$rider->is_online;
        $rider->save();
    }

    // ── Surge Pricing ──
    public function activateSurge(): void
    {
        // Deactivate existing
        SurgePricingLog::where('is_active', true)->update(['is_active' => false, 'active_to' => now()]);

        SurgePricingLog::create([
            'strategy' => $this->surgeStrategy,
            'multiplier' => $this->surgeStrategy !== 'flat' ? $this->surgeMultiplier : null,
            'flat_amount' => $this->surgeStrategy === 'flat' ? $this->surgeFlatAmount : null,
            'active_from' => now(),
            'is_active' => true,
        ]);
        session()->flash('success', 'Surge pricing activated!');
    }

    public function deactivateSurge(): void
    {
        SurgePricingLog::where('is_active', true)->update(['is_active' => false, 'active_to' => now()]);
        session()->flash('success', 'Surge pricing deactivated.');
    }

    // ── Payouts ──
    public function markAsPaid(int $payoutId): void
    {
        $payout = PayoutSplit::with('order.restaurant', 'order.rider')->findOrFail($payoutId);
        
        try {
            $stripeService = new \App\Services\StripePaymentService();
            
            // Transfer to Restaurant
            if ($payout->restaurant_amount > 0) {
                // In a real app, restaurant would have a connected stripe_account_id
                $restaurantStripeId = 'acct_simulated_rest_' . $payout->order->restaurant->id;
                $stripeService->transferToConnectedAccount(
                    $payout->restaurant_amount, 
                    $restaurantStripeId, 
                    "Payout for Order #{$payout->order_id}", 
                    $payout->order_id
                );
            }

            // Transfer to Rider
            if ($payout->rider_amount > 0) {
                // In a real app, rider would have a connected stripe_account_id
                $riderStripeId = 'acct_simulated_rider_' . $payout->order->rider->id;
                $stripeService->transferToConnectedAccount(
                    $payout->rider_amount, 
                    $riderStripeId, 
                    "Delivery fee for Order #{$payout->order_id}", 
                    $payout->order_id
                );
            }

            $payout->update(['is_paid' => true]);
            session()->flash('success', 'Stripe transfers processed and payout marked as paid.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function markAsUnpaid(int $payoutId): void
    {
        PayoutSplit::where('id', $payoutId)->update(['is_paid' => false]);
    }

    // ── Restaurant Actions ──
    public function toggleRestaurant(int $restaurantId): void
    {
        $r = Restaurant::findOrFail($restaurantId);
        $r->is_active = !$r->is_active;
        $r->save();
    }

    public function render()
    {
        // Stats
        $totalOrders = Order::count();
        $activeOrders = Order::whereNotIn('status', ['delivered', 'cancelled'])->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $totalRevenue = PayoutSplit::sum('platform_amount');
        $totalRestaurantPayouts = PayoutSplit::sum('restaurant_amount');
        $totalRiderPayouts = PayoutSplit::sum('rider_amount');
        $onlineRiders = Rider::where('is_online', true)->count();
        $totalRiders = Rider::count();
        $activeSurge = SurgePricingLog::where('is_active', true)->first();

        // Orders query
        $ordersQuery = Order::with(['customer', 'restaurant', 'rider', 'items.menuItem']);
        if ($this->orderFilter === 'active') {
            $ordersQuery->whereNotIn('status', ['delivered', 'cancelled']);
        } elseif ($this->orderFilter !== 'all') {
            $ordersQuery->where('status', $this->orderFilter);
        }
        if ($this->searchQuery) {
            $ordersQuery->where(function ($q) {
                $q->where('id', 'like', "%{$this->searchQuery}%")
                  ->orWhere('delivery_address', 'like', "%{$this->searchQuery}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$this->searchQuery}%"));
            });
        }
        $orders = $ordersQuery->latest()->paginate(10);

        // Riders
        $riders = Rider::with('user')->get();

        // Restaurants
        $restaurants = Restaurant::withCount('orders')->with('owner')->get();

        // Payouts
        $payouts = PayoutSplit::with('order.restaurant', 'order.rider')->latest()->paginate(10);

        // Surge logs
        $surgeLogs = SurgePricingLog::latest()->limit(20)->get();

        $recentLogs = OrderStateLog::with('order')->latest()->limit(15)->get();

        if ($this->activeTab === 'livemap') {
            $this->dispatch('init-map');
        }

        return view('livewire.admin.control-tower', compact(
            'totalOrders', 'activeOrders', 'deliveredOrders', 'totalRevenue',
            'totalRestaurantPayouts', 'totalRiderPayouts', 'onlineRiders', 'totalRiders',
            'activeSurge', 'orders', 'riders', 'restaurants', 'payouts', 'surgeLogs', 'recentLogs'
        ))->layout('components.layouts.app', ['title' => 'Admin Control Tower — DeliverEats']);
    }
}
