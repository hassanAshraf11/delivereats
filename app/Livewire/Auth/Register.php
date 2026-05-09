<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'customer';

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:customer,restaurant_owner,rider',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        if ($this->role === 'rider') {
            \App\Models\Rider::create([
                'user_id' => $user->id,
                'is_online' => false,
            ]);
        }

        session()->flash('success', 'Account created successfully! Please login to continue.');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.guest', ['title' => 'Register — DeliverEats']);
    }
}
