<?php

namespace App\Livewire\Rider;

use Livewire\Component;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Review;
use App\Models\PayoutSplit;
use App\StateMachines\OrderStateMachine;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public string $activeTab = 'overview';
    public ?int $riderId = null;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->rider) {
            $this->riderId = $user->rider->id;
        }
    }

    public function switchTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function toggleStatus()
    {
        if ($this->riderId) {
            $rider = Rider::find($this->riderId);
            $rider->is_online = !$rider->is_online;
            $rider->save();
            session()->flash('success', $rider->is_online ? 'You are now online and ready for deliveries!' : 'You are now offline.');
        }
    }

    public function pickupOrder(int $orderId)
    {
        $order = Order::where('rider_id', Auth::id())->findOrFail($orderId);
        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo('on_the_way', 'rider', Auth::id());
            session()->flash('success', "Order #{$orderId} picked up! Head to the delivery address.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function deliverOrder(int $orderId)
    {
        $order = Order::where('rider_id', Auth::id())->findOrFail($orderId);
        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo('delivered', 'rider', Auth::id());

            // Generate payout split
            $payoutService = new \App\Services\PayoutCalculatorService();
            $payoutService->calculate($order->fresh());

            session()->flash('success', "Order #{$orderId} delivered successfully! Payout recorded.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $rider = Rider::find($this->riderId);

        $activeOrders = collect();
        $pastOrders = collect();
        $payouts = collect();
        $totalDeliveries = 0;
        $todayDeliveries = 0;
        $todayEarnings = 0;
        $totalEarnings = 0;
        $avgRating = null;
        $reviewCount = 0;
        $recentReviews = collect();

        if ($rider) {
            $orders = Order::where('rider_id', Auth::id())
                ->with(['restaurant', 'customer', 'items.menuItem'])
                ->latest()
                ->get();

            $activeOrders = $orders->whereIn('status', ['confirmed', 'preparing', 'on_the_way']);
            $pastOrders = $orders->whereIn('status', ['delivered', 'cancelled']);
            $totalDeliveries = $orders->where('status', 'delivered')->count();
            $todayDeliveries = $orders->where('status', 'delivered')
                ->where('updated_at', '>=', now()->startOfDay())->count();

            $payouts = PayoutSplit::with('order.restaurant')
                ->whereHas('order', fn($q) => $q->where('rider_id', Auth::id()))
                ->latest()
                ->get();

            $totalEarnings = $payouts->sum('rider_amount');
            $todayEarnings = $payouts->filter(fn($p) => $p->created_at->isToday())->sum('rider_amount');

            // Reviews for this rider
            $reviews = Review::where('reviewable_type', Rider::class)
                ->where('reviewable_id', $rider->id)
                ->with('user')
                ->latest()
                ->get();
            $avgRating = $reviews->avg('rating');
            $reviewCount = $reviews->count();
            $recentReviews = $reviews->take(10);
        }

        return view('livewire.rider.dashboard', compact(
            'rider', 'activeOrders', 'pastOrders', 'payouts',
            'totalDeliveries', 'todayDeliveries', 'todayEarnings', 'totalEarnings',
            'avgRating', 'reviewCount', 'recentReviews'
        ))->layout('components.layouts.app', ['title' => 'Rider Dashboard — DeliverEats']);
    }
}
