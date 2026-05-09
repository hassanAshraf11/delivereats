<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Services\SurgePricingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cart extends Component
{
    public array $cart = [];
    public string $deliveryAddress = '';
    public string $instructions = '';
    public float $lat = 30.0444;
    public float $lng = 31.2357;

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function updateQuantity(string $key, int $qty)
    {
        if ($qty <= 0) {
            unset($this->cart[$key]);
        } else {
            $this->cart[$key]['quantity'] = $qty;
        }
        session()->put('cart', $this->cart);
    }

    public function removeItem(string $key)
    {
        unset($this->cart[$key]);
        session()->put('cart', $this->cart);
    }

    public function clearCart()
    {
        $this->cart = [];
        session()->forget('cart');
    }

    public function placeOrder(SurgePricingService $surgeService)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (empty($this->cart)) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        $this->validate([
            'deliveryAddress' => 'required|string|min:5',
        ]);

        $restaurantId = collect($this->cart)->first()['restaurant_id'] ?? null;
        if (!$restaurantId) {
            session()->flash('error', 'Invalid cart.');
            return;
        }

        try {
            $order = DB::transaction(function () use ($restaurantId, $surgeService) {
                $baseDeliveryFee = 15.00;
                $finalDeliveryFee = $surgeService->calculateFee($baseDeliveryFee);

                $order = Order::create([
                    'customer_id' => Auth::id(),
                    'restaurant_id' => $restaurantId,
                    'total_amount' => 0,
                    'delivery_fee' => $finalDeliveryFee,
                    'status' => 'placed',
                    'delivery_address' => $this->deliveryAddress,
                    'lat' => $this->lat,
                    'lng' => $this->lng,
                    'instructions' => $this->instructions ?: null,
                ]);

                $subtotal = 0;
                foreach ($this->cart as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $item['menu_item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                $order->update(['total_amount' => $subtotal + $finalDeliveryFee]);

                $order->stateLogs()->create([
                    'new_state' => 'placed',
                    'actor_type' => 'customer',
                    'actor_id' => Auth::id(),
                ]);

                return $order;
            });

            $this->clearCart();
            return redirect()->route('track.order', $order->id);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $subtotal = collect($this->cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $surgeService = app(SurgePricingService::class);
        $deliveryFee = $surgeService->calculateFee(15.00);
        $total = $subtotal + $deliveryFee;
        $restaurantName = null;
        if (!empty($this->cart)) {
            $rid = collect($this->cart)->first()['restaurant_id'] ?? null;
            $restaurantName = $rid ? Restaurant::find($rid)?->name : null;
        }

        return view('livewire.customer.cart', compact('subtotal', 'deliveryFee', 'total', 'restaurantName'))
            ->layout('components.layouts.customer', ['title' => 'Cart — DeliverEats']);
    }
}
