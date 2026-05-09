<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class MyOrders extends Component
{
    public function render()
    {
        $orders = Order::where('customer_id', Auth::id())
            ->with(['restaurant', 'items.menuItem', 'rider'])
            ->latest()
            ->get();

        return view('livewire.customer.my-orders', compact('orders'))
            ->layout('components.layouts.customer', ['title' => 'My Orders — DeliverEats']);
    }
}
