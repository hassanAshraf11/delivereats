# DeliverEats – Multi-Restaurant Delivery Platform

DeliverEats is a robust, multi-tenant food delivery ecosystem built with Laravel 11, Livewire 3, Alpine.js, and Tailwind CSS. It connects customers, restaurants, delivery riders, and administrators in a single, unified platform with real-time tracking and automated dispatching.

---

## 🏗️ Deep Dive into System Logic & Architecture

This project strictly adheres to solid OOP principles and advanced architectural patterns. Standard CRUD operations are abstracted, with heavy emphasis placed on background processing, state management, and algorithmic pricing.

### 1. Order State Machine (State Pattern)
Orders are governed by the `App\StateMachines\OrderStateMachine` class, preventing invalid data states and race conditions.
* **Validation Matrix:** Uses a `VALID_TRANSITIONS` constant array mapping authorized state jumps (e.g., `placed` → `confirmed`). Attempting an invalid jump throws a custom `InvalidOrderTransitionException`.
* **ACID Transactions:** Every state transition (`transitionTo()`) is wrapped in a `DB::transaction()`. If the order fails to save, or the log fails to create, the entire transition rolls back.
* **Audit Trail:** Every successful jump creates an `OrderStateLog` record, tracking the exact timestamp, `actor_type` (Admin, Restaurant, System), and `actor_id`.
* **Event-Driven Side Effects:** Transitions fire an `OrderStatusUpdated` Laravel Event. Listeners decouple the core order flow from external actions, such as triggering the background dispatch job when moving to `confirmed`.

### 2. Automated Rider Dispatch Algorithm (Queue & Geo-Spatial Logic)
When an order is confirmed, the system pushes a `DispatchOrderJob` (implementing `ShouldQueue`) to the background Redis worker queue to avoid blocking the HTTP request.
* **Geo-Spatial Querying:** The algorithm queries the `riders` table for instances where `is_online = true`.
* **Haversine Formula Application:** It extracts the `$restaurant->lat` and `$restaurant->lng` and calculates the spherical distance between the restaurant and every online rider's `current_lat` and `current_lng`. 
* **Algorithmic Sorting:** Riders are sorted in a `Collection` by calculated distance (or via the Google Maps Distance Matrix API service). The system automatically assigns the closest optimal rider, updating `$order->rider_id`.

### 3. Dynamic Surge Pricing Engine (Strategy Pattern)
The delivery fee calculation utilizes a strict Strategy behavioral design pattern via the `App\Services\SurgePricingService`.
* **Interface Contract:** All pricing algorithms implement the `SurgePricingStrategy` interface, requiring a `calculate(float $baseFee): float` method.
* **Dynamic Resolution:** The service resolves active strategies such as:
  * `TimeBasedSurgeStrategy`: Applies a 1.5x multiplier during configured rush hours.
  * `MultiplierSurgeStrategy`: Evaluates current supply/demand (active orders vs active riders) to calculate an algorithmic multiplier.
  * `FlatSurgeStrategy`: Adds fixed hazard pay (e.g., bad weather).
* **Financial Ledgering:** The final calculated multiplier and the exact reasoning are durably recorded in the `surge_pricing_logs` table before the user is charged.

### 4. Financial Splitting & Stripe Connect Simulation
Processing a single customer payment requires routing funds to three separate entities (Restaurant, Rider, Platform).
* **Payout Split Engine:** When an order is completed, the `PayoutCalculatorService` executes business logic rules (e.g., Platform retains a 20% commission on subtotal and 10% on delivery fee). The exact dollar amounts are stored in a `PayoutSplit` model.
* **Stripe Facade:** The Admin Control Tower triggers a payout run. The platform utilizes a mock `StripePaymentService` class that acts as a facade, accepting the `PayoutSplit` model and simulating the secure API payload required to execute multi-party transfers via Stripe Connect.

### 5. Polymorphic Rating & Review System
To maintain database normalization, the `reviews` table utilizes Laravel's Polymorphic Relationships.
* Instead of separate `restaurant_reviews` and `rider_reviews` tables, the schema uses `reviewable_type` and `reviewable_id`.
* When a customer completes an order, they submit two distinct ratings. The controller dynamically resolves the `Restaurant::class` and `Rider::class` models, saving them securely to the single polymorphic table.

### 6. Real-Time Telemetry via XHR Polling
To provide a live experience without the overhead of maintaining a WebSocket server (like Pusher) in a university/demo environment, the application leverages Livewire 3's `wire:poll` directive.
* **Asynchronous Feeds:** High-level dashboard containers send non-blocking XHR requests every 5000ms.
* **DOM Diffing:** Livewire calculates the state differences. If an order transitions to `preparing`, the Restaurant and Rider dashboards update instantly without a hard page reload.
* **Live Map Telemetry:** The customer tracking page fetches the latest `$rider->current_lat` and `$rider->current_lng`. A custom browser event dispatcher injects these new coordinates into the frontend Leaflet.js instance, smoothly moving the marker across the map in real-time.

---

## 👥 Role-Based Access Control (RBAC)

The platform relies on a unified `users` table with a string `role` identifier, protected by custom Middleware (`RedirectByRole` and `CheckRole`).

1. **Customers:** Browse dynamic cart sessions, place orders, and track them via Live Maps.
2. **Restaurants:** Manage polymorphic `MenuItems` and `MenuCategories`. Transition active orders via the state machine override.
3. **Riders:** Stream telemetry data (`current_lat`/`lng`), toggle availability, and execute order pickups.
4. **Admins:** Oversee the entire city via the Control Tower Leaflet Map, analyze `SurgePricingLogs`, and trigger `PayoutSplits`.

## 🚀 Deployment (Render)
Includes a custom `Dockerfile` and `build.sh` script optimized for Render.
* **Requirements:** PHP 8.2+, SQLite/PostgreSQL, Node.js (for Vite).
* **Queues:** Defaults to the `sync` queue driver, but is production-ready for `redis` via the Predis client for actual background job processing.
