# DeliverEats – Multi-Restaurant Delivery Platform

DeliverEats is a comprehensive, multi-tenant food delivery ecosystem built with Laravel 11, Livewire 3, Alpine.js, and Tailwind CSS. It connects customers, restaurants, delivery riders, and administrators in a single, unified platform with real-time tracking and automated dispatching.

## 🏗️ Core Architecture & Logic

This project moves beyond standard CRUD operations by implementing several advanced architectural patterns and business logic engines:

### 1. Robust State Machine (Order Lifecycle)
Orders do not simply update a "status" string. They are governed by a strict `OrderStateMachine` that enforces valid transitions and logs all state changes for auditing.
* **Flow:** `placed` → `confirmed` → `preparing` → `on_the_way` → `delivered`.
* **Cancellations:** Orders can be gracefully cancelled by admins or restaurants at any active stage via the state machine override logic.
* **Event-Driven:** Transitions trigger Laravel Events (e.g., `OrderStatusUpdated`), ensuring side-effects like notifications or dispatching are loosely coupled.

### 2. Automated Dispatch Algorithm
When an order transitions to `confirmed`, the state machine triggers a `DispatchOrderJob` pushed to the background Queue.
* The algorithm queries the database for all `is_online` Riders.
* It calculates the exact straight-line distance from the restaurant to each online rider using the Haversine formula (or Google Maps Distance Matrix API).
* The order is automatically assigned to the optimal rider to ensure fast delivery times.
* *Note: Restaurants also have the capability to manually override and assign riders via their dashboard.*

### 3. Dynamic Surge Pricing Engine
The platform dynamically calculates delivery fees using the Strategy Pattern (`SurgePricingStrategy`).
* **Multipliers:** Evaluates current demand (active orders vs. active riders), time of day (rush hour), and simulated weather conditions.
* **Logging:** When surge pricing is active, it logs the multiplier and reasoning in the `surge_pricing_logs` table for administrative review.

### 4. Payout Split Engine (Stripe Connect Simulation)
When an order is completed, the `PayoutCalculatorService` determines how the revenue is distributed.
* **Logic:** The total order amount is split based on commission rates. A percentage goes to the Restaurant, a fixed/distance-based fee goes to the Rider, and the platform retains the remainder.
* **Financial Ledger:** These splits are stored in the `payout_splits` table. The Admin Control Tower can then review and "Mark as Paid", which triggers a simulated Stripe Connect payout transfer to the respective user bank accounts.

### 5. Polymorphic Review System
Customers can leave reviews at the end of their order. To keep the database clean, the `reviews` table utilizes Laravel's Polymorphic Relationships (`reviewable_type`, `reviewable_id`).
* This single table securely stores ratings and comments for **both** Restaurants and Riders independently.

### 6. "Real-Time" Dashboard Polling
To provide a live experience without the overhead of maintaining a WebSocket server (like Pusher) in a university/demo environment, the application leverages **Livewire Polling**.
* **Live Map Tracking:** The customer tracking page polls the server to fetch the actual Rider's `current_lat` and `current_lng` from the database, updating their Leaflet map markers in real-time.
* **Dashboard Feeds:** Restaurant and Rider dashboards automatically refresh to display new incoming orders and state changes instantly.

## 👥 Role-Based Access & Dashboards

The platform uses a unified `users` table with a `role` column, managed via custom Middleware (`RedirectByRole` and `CheckRole`).

1. **Customers:** Browse restaurants, add items to a dynamic cart, place orders, and track them on a live map.
2. **Restaurants:** Manage menu categories, items, variants, and availability. Monitor incoming orders and transition their states (e.g., "Start Preparing").
3. **Riders:** Toggle online/offline status. View active deliveries assigned to them, and mark orders as "Picked Up" or "Delivered".
4. **Admins (Control Tower):** View high-level metrics, manage surge pricing, process financial payouts, and monitor the entire city via a Live Leaflet Map showing all online riders, active orders, and restaurants.

## 🚀 Deployment (Render)

This project includes a custom `Dockerfile` and `build.sh` script optimized for deployment on Render.
* **Requirements:** PHP 8.2+, SQLite/PostgreSQL, Node.js (for Vite asset compilation).
* **Queues:** The application defaults to the `sync` queue driver for ease of deployment, but is fully configured to use `redis` via the Predis client for high-performance background processing.
