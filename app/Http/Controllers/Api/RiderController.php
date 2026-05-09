<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rider;
use App\Models\Review;
use App\StateMachines\OrderStateMachine;
use App\Services\PayoutCalculatorService;
use Illuminate\Http\Request;

class RiderController extends Controller
{
    /**
     * Go online / offline.
     */
    public function toggleOnline(Request $request)
    {
        $rider = Rider::where('user_id', $request->user()->id)->firstOrFail();
        $rider->is_online = !$rider->is_online;
        $rider->save();

        return response()->json([
            'message' => $rider->is_online ? 'You are now online' : 'You are now offline',
            'is_online' => $rider->is_online,
        ]);
    }

    /**
     * Update GPS location.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $rider = Rider::where('user_id', $request->user()->id)->firstOrFail();
        $rider->update([
            'current_lat' => $request->lat,
            'current_lng' => $request->lng,
        ]);

        return response()->json(['message' => 'Location updated']);
    }

    /**
     * Get assigned orders for this rider.
     */
    public function myOrders(Request $request)
    {
        $orders = Order::where('rider_id', $request->user()->id)
            ->with(['restaurant', 'customer', 'items.menuItem'])
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Pick up order (transition to on_the_way).
     */
    public function pickupOrder(Request $request, int $orderId)
    {
        $order = Order::where('rider_id', $request->user()->id)->findOrFail($orderId);

        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo('on_the_way', 'rider', $request->user()->id);

            return response()->json([
                'message' => 'Order picked up, on the way!',
                'order' => $order->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Deliver order (transition to delivered) + generate payout split.
     */
    public function deliverOrder(Request $request, int $orderId)
    {
        $order = Order::where('rider_id', $request->user()->id)
            ->with('restaurant')
            ->findOrFail($orderId);

        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo('delivered', 'rider', $request->user()->id);

            // Generate payout split
            $payoutService = app(PayoutCalculatorService::class);
            $payoutService->calculate($order);

            return response()->json([
                'message' => 'Order delivered successfully!',
                'order' => $order->fresh()->load('payoutSplit'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Submit a review for a restaurant after delivery.
     */
    public function reviewRestaurant(Request $request, int $orderId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $order = Order::where('rider_id', $request->user()->id)
            ->where('status', 'delivered')
            ->findOrFail($orderId);

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'reviewable_id' => $order->restaurant_id,
                'reviewable_type' => \App\Models\Restaurant::class,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return response()->json(['message' => 'Review submitted', 'review' => $review]);
    }

    /**
     * Get rider profile and stats.
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $rider = Rider::where('user_id', $user->id)->firstOrFail();

        $deliveredCount = Order::where('rider_id', $user->id)->where('status', 'delivered')->count();
        $totalEarnings = \App\Models\PayoutSplit::whereHas('order', fn($q) => $q->where('rider_id', $user->id))->sum('rider_amount');

        $avgRating = Review::where('reviewable_id', $user->id)
            ->where('reviewable_type', \App\Models\User::class)
            ->avg('rating');

        return response()->json([
            'user' => $user,
            'rider' => $rider,
            'stats' => [
                'deliveries' => $deliveredCount,
                'total_earnings' => round($totalEarnings, 2),
                'average_rating' => $avgRating ? round($avgRating, 1) : null,
            ],
        ]);
    }
}
