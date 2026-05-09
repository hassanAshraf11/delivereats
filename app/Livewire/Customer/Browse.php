<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Restaurant;

class Browse extends Component
{
    public string $search = '';
    public string $cuisine = '';
    public string $sort = 'name';

    public function render()
    {
        $cuisines = Restaurant::where('is_active', true)->distinct()->pluck('cuisine_type');

        $query = Restaurant::where('is_active', true)
            ->withCount('orders')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        if ($this->cuisine) {
            $query->where('cuisine_type', $this->cuisine);
        }

        $query->orderBy($this->sort === 'rating' ? 'reviews_avg_rating' : 'name', $this->sort === 'rating' ? 'desc' : 'asc');

        $restaurants = $query->get();

        return view('livewire.customer.browse', compact('restaurants', 'cuisines'))
            ->layout('components.layouts.customer', ['title' => 'Browse Restaurants — DeliverEats']);
    }
}
