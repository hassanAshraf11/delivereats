<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Restaurant;
use App\Models\Review;

class RestaurantPage extends Component
{
    public int $restaurantId;
    public array $cart = [];

    public function mount(int $id)
    {
        $this->restaurantId = $id;
        $this->cart = session()->get('cart', []);
    }

    public function addToCart(int $menuItemId, string $name, float $price, int $qty = 1)
    {
        $key = 'item_' . $menuItemId;
        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity'] += $qty;
        } else {
            $this->cart[$key] = [
                'menu_item_id' => $menuItemId,
                'restaurant_id' => $this->restaurantId,
                'name' => $name,
                'price' => $price,
                'quantity' => $qty,
            ];
        }
        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');
    }

    public function removeFromCart(string $key)
    {
        unset($this->cart[$key]);
        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $restaurant = Restaurant::with(['menuCategories.menuItems' => function ($q) {
            $q->where('is_available', true);
        }, 'menuCategories.menuItems.variants'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($this->restaurantId);

        $reviews = Review::where('reviewable_type', Restaurant::class)
            ->where('reviewable_id', $this->restaurantId)
            ->with('user')->latest()->limit(10)->get();

        $cartTotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $cartCount = collect($this->cart)->sum('quantity');

        return view('livewire.customer.restaurant-page', compact('restaurant', 'reviews', 'cartTotal', 'cartCount'))
            ->layout('components.layouts.customer', ['title' => $restaurant->name . ' — DeliverEats']);
    }
}
