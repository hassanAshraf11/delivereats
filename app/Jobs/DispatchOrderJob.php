<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Rider;
use App\Services\GoogleMapsDistanceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DispatchOrderJob implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleMapsDistanceService $distanceService): void
    {
        $restaurant = $this->order->restaurant;
        
        // Find all online riders
        $onlineRiders = Rider::where('is_online', true)->get();
        
        if ($onlineRiders->isEmpty()) {
            Log::warning("No online riders available for order {$this->order->id}");
            // In a real system, we would retry this job later
            return;
        }

        $nearestRider = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($onlineRiders as $rider) {
            // Check if rider is currently busy with another order
            $activeOrders = Order::where('rider_id', $rider->user_id)
                ->whereIn('status', ['confirmed', 'preparing', 'on_the_way'])
                ->count();
                
            if ($activeOrders > 0) {
                continue; // Rider is busy
            }

            $distance = $distanceService->getDistance(
                $restaurant->lat ?? 0, 
                $restaurant->lng ?? 0, 
                $rider->current_lat ?? 0, 
                $rider->current_lng ?? 0
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestRider = $rider;
            }
        }

        if ($nearestRider) {
            $this->order->rider_id = $nearestRider->user_id;
            $this->order->save();
            
            // Notify rider via Pusher
            event(new \App\Events\OrderStatusUpdated($this->order));
            
            Log::info("Order {$this->order->id} dispatched to rider {$nearestRider->user_id}");
        } else {
            Log::warning("All online riders are currently busy for order {$this->order->id}");
        }
    }
}
