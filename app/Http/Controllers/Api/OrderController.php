<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Services\SurgePricingService;
use App\Services\PayoutCalculatorService;
use App\StateMachines\OrderStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::with(['items.menuItem', 'restaurant', 'stateLogs']);

        if ($user->role === 'customer') {
            $query->where('customer_id', $user->id);
        } elseif ($user->role === 'rider') {
            $query->where('rider_id', $user->id);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($query->latest()->paginate(15));
    }

    /**
     * Show a specific order with full details.
     */
    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $order = Order::with(['items.menuItem', 'restaurant', 'customer', 'rider', 'stateLogs', 'payoutSplit'])
            ->findOrFail($id);

        // Authorization: customer, rider, restaurant owner, or admin
        if ($user->role === 'customer' && $order->customer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if ($user->role === 'rider' && $order->rider_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($order);
    }

    /**
     * Place a new order with variant pricing support.
     */
    public function placeOrder(Request $request, SurgePricingService $surgeService)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'delivery_address' => 'required|string|max:500',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'instructions' => 'nullable|string|max:300',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.menu_item_variant_id' => 'nullable|exists:menu_item_variants,id',
            'items.*.quantity' => 'required|integer|min:1|max:20',
        ]);

        return DB::transaction(function () use ($validated, $request, $surgeService) {
            $baseDeliveryFee = 15.00;
            $finalDeliveryFee = $surgeService->calculateFee($baseDeliveryFee);

            $order = Order::create([
                'customer_id' => $request->user()->id,
                'restaurant_id' => $validated['restaurant_id'],
                'total_amount' => 0,
                'delivery_fee' => $finalDeliveryFee,
                'status' => 'placed',
                'delivery_address' => $validated['delivery_address'],
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'instructions' => $validated['instructions'] ?? null,
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                $price = $menuItem->base_price;

                // Apply variant price adjustment if selected
                if (!empty($itemData['menu_item_variant_id'])) {
                    $variant = MenuItemVariant::where('id', $itemData['menu_item_variant_id'])
                        ->where('menu_item_id', $menuItem->id)
                        ->first();
                    if ($variant) {
                        $price += $variant->price_adjustment;
                    }
                }

                $subtotal += $price * $itemData['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $itemData['menu_item_id'],
                    'menu_item_variant_id' => $itemData['menu_item_variant_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'price' => $price,
                ]);
            }

            $order->update(['total_amount' => $subtotal + $finalDeliveryFee]);

            // Log initial state
            $order->stateLogs()->create([
                'new_state' => 'placed',
                'actor_type' => 'customer',
                'actor_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Order placed successfully!',
                'order' => $order->load('items.menuItem', 'restaurant'),
            ], 201);
        });
    }

    /**
     * Update order status via the FSM.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|string|in:confirmed,preparing,on_the_way,delivered,cancelled',
        ]);

        $order = Order::with('restaurant')->findOrFail($id);
        $user = $request->user();

        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo($request->status, $user->role, $user->id);

            // Auto-generate payout on delivery
            if ($request->status === 'delivered') {
                $payoutService = app(PayoutCalculatorService::class);
                $payoutService->calculate($order);
            }

            return response()->json([
                'message' => 'Order status updated to: ' . $request->status,
                'order' => $order->fresh()->load('stateLogs'),
            ]);
        } catch (\App\Exceptions\InvalidOrderTransitionException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancel an order (customer only, must be in 'placed' state).
     */
    public function cancel(Request $request, int $id)
    {
        $order = Order::where('customer_id', $request->user()->id)->findOrFail($id);

        if ($order->status !== 'placed') {
            return response()->json(['error' => 'Order can only be cancelled when in "placed" state.'], 422);
        }

        try {
            $sm = new OrderStateMachine($order);
            $sm->transitionTo('cancelled', 'customer', $request->user()->id);

            return response()->json(['message' => 'Order cancelled.', 'order' => $order->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Track order — returns the full state log timeline.
     */
    public function track(Request $request, int $id)
    {
        $order = Order::with(['stateLogs' => fn($q) => $q->orderBy('created_at'), 'rider'])
            ->findOrFail($id);

        return response()->json([
            'order_id' => $order->id,
            'current_status' => $order->status,
            'rider' => $order->rider ? [
                'name' => $order->rider->name,
            ] : null,
            'timeline' => $order->stateLogs->map(fn($log) => [
                'from' => $log->previous_state,
                'to' => $log->new_state,
                'at' => $log->created_at->toIso8601String(),
                'actor' => $log->actor_type,
            ]),
        ]);
    }
}
