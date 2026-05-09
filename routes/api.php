<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RiderController;

// ── Public ──
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ── Authenticated ──
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // ── Customer Endpoints ──
    Route::get('/restaurants', [CustomerController::class, 'restaurants']);
    Route::get('/restaurants/{id}/menu', [CustomerController::class, 'restaurantMenu']);
    Route::get('/restaurants/{id}/reviews', [CustomerController::class, 'restaurantReviews']);
    Route::post('/restaurants/{id}/reviews', [CustomerController::class, 'submitReview']);
    Route::get('/cuisines', [CustomerController::class, 'cuisines']);

    // ── Order Endpoints ──
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'placeOrder']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{id}/track', [OrderController::class, 'track']);

    // ── Rider Endpoints ──
    Route::prefix('rider')->group(function () {
        Route::get('/profile', [RiderController::class, 'profile']);
        Route::post('/toggle-online', [RiderController::class, 'toggleOnline']);
        Route::post('/location', [RiderController::class, 'updateLocation']);
        Route::get('/orders', [RiderController::class, 'myOrders']);
        Route::post('/orders/{id}/pickup', [RiderController::class, 'pickupOrder']);
        Route::post('/orders/{id}/deliver', [RiderController::class, 'deliverOrder']);
        Route::post('/orders/{id}/review', [RiderController::class, 'reviewRestaurant']);
    });
});
