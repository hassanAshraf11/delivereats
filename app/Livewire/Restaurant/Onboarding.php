<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class Onboarding extends Component
{
    public string $name = '';
    public string $description = '';
    public string $cuisine_type = '';
    public string $address = '';
    public string $opening_time = '09:00';
    public string $closing_time = '23:00';

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'cuisine_type' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'opening_time' => 'required',
            'closing_time' => 'required',
        ]);

        Restaurant::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'description' => $this->description,
            'cuisine_type' => $this->cuisine_type,
            'address' => $this->address,
            'lat' => 30.0444 + (rand(-50, 50) / 1000),
            'lng' => 31.2357 + (rand(-50, 50) / 1000),
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'commission_rate' => 12.00,
            'is_active' => true, // Active immediately, admin can deactivate if needed
        ]);

        session()->flash('success', 'Restaurant registered! It will go live once approved by admin.');
        return redirect()->route('restaurant.dashboard');
    }

    public function render()
    {
        // If user already has a restaurant, redirect
        if (Auth::user()->restaurant) {
            return redirect()->route('restaurant.dashboard');
        }

        return view('livewire.restaurant.onboarding')
            ->layout('components.layouts.guest', ['title' => 'Register Your Restaurant — DeliverEats']);
    }
}
