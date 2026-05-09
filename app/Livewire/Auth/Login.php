<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $user = Auth::user();

            return match($user->role) {
                'admin' => redirect()->route('admin.tower'),
                'restaurant_owner' => redirect()->route('restaurant.dashboard'),
                'customer' => redirect()->route('browse'),
                'rider' => redirect()->route('browse'),
                default => redirect('/'),
            };
        }

        $this->addError('email', 'Invalid credentials.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest', ['title' => 'Login — DeliverEats']);
    }
}
