<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PayoutSplit;
use Illuminate\Support\Facades\DB;

class PayoutCalculatorService
{
    /**
     * Calculate and store the payout split for an order.
     */
    public function calculateAndSave(Order $order): PayoutSplit
    {
        return DB::transaction(function () use ($order) {
            $restaurant = $order->restaurant;
            
            // Rider gets the delivery fee completely
            $riderAmount = $order->delivery_fee;
            
            // Subtotal is total amount minus delivery fee
            $subtotal = $order->total_amount - $order->delivery_fee;

            // Restaurant gets subtotal minus their commission rate
            $commissionRate = $restaurant->commission_rate / 100;
            $platformCommission = $subtotal * $commissionRate;
            
            $restaurantAmount = $subtotal - $platformCommission;
            
            // Platform gets the commission
            $platformAmount = $platformCommission;

            return PayoutSplit::create([
                'order_id' => $order->id,
                'platform_amount' => $platformAmount,
                'restaurant_amount' => $restaurantAmount,
                'rider_amount' => $riderAmount,
                'is_paid' => false,
            ]);
        });
    }

    /**
     * Alias for calculateAndSave.
     */
    public function calculate(Order $order): PayoutSplit
    {
        // Don't duplicate if payout already exists
        if ($order->payoutSplit) {
            return $order->payoutSplit;
        }
        return $this->calculateAndSave($order);
    }
}
