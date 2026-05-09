<div class="flex min-h-screen" wire:poll.5s>
    {{-- Sidebar --}}
    <aside class="w-64 bg-[#111111] border-r border-[#2A2A2A] p-6 flex flex-col fixed h-full">
        <a href="/" class="text-2xl font-black text-white mb-1"><img src="/delivereats_logo.svg" alt="DeliverEats" class="h-9"></a>
        <p class="text-xs text-[#666] mb-8">Admin Control Tower</p>
        <nav class="space-y-1 flex-1">
            <button wire:click="switchTab('overview')" class="sidebar-link w-full text-left {{ $activeTab === 'overview' ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Overview
            </button>
            <button wire:click="switchTab('orders')" class="sidebar-link w-full text-left {{ $activeTab === 'orders' ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Orders
            </button>
            <button wire:click="switchTab('riders')" class="sidebar-link w-full text-left {{ $activeTab === 'riders' ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Riders
            </button>
            <button wire:click="switchTab('restaurants')" class="sidebar-link w-full text-left {{ $activeTab === 'restaurants' ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Restaurants
            </button>
            <button wire:click="switchTab('surge')" class="sidebar-link w-full text-left {{ $activeTab === 'surge' ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Surge Pricing
            </button>
            <button wire:click="switchTab('payouts')" class="sidebar-link w-full text-left {{ $activeTab === 'payouts' ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Payouts
            </button>
            <button wire:click="switchTab('livemap')" class="sidebar-link w-full text-left {{ $activeTab === 'livemap' ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Live Map
            </button>
        </nav>
        <div class="pt-4 border-t border-[#2A2A2A]">
            <p class="text-xs text-[#555] mb-3">Logged in as Admin</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-[#888] hover:text-[#FF6B2B] transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="ml-64 flex-1 p-8">
        @if(session('success'))<div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">{{ session('error') }}</div>@endif

        {{-- OVERVIEW TAB --}}
        @if($activeTab === 'overview')
        <h1 class="text-3xl font-bold text-white mb-8">Dashboard Overview</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="stat-card glass glow-orange"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Total Orders</p><p class="text-3xl font-black text-white">{{ $totalOrders }}</p></div>
            <div class="stat-card glass glow-green"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Active Orders</p><p class="text-3xl font-black text-emerald-400">{{ $activeOrders }}</p></div>
            <div class="stat-card glass glow-gold"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Platform Revenue</p><p class="text-3xl font-black text-amber-400">EGP {{ number_format($totalRevenue, 2) }}</p></div>
            <div class="stat-card glass"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Riders Online</p><p class="text-3xl font-black text-white">{{ $onlineRiders }}<span class="text-sm text-[#666]">/{{ $totalRiders }}</span></p></div>
        </div>

        @if($activeSurge)
        <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center gap-3">
            <svg class="w-6 h-6 text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <div><p class="text-amber-400 font-semibold">Surge Pricing Active</p><p class="text-xs text-amber-500/70">Strategy: {{ ucfirst($activeSurge->strategy) }} | Multiplier: {{ $activeSurge->multiplier ?? 'N/A' }}x</p></div>
            <button wire:click="deactivateSurge" class="ml-auto btn-danger text-xs">Deactivate</button>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Orders --}}
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Recent Active Orders</h2>
                <div class="space-y-3">
                    @foreach($orders->take(5) as $order)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#1F1F1F]">
                        <div>
                            <p class="text-sm font-semibold text-white">#{{ $order->id }} — {{ $order->restaurant->name ?? 'N/A' }}</p>
                            <p class="text-xs text-[#666]">{{ $order->customer->name ?? 'N/A' }} · EGP {{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <span class="badge {{ $order->status === 'placed' ? 'bg-yellow-500/20 text-yellow-400' : ($order->status === 'on_the_way' ? 'bg-blue-500/20 text-blue-400' : ($order->status === 'preparing' ? 'bg-purple-500/20 text-purple-400' : 'bg-emerald-500/20 text-emerald-400')) }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Event Log --}}
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">State Transition Log</h2>
                <div class="space-y-2 max-h-80 overflow-y-auto pr-2">
                    @foreach($recentLogs as $log)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-[#1A1A1A]">
                        <div class="w-2 h-2 rounded-full {{ $log->new_state === 'delivered' ? 'bg-emerald-400' : ($log->new_state === 'cancelled' ? 'bg-red-400' : 'bg-[#FF6B2B]') }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white">Order #{{ $log->order_id }}: <span class="text-[#888]">{{ $log->previous_state ?? '—' }}</span> → <span class="font-semibold {{ $log->new_state === 'cancelled' ? 'text-red-400' : 'text-[#FF6B2B]' }}">{{ $log->new_state }}</span></p>
                        </div>
                        <p class="text-xs text-[#555]">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ORDERS TAB --}}
        @if($activeTab === 'orders')
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Order Management</h1>
            <input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="Search orders..." class="input-field w-64">
        </div>
        <div class="flex gap-2 mb-6">
            @foreach(['active' => 'Active', 'placed' => 'Placed', 'confirmed' => 'Confirmed', 'preparing' => 'Preparing', 'on_the_way' => 'On the Way', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled', 'all' => 'All'] as $key => $label)
            <button wire:click="setOrderFilter('{{ $key }}')" class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ $orderFilter === $key ? 'bg-[#FF6B2B] text-white' : 'bg-[#1A1A1A] text-[#888] hover:text-white' }}">{{ $label }}</button>
            @endforeach
        </div>
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead><tr class="border-b border-[#2A2A2A] text-left">
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">ID</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Customer</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Restaurant</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Total</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Status</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Rider</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($orders as $order)
                <tr class="table-row">
                    <td class="px-6 py-4 text-sm font-mono text-white">#{{ $order->id }}</td>
                    <td class="px-6 py-4 text-sm text-[#B0B0B0]">{{ $order->customer->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-[#B0B0B0]">{{ $order->restaurant->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-white">EGP {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4"><span class="badge {{ $order->status === 'delivered' ? 'bg-emerald-500/20 text-emerald-400' : ($order->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : ($order->status === 'placed' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-[#FF6B2B]/20 text-[#FF6B2B]')) }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                    <td class="px-6 py-4 text-sm text-[#888]">{{ $order->rider->name ?? 'Unassigned' }}</td>
                    <td class="px-6 py-4">
                        @if(!in_array($order->status, ['delivered', 'cancelled']))
                        <button wire:click="cancelOrder({{ $order->id }})" wire:confirm="Cancel order #{{ $order->id }}?" class="text-xs text-red-400 hover:text-red-300 font-medium">Cancel</button>
                        @else
                        <span class="text-xs text-[#555]">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-[#666]">No orders found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
        @endif

        {{-- RIDERS TAB --}}
        @if($activeTab === 'riders')
        <h1 class="text-3xl font-bold text-white mb-8">Rider Fleet</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($riders as $rider)
            <div class="glass rounded-2xl p-5 hover:scale-[1.02] transition-transform duration-300">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#FF6B2B]/20 flex items-center justify-center text-[#FF6B2B] font-bold text-sm">{{ substr($rider->user->name ?? 'R', 0, 1) }}</div>
                        <div><p class="text-sm font-semibold text-white">{{ $rider->user->name ?? 'Rider' }}</p><p class="text-xs text-[#666]">{{ ucfirst($rider->vehicle_type ?? 'N/A') }}</p></div>
                    </div>
                    <button wire:click="toggleRiderStatus({{ $rider->id }})" class="badge cursor-pointer {{ $rider->is_online ? 'bg-emerald-500/20 text-emerald-400' : 'bg-[#272727] text-[#888]' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $rider->is_online ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500' }}"></span>
                        {{ $rider->is_online ? 'Online' : 'Offline' }}
                    </button>
                </div>
                <div class="text-xs text-[#666] space-y-1">
                    <p>📍 Lat: {{ number_format($rider->current_lat, 4) }}, Lng: {{ number_format($rider->current_lng, 4) }}</p>
                    <p>📧 {{ $rider->user->email ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- RESTAURANTS TAB --}}
        @if($activeTab === 'restaurants')
        <h1 class="text-3xl font-bold text-white mb-8">Restaurants</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($restaurants as $restaurant)
            <div class="glass rounded-2xl p-6 hover:scale-[1.01] transition-transform duration-300">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $restaurant->name }}</h3>
                        <p class="text-xs text-[#FF6B2B]">{{ $restaurant->cuisine_type }} · {{ $restaurant->orders_count }} orders</p>
                    </div>
                    <button wire:click="toggleRestaurant({{ $restaurant->id }})" class="badge cursor-pointer {{ $restaurant->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                        {{ $restaurant->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </div>
                <p class="text-sm text-[#888] mb-3">{{ $restaurant->description }}</p>
                <div class="flex gap-4 text-xs text-[#666]">
                    <span>📍 {{ $restaurant->address }}</span>
                    <span>⏰ {{ $restaurant->opening_time }} - {{ $restaurant->closing_time }}</span>
                    <span>💰 {{ $restaurant->commission_rate }}% commission</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- SURGE TAB --}}
        @if($activeTab === 'surge')
        <h1 class="text-3xl font-bold text-white mb-8">Surge Pricing Control</h1>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">{{ $activeSurge ? '⚡ Surge Active' : 'Activate Surge' }}</h2>
                @if($activeSurge)
                <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 mb-4">
                    <p class="text-amber-400 font-semibold">{{ ucfirst($activeSurge->strategy) }} Strategy</p>
                    <p class="text-sm text-amber-500/70">Multiplier: {{ $activeSurge->multiplier ?? 'N/A' }}x · Since {{ $activeSurge->active_from?->diffForHumans() ?? 'N/A' }}</p>
                </div>
                <button wire:click="deactivateSurge" class="btn-danger w-full">Deactivate Surge</button>
                @else
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-[#888] mb-1 block">Strategy</label>
                        <select wire:model="surgeStrategy" class="input-field">
                            <option value="multiplier">Multiplier</option>
                            <option value="flat">Flat Amount</option>
                            <option value="time_based">Time Based</option>
                        </select>
                    </div>
                    @if($surgeStrategy !== 'flat')
                    <div>
                        <label class="text-xs text-[#888] mb-1 block">Multiplier</label>
                        <input wire:model="surgeMultiplier" type="number" step="0.1" min="1" class="input-field">
                    </div>
                    @else
                    <div>
                        <label class="text-xs text-[#888] mb-1 block">Flat Amount (EGP)</label>
                        <input wire:model="surgeFlatAmount" type="number" step="1" min="0" class="input-field">
                    </div>
                    @endif
                    <button wire:click="activateSurge" class="btn-primary w-full">⚡ Activate Surge</button>
                </div>
                @endif
            </div>
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Surge History</h2>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($surgeLogs as $log)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#1F1F1F]">
                        <div>
                            <p class="text-sm text-white font-medium">{{ ucfirst($log->strategy) }}</p>
                            <p class="text-xs text-[#666]">{{ $log->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <span class="badge {{ $log->is_active ? 'bg-amber-500/20 text-amber-400' : 'bg-[#272727] text-[#888]' }}">{{ $log->is_active ? 'Active' : 'Ended' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- PAYOUTS TAB --}}
        @if($activeTab === 'payouts')
        <h1 class="text-3xl font-bold text-white mb-6">Payout Management</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="stat-card glass glow-orange"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Platform Revenue</p><p class="text-2xl font-black text-[#FF6B2B]">EGP {{ number_format($totalRevenue, 2) }}</p></div>
            <div class="stat-card glass glow-green"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Restaurant Payouts</p><p class="text-2xl font-black text-emerald-400">EGP {{ number_format($totalRestaurantPayouts, 2) }}</p></div>
            <div class="stat-card glass glow-gold"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Rider Payouts</p><p class="text-2xl font-black text-amber-400">EGP {{ number_format($totalRiderPayouts, 2) }}</p></div>
        </div>
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead><tr class="border-b border-[#2A2A2A] text-left">
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Order</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Restaurant</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Platform</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Restaurant Share</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Rider Share</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Status</th>
                    <th class="px-6 py-4 text-xs uppercase tracking-wider text-[#666]">Action</th>
                </tr></thead>
                <tbody>
                @forelse($payouts as $payout)
                <tr class="table-row">
                    <td class="px-6 py-4 text-sm font-mono text-white">#{{ $payout->order_id }}</td>
                    <td class="px-6 py-4 text-sm text-[#B0B0B0]">{{ $payout->order->restaurant->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-[#FF6B2B] font-semibold">EGP {{ number_format($payout->platform_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-emerald-400">EGP {{ number_format($payout->restaurant_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-amber-400">EGP {{ number_format($payout->rider_amount, 2) }}</td>
                    <td class="px-6 py-4"><span class="badge {{ $payout->is_paid ? 'bg-emerald-500/20 text-emerald-400' : 'bg-yellow-500/20 text-yellow-400' }}">{{ $payout->is_paid ? 'Paid' : 'Pending' }}</span></td>
                    <td class="px-6 py-4">
                        @if(!$payout->is_paid)
                        <button wire:click="markAsPaid({{ $payout->id }})" class="text-xs text-emerald-400 hover:text-emerald-300 font-medium">Mark Paid</button>
                        @else
                        <button wire:click="markAsUnpaid({{ $payout->id }})" class="text-xs text-[#666] hover:text-[#888]">Undo</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-[#666]">No payouts yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payouts->links() }}</div>
        @endif

        {{-- LIVE MAP TAB --}}
        @if($activeTab === 'livemap')
        <h1 class="text-3xl font-bold text-white mb-8">Live Map</h1>
        <div class="glass rounded-2xl p-6">
            <div id="admin-live-map" style="height: 500px; border-radius: 12px;" class="border border-[#2A2A2A]"></div>
        </div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            function initAdminMap() {
                const el = document.getElementById('admin-live-map');
                if (!el) return;

                if (window.adminMapInstance) {
                    window.adminMapInstance.remove();
                }

                const map = L.map(el).setView([30.0444, 31.2357], 12);
                window.adminMapInstance = map;

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                @foreach($restaurants as $r)
                L.marker([{{ $r->lat ?? 30.0444 }}, {{ $r->lng ?? 31.2357 }}], {
                    icon: L.divIcon({className:'', html:'<div style="font-size:22px">🏪</div>', iconSize:[28,28], iconAnchor:[14,14]})
                }).addTo(map).bindPopup('<b>{{ addslashes($r->name) }}</b><br>{{ $r->is_active ? "Active" : "Inactive" }}');
                @endforeach
                @foreach($orders->filter(fn($o) => !in_array($o->status, ['delivered','cancelled'])) as $o)
                @if($o->lat && $o->lng)
                L.marker([{{ $o->lat }}, {{ $o->lng }}], {
                    icon: L.divIcon({className:'', html:'<div style="font-size:18px">📦</div>', iconSize:[22,22], iconAnchor:[11,11]})
                }).addTo(map).bindPopup('Order #{{ $o->id }}<br>{{ ucfirst($o->status) }}<br>{{ addslashes($o->customer->name ?? "Customer") }}');
                @endif
                @endforeach
                @foreach($riders as $r)
                @if($r->is_online)
                L.marker([{{ $r->current_lat ?? 30.0444 }}, {{ $r->current_lng ?? 31.2357 }}], {
                    icon: L.divIcon({className:'', html:'<div style="font-size:20px">🏍️</div>', iconSize:[24,24], iconAnchor:[12,12]})
                }).addTo(map).bindPopup('<b>{{ addslashes($r->user->name ?? "Rider") }}</b><br>Online');
                @endif
                @endforeach
                setTimeout(() => map.invalidateSize(), 200);
            }

            document.addEventListener('DOMContentLoaded', initAdminMap);
            document.addEventListener('livewire:navigated', initAdminMap);
            window.addEventListener('init-map', initAdminMap);
        </script>
        <div class="mt-4 flex gap-6 text-sm text-[#888]">
            <span>🏪 Restaurants</span>
            <span>📦 Active Orders</span>
            <span>🏍️ Online Riders</span>
        </div>
        @endif
    </main>
</div>
