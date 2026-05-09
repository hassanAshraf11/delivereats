<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Transfer;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    public function __construct()
    {
        // Use a test key or environment variable. 
        // In a real app, this would be config('services.stripe.secret')
        Stripe::setApiKey(env('STRIPE_SECRET', 'sk_test_simulated_key_for_demo'));
    }

    /**
     * Simulate creating a transfer to a connected Stripe account.
     * 
     * @param float $amount The amount to transfer in EGP
     * @param string $destinationAccountId The connected Stripe account ID (e.g., acct_123)
     * @param string $description Description for the transfer
     * @param int $orderId Reference to the internal order ID
     * @return bool True if successful
     */
    public function transferToConnectedAccount(float $amount, string $destinationAccountId, string $description, int $orderId): bool
    {
        // Stripe expects amounts in cents/smallest currency unit.
        // For EGP, it's piastres (amount * 100).
        $amountInPiastres = (int)($amount * 100);

        try {
            // For the sake of this demo/simulation, if no real STRIPE_SECRET is configured,
            // we will just log the intended action instead of hitting the API and failing.
            if (env('STRIPE_SECRET') === null) {
                Log::info("SIMULATED STRIPE TRANSFER: EGP {$amount} to {$destinationAccountId} for Order #{$orderId}. Description: {$description}");
                return true;
            }

            // Real Stripe API call
            $transfer = Transfer::create([
                'amount' => $amountInPiastres,
                'currency' => 'egp',
                'destination' => $destinationAccountId,
                'description' => $description,
                'metadata' => [
                    'order_id' => $orderId
                ]
            ]);

            Log::info("STRIPE TRANSFER SUCCESS: {$transfer->id} - EGP {$amount} to {$destinationAccountId}");
            return true;

        } catch (\Exception $e) {
            Log::error("STRIPE TRANSFER FAILED for Order #{$orderId}: " . $e->getMessage());
            throw new \Exception("Payment transfer failed: " . $e->getMessage());
        }
    }
}
