<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Admin\ControlTower;
use App\Livewire\Restaurant\Dashboard;
use App\Livewire\Restaurant\Onboarding;
use App\Livewire\Customer\Browse;
use App\Livewire\Customer\RestaurantPage;
use App\Livewire\Customer\Cart;
use App\Livewire\Customer\MyOrders;
use App\Livewire\Customer\TrackOrder;

// ── Public ──
Route::get('/', function () {
    if (Auth::check()) {
        return match(Auth::user()->role) {
            'admin' => redirect()->route('admin.tower'),
            'restaurant_owner' => redirect()->route('restaurant.dashboard'),
            'rider' => redirect()->route('rider.dashboard'),
            default => redirect()->route('browse'),
        };
    }
    return view('welcome');
});

// ── Auth ──
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ── Customer (public browsing, auth for ordering) ──
Route::middleware('redirect.role:customer')->group(function () {
    Route::get('/browse', Browse::class)->name('browse');
    Route::get('/restaurant/{id}', RestaurantPage::class)->name('restaurant.show')->where('id', '[0-9]+');
    Route::get('/cart', Cart::class)->name('cart');
});

Route::middleware(['auth', 'redirect.role:customer'])->group(function () {
    Route::get('/my-orders', MyOrders::class)->name('my.orders');
    Route::get('/my-orders/{id}', TrackOrder::class)->name('track.order');
});

// ── Restaurant Owner ──
Route::middleware(['auth', 'redirect.role:restaurant_owner,admin'])->group(function () {
    Route::get('/restaurant/onboarding', Onboarding::class)->name('restaurant.onboarding');
    Route::get('/restaurant/dashboard', \App\Livewire\Restaurant\Dashboard::class)->name('restaurant.dashboard');
});

// ── Rider ──
Route::middleware(['auth', 'redirect.role:rider,admin'])->group(function () {
    Route::get('/rider/dashboard', \App\Livewire\Rider\Dashboard::class)->name('rider.dashboard');
});

// ── Admin ──
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/tower', ControlTower::class)->name('admin.tower');
});
