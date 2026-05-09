<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use App\Models\Review;
use App\Models\Restaurant;
use App\StateMachines\OrderStateMachine;
use Illuminate\Support\Facades\Auth;

class TrackOrder extends Component
{
    public int $orderId;
    public int $restaurantRating = 0;
    public string $restaurantComment = '';
    public int $riderRating = 0;
    public string $riderComment = '';
    public bool $showReviewForm = false;

    public function mount(int $id)
    {
        $this->orderId = $id;
    }

    public function cancelOrder()
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($this->orderId);
        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo('cancelled', 'customer', Auth::id());
            session()->flash('success', 'Order cancelled.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function submitReview()
    {
        $order = Order::findOrFail($this->orderId);
        $reviewed = false;

        if ($this->restaurantRating > 0) {
            $this->validate(['restaurantRating' => 'integer|min:1|max:5']);
            Review::updateOrCreate(
                ['user_id' => Auth::id(), 'reviewable_id' => $order->restaurant_id, 'reviewable_type' => Restaurant::class],
                ['rating' => $this->restaurantRating, 'comment' => $this->restaurantComment]
            );
            $reviewed = true;
        }

        if ($this->riderRating > 0 && $order->rider_id) {
            $this->validate(['riderRating' => 'integer|min:1|max:5']);
            // Rider reviews belong to the Rider model, but reviewable_id is user_id or rider_id? 
            // In Rider API, review is on Restaurant, not Rider. Let's review the Rider model itself.
            Review::updateOrCreate(
                ['user_id' => Auth::id(), 'reviewable_id' => $order->rider_id, 'reviewable_type' => \App\Models\Rider::class],
                ['rating' => $this->riderRating, 'comment' => $this->riderComment]
            );
            $reviewed = true;
        }

        if ($reviewed) {
            $this->showReviewForm = false;
            session()->flash('success', 'Thank you for your review!');
        } else {
            session()->flash('error', 'Please provide a rating before submitting.');
        }
    }

    public function render()
    {
        $order = Order::where('customer_id', Auth::id())
            ->with(['restaurant', 'items.menuItem', 'rider', 'stateLogs' => fn($q) => $q->orderBy('created_at')])
            ->findOrFail($this->orderId);

        $hasReviewed = Review::where('user_id', Auth::id())
            ->where('reviewable_id', $order->restaurant_id)
            ->where('reviewable_type', Restaurant::class)
            ->exists();

        $this->dispatch('init-track-map');

        return view('livewire.customer.track-order', compact('order', 'hasReviewed'))
            ->layout('components.layouts.customer', ['title' => 'Track Order #' . $order->id . ' — DeliverEats']);
    }
}
