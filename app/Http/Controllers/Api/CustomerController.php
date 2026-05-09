<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Browse restaurants with search, filter, and sorting.
     */
    public function restaurants(Request $request)
    {
        $query = Restaurant::where('is_active', true)
            ->withCount('orders')
            ->withAvg('reviews', 'rating');

        // Filter by cuisine
        if ($request->filled('cuisine_type')) {
            $query->where('cuisine_type', $request->cuisine_type);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        if ($sortBy === 'rating') {
            $query->orderByDesc('reviews_avg_rating');
        } elseif ($sortBy === 'orders') {
            $query->orderByDesc('orders_count');
        } else {
            $query->orderBy('name');
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Get restaurant details with full menu.
     */
    public function restaurantMenu($id)
    {
        $restaurant = Restaurant::with(['menuCategories.menuItems' => function ($q) {
            $q->where('is_available', true);
        }, 'menuCategories.menuItems.variants'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        return response()->json($restaurant);
    }

    /**
     * Get reviews for a restaurant.
     */
    public function restaurantReviews($id)
    {
        $reviews = Review::where('reviewable_type', Restaurant::class)
            ->where('reviewable_id', $id)
            ->with('user:id,name')
            ->latest()
            ->paginate(20);

        return response()->json($reviews);
    }

    /**
     * Submit review for a restaurant (after delivery).
     */
    public function submitReview(Request $request, $restaurantId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'order_id' => 'required|exists:orders,id',
        ]);

        // Verify the customer actually ordered from this restaurant
        $order = \App\Models\Order::where('id', $request->order_id)
            ->where('customer_id', $request->user()->id)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'delivered')
            ->firstOrFail();

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'reviewable_id' => $restaurantId,
                'reviewable_type' => Restaurant::class,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return response()->json(['message' => 'Review submitted!', 'review' => $review], 201);
    }

    /**
     * Get available cuisine types for filtering.
     */
    public function cuisines()
    {
        $cuisines = Restaurant::where('is_active', true)
            ->distinct()
            ->pluck('cuisine_type');

        return response()->json($cuisines);
    }
}
