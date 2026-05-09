# DeliverEats — System Architecture & Documentation

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Order State Machine](#order-state-machine)
3. [API Documentation](#api-documentation)
4. [Real-Time Architecture](#real-time-architecture)
5. [User Guides](#user-guides)

---

## System Architecture

### High-Level Overview

```
┌───────────────────────────────────────────────────────────────┐
│                     DELIVEREATS PLATFORM                       │
├───────────┬──────────────┬──────────────┬─────────────────────┤
│  Customer │  Restaurant  │    Rider     │      Admin          │
│  Web App  │  Dashboard   │  Mobile API  │   Control Tower     │
│ (Livewire)│  (Livewire)  │  (Sanctum)   │    (Livewire)       │
└─────┬─────┴──────┬───────┴──────┬───────┴─────────┬───────────┘
      │            │              │                 │
      ▼            ▼              ▼                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                    LARAVEL APPLICATION LAYER                     │
│                                                                 │
│  ┌──────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │ OrderState    │  │ SurgePricing     │  │ PayoutCalculator │  │
│  │ Machine (FSM) │  │ Service          │  │ Service          │  │
│  │               │  │ (Strategy Pattern)│  │                  │  │
│  └───────┬──────┘  └────────┬─────────┘  └────────┬─────────┘  │
│          │                  │                      │             │
│  ┌───────▼──────────────────▼──────────────────────▼──────────┐ │
│  │                    EVENT SYSTEM                             │ │
│  │  OrderStatusUpdated → Pusher Broadcast                     │ │
│  │  DispatchOrderJob   → Redis Queue                          │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                 │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │              GOOGLE MAPS DISTANCE SERVICE                   │ │
│  │  Calculate rider-to-restaurant distance for dispatch        │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE (MySQL/SQLite)                     │
│                                                                 │
│  users · restaurants · menu_categories · menu_items · variants  │
│  orders · order_items · order_state_logs · riders · reviews     │
│  payout_splits · surge_pricing_logs · personal_access_tokens    │
└─────────────────────────────────────────────────────────────────┘
```

### Technology Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 |
| Frontend Components | Livewire 3 |
| API Authentication | Laravel Sanctum |
| Real-Time Broadcasting | Pusher via Laravel Echo |
| Queue Processing | Redis + Laravel Queues |
| Distance Calculation | Google Maps Distance Matrix API |
| CSS | TailwindCSS v4 |
| Database | MySQL (production) / SQLite (testing) |

### Design Patterns Used

| Pattern | Implementation |
|---------|---------------|
| **Finite State Machine** | `OrderStateMachine` — strict transition guards with event sourcing |
| **Strategy Pattern** | `SurgePricingStrategy` interface → Flat, Multiplier, TimeBased |
| **Observer Pattern** | `OrderStatusUpdated` event → Pusher broadcast |
| **Repository Pattern** | Eloquent models with domain-specific relationships |
| **Service Layer** | `PayoutCalculatorService`, `SurgePricingService`, `GoogleMapsDistanceService` |

---

## Order State Machine

### State Diagram

```
                    ┌─────────┐
           ┌───────│ PLACED  │───────┐
           │       └────┬────┘       │
           │            │            │
           │     ┌──────▼──────┐     │
           │     │  CONFIRMED  │     │
           │     └──────┬──────┘     │
           │            │            │
           │     ┌──────▼──────┐     │
           │     │  PREPARING  │     │
           │     └──────┬──────┘     │
           │            │            │
           │     ┌──────▼──────┐     │
           │     │ ON THE WAY  │     │
           │     └──────┬──────┘     │
           │            │            │
           │     ┌──────▼──────┐     │
           │     │  DELIVERED  │     │
           │     └─────────────┘     │
           │                         │
           │     ┌─────────────┐     │
           └────►│  CANCELLED  │◄────┘
                 └─────────────┘
```

### Transition Rules

| From | Allowed Transitions | Actor |
|------|-------------------|-------|
| `placed` | `confirmed`, `cancelled` | Restaurant, Customer |
| `confirmed` | `preparing`, `cancelled` | Restaurant |
| `preparing` | `on_the_way` | Rider |
| `on_the_way` | `delivered` | Rider |
| `delivered` | *(terminal state)* | — |
| `cancelled` | *(terminal state)* | — |

### Guards
- Invalid transitions throw `InvalidOrderTransitionException`
- Every transition is logged to `order_state_logs` with timestamp, actor_type, and actor_id
- On `confirmed` → auto-dispatches `DispatchOrderJob` to assign nearest rider
- On `delivered` → auto-generates `PayoutSplit` for revenue distribution

---

## API Documentation

### Authentication
All authenticated routes require `Authorization: Bearer {token}` header.

#### `POST /api/register`
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "customer"  // or "rider"
}
```

#### `POST /api/login`
```json
{ "email": "john@example.com", "password": "password123" }
```
Returns: `{ "user": {...}, "token": "1|abc..." }`

#### `POST /api/logout` (auth required)

---

### Customer Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/restaurants` | Browse restaurants (search, cuisine, sort) |
| `GET` | `/api/restaurants/{id}/menu` | Full menu with categories and variants |
| `GET` | `/api/restaurants/{id}/reviews` | Restaurant reviews |
| `POST` | `/api/restaurants/{id}/reviews` | Submit review (after delivery) |
| `GET` | `/api/cuisines` | Available cuisine types |

### Order Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/orders` | My orders (customer or rider) |
| `POST` | `/api/orders` | Place new order |
| `GET` | `/api/orders/{id}` | Order details |
| `PATCH` | `/api/orders/{id}/status` | Update order status via FSM |
| `POST` | `/api/orders/{id}/cancel` | Cancel order (placed state only) |
| `GET` | `/api/orders/{id}/track` | Order timeline with all state transitions |

#### Place Order Request
```json
{
  "restaurant_id": 1,
  "delivery_address": "123 Main St, Cairo",
  "lat": 30.0444,
  "lng": 31.2357,
  "instructions": "No onions please",
  "items": [
    { "menu_item_id": 1, "quantity": 2 },
    { "menu_item_id": 5, "menu_item_variant_id": 3, "quantity": 1 }
  ]
}
```

### Rider Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/rider/profile` | Rider stats (deliveries, earnings, rating) |
| `POST` | `/api/rider/toggle-online` | Go online/offline |
| `POST` | `/api/rider/location` | Update GPS coordinates |
| `GET` | `/api/rider/orders` | Assigned orders |
| `POST` | `/api/rider/orders/{id}/pickup` | Transition to on_the_way |
| `POST` | `/api/rider/orders/{id}/deliver` | Transition to delivered + payout |
| `POST` | `/api/rider/orders/{id}/review` | Review restaurant |

---

## Real-Time Architecture

### Broadcasting Flow

```
Order Status Change
        │
        ▼
OrderStateMachine::transitionTo()
        │
        ├── Updates order status in DB
        ├── Logs to order_state_logs
        └── Fires OrderStatusUpdated event
                │
                ▼
        Pusher Channel: order.{orderId}
                │
                ├── Customer app listens → updates tracking UI
                ├── Restaurant dashboard → updates order card
                └── Admin tower → updates live overview
```

### Redis Queue Architecture

```
Order Confirmed
        │
        ▼
DispatchOrderJob (queued via Redis)
        │
        ├── Queries all online riders
        ├── Filters out busy riders (active orders)
        ├── Calculates distance via GoogleMapsDistanceService
        ├── Assigns nearest available rider
        └── Broadcasts assignment via Pusher
```

### Pusher Channels
| Channel | Events | Consumers |
|---------|--------|-----------|
| `order.{id}` | `OrderStatusUpdated` | Customer tracking, Restaurant dashboard |
| `rider.{id}` | New order assignment | Rider mobile app |
| `admin` | All order events | Admin control tower |

---

## User Guides

### Customer Guide
1. **Browse**: Visit `/browse` to see all restaurants, filter by cuisine, search by name
2. **Menu**: Click a restaurant to see its full menu with categories and prices
3. **Add to Cart**: Hover over items and click `+` to add to cart
4. **Checkout**: Go to `/cart`, enter delivery address, add instructions, click "Place Order"
5. **Track**: View real-time order status at `/my-orders/{id}` with a timeline
6. **Review**: After delivery, rate the restaurant with 1-5 stars

### Restaurant Owner Guide
1. **Register**: Create account as "Restaurant" → fill onboarding form
2. **Dashboard**: After admin approval, access `/restaurant/dashboard`
3. **Orders Tab**: See incoming orders → click "Confirm" → "Start Preparing"
4. **Menu Tab**: Add/edit/delete menu items, toggle availability
5. **Reviews Tab**: See customer feedback and ratings
6. **Payouts Tab**: Track your revenue and pending payouts

### Rider Guide (API)
1. **Login**: `POST /api/login` to get auth token
2. **Go Online**: `POST /api/rider/toggle-online`
3. **Update Location**: `POST /api/rider/location` with `{ lat, lng }`
4. **Accept Orders**: System auto-assigns orders to nearest online rider
5. **Pickup**: `POST /api/rider/orders/{id}/pickup` to start delivery
6. **Deliver**: `POST /api/rider/orders/{id}/deliver` to complete order

### Admin Guide
1. **Login**: Use admin credentials → redirected to `/admin/tower`
2. **Overview**: See total orders, active orders, revenue, online riders
3. **Orders**: Filter, search, and cancel orders
4. **Riders**: Toggle rider online/offline status
5. **Restaurants**: Activate/deactivate restaurants
6. **Surge Pricing**: Activate surge with multiplier/flat/time-based strategies
7. **Payouts**: View revenue splits, mark payouts as paid

---

## Testing

### Test Suite (33 tests, 49 assertions)

| Suite | Tests | What's Tested |
|-------|-------|--------------|
| `FsmTransitionTest` | 10 | Valid transitions, invalid transitions (must throw), state logging, full lifecycle |
| `SurgePricingTest` | 9 | All 3 strategies, activation, deactivation, caps, rollback |
| `PayoutSplitTest` | 6 | Commission rates (10%, 15%, 20%), total accuracy, duplicate prevention |
| `DispatchAlgorithmTest` | 5 | Nearest rider, offline skip, no riders, 50 concurrent orders, role verification |

### Running Tests
```bash
php vendor/bin/phpunit
```

### Key Test Scenarios
- **Invalid FSM transitions throw**: `delivered → preparing`, `cancelled → confirmed`, `placed → delivered`
- **Surge cap enforcement**: Multiplier 5.0x capped to 2.5x max
- **Surge rollback**: Fee returns to base when surge is deactivated
- **Payout accuracy**: `platform + restaurant + rider = order_total` verified across rates
- **50 concurrent orders**: All dispatched to available riders
