<?php

namespace App\StateMachines;

use App\Models\Order;
use App\Models\OrderStateLog;
use App\Exceptions\InvalidOrderTransitionException;
use Illuminate\Support\Facades\DB;

class OrderStateMachine
{
    private Order $order;

    private const VALID_TRANSITIONS = [
        'placed' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['on_the_way', 'cancelled'],
        'on_the_way' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function transitionTo(string $newState, ?string $actorType = null, ?int $actorId = null): void
    {
        $currentState = $this->order->status;

        if (!$this->canTransition($currentState, $newState)) {
            throw new InvalidOrderTransitionException($currentState, $newState);
        }

        DB::transaction(function () use ($currentState, $newState, $actorType, $actorId) {
            $this->order->status = $newState;
            $this->order->save();

            OrderStateLog::create([
                'order_id' => $this->order->id,
                'previous_state' => $currentState,
                'new_state' => $newState,
                'actor_type' => $actorType,
                'actor_id' => $actorId,
            ]);
            
            event(new \App\Events\OrderStatusUpdated($this->order));
            
            if ($newState === 'confirmed') {
                \App\Jobs\DispatchOrderJob::dispatch($this->order);
            }
        });
    }

    private function canTransition(string $from, string $to): bool
    {
        return isset(self::VALID_TRANSITIONS[$from]) && in_array($to, self::VALID_TRANSITIONS[$from]);
    }
}
