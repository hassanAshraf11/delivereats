<div class="flex min-h-screen" wire:poll.5s>
    {{-- Sidebar --}}
    <aside class="w-64 bg-[#111111] border-r border-[#2A2A2A] p-6 flex flex-col fixed h-full">
        <a href="/" class="text-2xl font-black text-white mb-1"><img src="/delivereats_logo.svg" alt="DeliverEats" class="h-9"></a>
        <p class="text-xs text-[#666] mb-8">{{ $restaurant->name ?? 'Restaurant' }}</p>
        <nav class="space-y-1 flex-1">
            @foreach(['orders' => ['Orders', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'], 'menu' => ['Menu', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'], 'reviews' => ['Reviews', 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'], 'payouts' => ['Payouts', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'], 'settings' => ['Settings', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'], ] as $key => [$label, $path])
            <button wire:click="switchTab('{{ $key }}')" class="sidebar-link w-full text-left {{ $activeTab === $key ? 'active' : 'text-[#888]' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                {{ $label }}
            </button>
            @endforeach
        </nav>
        <div class="pt-4 border-t border-[#2A2A2A]">
            <a href="{{ route('admin.tower') }}" class="text-xs text-[#555] hover:text-[#888] transition mb-3 block">← Admin Panel</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-[#888] hover:text-[#FF6B2B] transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="ml-64 flex-1 p-8">
        @if(session('success'))<div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">{{ session('error') }}</div>@endif

        {{-- Stats Bar --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="stat-card glass glow-gold"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Active Orders</p><p class="text-3xl font-black text-amber-400">{{ $activeOrders->count() }}</p></div>
            <div class="stat-card glass glow-green"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Delivered</p><p class="text-3xl font-black text-emerald-400">{{ $deliveredOrders->count() }}</p></div>
            <div class="stat-card glass glow-orange"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Total Revenue</p><p class="text-2xl font-black text-[#FF6B2B]">EGP {{ number_format($totalRevenue, 2) }}</p></div>
            <div class="stat-card glass"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Rating</p><p class="text-3xl font-black text-white">{{ $avgRating ? number_format($avgRating, 1) : '—' }} <span class="text-amber-400 text-lg">★</span></p></div>
        </div>

        {{-- ORDERS TAB --}}
        @if($activeTab === 'orders')
        <h1 class="text-3xl font-bold text-white mb-6">Incoming Orders</h1>

        @if($activeOrders->count())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
            @foreach($activeOrders as $order)
            <div class="glass rounded-2xl p-6 {{ $order->status === 'placed' ? 'border-l-4 border-l-[#FFC542]' : ($order->status === 'preparing' ? 'border-l-4 border-l-[#FF6B2B]' : 'border-l-4 border-l-[#FF6B2B]') }}">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-lg font-bold text-white">Order #{{ $order->id }}</p>
                        <p class="text-sm text-[#888]">{{ $order->customer->name ?? 'Customer' }} · {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="badge {{ $order->status === 'placed' ? 'bg-yellow-500/20 text-yellow-400' : ($order->status === 'preparing' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400') }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="space-y-1 mb-4">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm"><span class="text-[#B0B0B0]">{{ $item->quantity }}x {{ $item->menuItem->name ?? 'Item' }}</span><span class="text-[#888]">EGP {{ number_format($item->price * $item->quantity, 2) }}</span></div>
                    @endforeach
                    <div class="pt-2 border-t border-[#2A2A2A] flex justify-between text-sm font-semibold"><span class="text-white">Total</span><span class="text-white">EGP {{ number_format($order->total_amount, 2) }}</span></div>
                </div>
                @if($order->instructions)<p class="text-xs text-amber-400/70 mb-3">📝 {{ $order->instructions }}</p>@endif
                <div class="flex gap-2 mb-3">
                    @if($order->status === 'placed')
                    <button wire:click="confirmOrder({{ $order->id }})" class="btn-success flex-1">✓ Confirm</button>
                    <button wire:click="cancelOrder({{ $order->id }})" wire:confirm="Cancel this order?" class="btn-danger">✗</button>
                    @elseif($order->status === 'confirmed')
                    <button wire:click="startPreparing({{ $order->id }})" class="btn-primary flex-1">🍳 Start Preparing</button>
                    @else
                    <p class="text-xs text-[#666] italic">Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
                    @endif
                </div>
                
                @if(in_array($order->status, ['confirmed', 'preparing', 'on_the_way']))
                    <div class="pt-3 border-t border-[#2A2A2A]">
                        @if($order->rider_id)
                            <p class="text-xs text-emerald-400">🏍️ Assigned to: <strong>{{ $order->rider->name ?? 'Rider' }}</strong></p>
                        @else
                            <div class="flex items-center gap-2">
                                <select wire:model="selectedRiders.{{ $order->id }}" class="input-field flex-1 text-xs py-1.5 px-2">
                                    <option value="">Select Rider...</option>
                                    @foreach($onlineRiders as $rider)
                                        <option value="{{ $rider->id }}">{{ $rider->user->name ?? 'Rider' }} ({{ $rider->vehicle_type ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                                <button wire:click="assignRider({{ $order->id }})" class="btn-primary text-xs py-1.5 px-3">Assign</button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="glass rounded-2xl p-12 text-center"><p class="text-[#666] text-lg">No active orders right now.</p><p class="text-[#555] text-sm mt-2">New orders will appear here in real-time.</p></div>
        @endif

        {{-- Delivery Locations Map --}}
        @if($activeOrders->count())
        <div class="glass rounded-2xl p-6 mb-8">
            <h2 class="text-lg font-bold text-white mb-4">📍 Delivery Locations</h2>
            <div id="restaurant-orders-map" style="height: 300px; border-radius: 12px;" class="border border-[#2A2A2A]"></div>
        </div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('livewire:navigated', initRestaurantMap);
            document.addEventListener('DOMContentLoaded', initRestaurantMap);
            function initRestaurantMap() {
                const el = document.getElementById('restaurant-orders-map');
                if (!el || el._leaflet_id) return;
                const map = L.map(el).setView([{{ $restaurant->lat ?? 30.0444 }}, {{ $restaurant->lng ?? 31.2357 }}], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                // Restaurant marker
                L.marker([{{ $restaurant->lat ?? 30.0444 }}, {{ $restaurant->lng ?? 31.2357 }}], {
                    icon: L.divIcon({className:'', html:'<div style="font-size:24px">🏪</div>', iconSize:[30,30], iconAnchor:[15,15]})
                }).addTo(map).bindPopup('<b>{{ addslashes($restaurant->name ?? "Restaurant") }}</b>');
                // Order markers
                @foreach($activeOrders as $order)
                @if($order->lat && $order->lng)
                L.marker([{{ $order->lat }}, {{ $order->lng }}], {
                    icon: L.divIcon({className:'', html:'<div style="font-size:20px">📦</div>', iconSize:[24,24], iconAnchor:[12,12]})
                }).addTo(map).bindPopup('Order #{{ $order->id }}<br>{{ addslashes($order->customer->name ?? "Customer") }}<br>{{ ucfirst($order->status) }}');
                @endif
                @endforeach
                setTimeout(() => map.invalidateSize(), 200);
            }
        </script>
        @endif

        {{-- Past Orders --}}
        <h2 class="text-xl font-bold text-white mt-10 mb-4">Order History</h2>
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead><tr class="border-b border-[#2A2A2A] text-left">
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">ID</th>
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Customer</th>
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Total</th>
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Status</th>
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Date</th>
                </tr></thead>
                <tbody>
                @foreach($orders->whereIn('status', ['delivered', 'cancelled'])->take(15) as $order)
                <tr class="table-row">
                    <td class="px-5 py-3 text-sm font-mono text-white">#{{ $order->id }}</td>
                    <td class="px-5 py-3 text-sm text-[#B0B0B0]">{{ $order->customer->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-sm font-semibold text-white">EGP {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-5 py-3"><span class="badge {{ $order->status === 'delivered' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="px-5 py-3 text-sm text-[#666]">{{ $order->created_at->format('M d, H:i') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- MENU TAB --}}
        @if($activeTab === 'menu')
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Menu Manager</h1>
            <div class="flex gap-3">
                <button wire:click="$toggle('showCategoryForm')" class="btn-ghost">📁 Manage Categories</button>
                <button wire:click="newItem" class="btn-primary">+ Add Item</button>
            </div>
        </div>

        {{-- Category Management --}}
        @if($showCategoryForm)
        <div class="glass rounded-2xl p-6 mb-6">
            <h2 class="text-lg font-bold text-white mb-4">Manage Categories</h2>
            <div class="flex gap-3 mb-4">
                <input wire:model="newCategoryName" class="input-field flex-1" placeholder="New category name (e.g. Appetizers, Main Course...)">
                <button wire:click="createCategory" class="btn-primary whitespace-nowrap">+ Add Category</button>
            </div>
            @error('newCategoryName')<p class="text-red-400 text-xs mb-3">{{ $message }}</p>@enderror
            @if($restaurant && $restaurant->menuCategories->count())
            <div class="flex flex-wrap gap-2">
                @foreach($restaurant->menuCategories as $cat)
                <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-[#1A1A1A] border border-[#2A2A2A]">
                    <span class="text-sm text-white">{{ $cat->name }}</span>
                    <span class="text-xs text-[#666]">({{ $cat->menuItems->count() }} items)</span>
                    @if($cat->menuItems->count() === 0)
                    <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Delete category '{{ $cat->name }}'?" class="text-red-400 hover:text-red-300 text-xs ml-1">✕</button>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-[#666] text-sm">No categories yet. Create one to start adding menu items.</p>
            @endif
        </div>
        @endif

        {{-- Menu Item Form --}}
        @if($showMenuForm)
        <div class="glass rounded-2xl p-6 mb-8">
            <h2 class="text-lg font-bold text-white mb-4">{{ $editingItemId ? 'Edit Item' : 'New Item' }}</h2>

            @if($restaurant && $restaurant->menuCategories->count() === 0)
            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm mb-4">
                ⚠️ You need to create at least one category first. Click "Manage Categories" above.
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-[#888] block mb-1">Name *</label>
                    <input wire:model="itemName" class="input-field" placeholder="Item name">
                    @error('itemName')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs text-[#888] block mb-1">Category *</label>
                    <select wire:model="itemCategoryId" class="input-field">
                        <option value="">Select a category</option>
                        @if($restaurant)
                        @foreach($restaurant->menuCategories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                        @endif
                    </select>
                    @error('itemCategoryId')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs text-[#888] block mb-1">Price (EGP) *</label>
                    <input wire:model="itemPrice" type="number" step="0.01" class="input-field">
                    @error('itemPrice')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs text-[#888] block mb-1">Prep Time (min)</label>
                    <input wire:model="itemPrepTime" type="number" class="input-field">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs text-[#888] block mb-1">Description</label>
                    <input wire:model="itemDescription" class="input-field" placeholder="Short description">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs text-[#888] block mb-1">Image (optional, max 2MB)</label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <input wire:model="itemImage" type="file" accept="image/*" class="block w-full text-sm text-[#888] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#FF6B2B]/10 file:text-[#FF6B2B] hover:file:bg-[#FF6B2B]/20 file:cursor-pointer file:transition">
                            @error('itemImage')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <div wire:loading wire:target="itemImage" class="text-xs text-[#FF6B2B] mt-1">Uploading...</div>
                        </div>
                        @if($itemImage)
                        <div class="relative">
                            <img src="{{ $itemImage->temporaryUrl() }}" class="w-20 h-20 rounded-xl object-cover border border-[#2A2A2A]">
                            <button wire:click="$set('itemImage', null)" class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">✕</button>
                        </div>
                        @elseif($existingImage)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $existingImage) }}" class="w-20 h-20 rounded-xl object-cover border border-[#2A2A2A]">
                            <button wire:click="removeImage" class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">✕</button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button wire:click="saveItem" class="btn-primary">Save</button>
                <button wire:click="$set('showMenuForm', false)" class="btn-ghost">Cancel</button>
            </div>
        </div>
        @endif

        @if($restaurant && $restaurant->menuCategories->count())
        @foreach($restaurant->menuCategories as $category)
        <div class="mb-8">
            <h2 class="text-xl font-bold text-white mb-4">{{ $category->name }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($category->menuItems as $item)
                <div class="glass rounded-xl overflow-hidden hover:scale-[1.02] transition-transform duration-300 {{ !$item->is_available ? 'opacity-50' : '' }}">
                    @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-36 object-cover" alt="{{ $item->name }}">
                    @endif
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-sm font-semibold text-white">{{ $item->name }}</h3>
                            <button wire:click="toggleAvailability({{ $item->id }})" class="badge cursor-pointer {{ $item->is_available ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $item->is_available ? 'Available' : 'Sold Out' }}
                            </button>
                        </div>
                        <p class="text-xs text-[#666] mb-3">{{ $item->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-[#FF6B2B]">EGP {{ number_format($item->base_price, 2) }}</span>
                            <div class="flex gap-2">
                                <button wire:click="editItem({{ $item->id }})" class="text-xs text-[#888] hover:text-white">Edit</button>
                                <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this item?" class="text-xs text-red-400 hover:text-red-300">Delete</button>
                            </div>
                        </div>
                        @if($item->variants->count())
                        <div class="mt-2 pt-2 border-t border-[#2A2A2A]">
                            <div class="flex flex-wrap gap-1">
                                @foreach($item->variants as $v)
                                <span class="text-xs px-2 py-0.5 rounded bg-[#1A1A1A] text-[#888]">{{ $v->name }} {{ $v->price_adjustment >= 0 ? '+' : '' }}{{ $v->price_adjustment }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @else
        <div class="glass rounded-2xl p-12 text-center">
            <p class="text-4xl mb-4">📋</p>
            <p class="text-[#888] text-lg mb-2">No menu categories yet</p>
            <p class="text-[#666] text-sm mb-4">Create categories first, then add items to your menu</p>
            <button wire:click="$set('showCategoryForm', true)" class="btn-primary">📁 Create Your First Category</button>
        </div>
        @endif
        @endif

        {{-- REVIEWS TAB --}}
        @if($activeTab === 'reviews')
        <h1 class="text-3xl font-bold text-white mb-6">Customer Reviews</h1>
        <div class="glass rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-6">
                <div class="text-center"><p class="text-5xl font-black text-white">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</p><p class="text-amber-400 text-lg">{{ str_repeat('★', round($avgRating ?? 0)) }}{{ str_repeat('☆', 5 - round($avgRating ?? 0)) }}</p><p class="text-xs text-[#666] mt-1">{{ $reviews->count() }} reviews</p></div>
                <div class="flex-1 space-y-1">
                    @for($i = 5; $i >= 1; $i--)
                    @php $count = $reviews->where('rating', $i)->count(); $pct = $reviews->count() ? ($count / $reviews->count()) * 100 : 0; @endphp
                    <div class="flex items-center gap-2"><span class="text-xs text-[#666] w-4">{{ $i }}</span><div class="flex-1 h-2 bg-[#1A1A1A] rounded-full overflow-hidden"><div class="h-full bg-amber-400 rounded-full" style="width:{{ $pct }}%"></div></div><span class="text-xs text-[#555] w-8">{{ $count }}</span></div>
                    @endfor
                </div>
            </div>
        </div>
        <div class="space-y-3">
            @forelse($reviews as $review)
            <div class="glass rounded-xl p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#FF6B2B]/20 flex items-center justify-center text-[#FF6B2B] font-bold text-xs">{{ substr($review->user->name ?? 'U', 0, 1) }}</div>
                        <div><p class="text-sm font-semibold text-white">{{ $review->user->name ?? 'Anonymous' }}</p><p class="text-xs text-[#666]">{{ $review->created_at->diffForHumans() }}</p></div>
                    </div>
                    <span class="text-amber-400 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                </div>
                @if($review->comment)<p class="text-sm text-[#888]">{{ $review->comment }}</p>@endif
            </div>
            @empty
            <div class="glass rounded-2xl p-12 text-center"><p class="text-[#666]">No reviews yet.</p></div>
            @endforelse
        </div>
        @endif

        {{-- PAYOUTS TAB --}}
        @if($activeTab === 'payouts')
        <h1 class="text-3xl font-bold text-white mb-6">Revenue & Payouts</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            <div class="stat-card glass glow-green"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Total Revenue</p><p class="text-3xl font-black text-emerald-400">EGP {{ number_format($totalRevenue, 2) }}</p></div>
            <div class="stat-card glass glow-gold"><p class="text-xs text-[#888] uppercase tracking-wider mb-1">Pending Payout</p><p class="text-3xl font-black text-amber-400">EGP {{ number_format($pendingPayouts, 2) }}</p></div>
        </div>
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead><tr class="border-b border-[#2A2A2A] text-left">
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Order</th>
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Your Share</th>
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Platform Fee</th>
                    <th class="px-5 py-3 text-xs uppercase text-[#666]">Status</th>
                </tr></thead>
                <tbody>
                @forelse($payouts as $payout)
                <tr class="table-row">
                    <td class="px-5 py-3 text-sm font-mono text-white">#{{ $payout->order_id }}</td>
                    <td class="px-5 py-3 text-sm font-semibold text-emerald-400">EGP {{ number_format($payout->restaurant_amount, 2) }}</td>
                    <td class="px-5 py-3 text-sm text-[#666]">EGP {{ number_format($payout->platform_amount, 2) }}</td>
                    <td class="px-5 py-3"><span class="badge {{ $payout->is_paid ? 'bg-emerald-500/20 text-emerald-400' : 'bg-yellow-500/20 text-yellow-400' }}">{{ $payout->is_paid ? 'Paid' : 'Pending' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-12 text-center text-[#666]">No payouts yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- SETTINGS TAB --}}
        @if($activeTab === 'settings')
        <h1 class="text-3xl font-bold text-white mb-6">Restaurant Settings</h1>
        <div class="glass rounded-2xl p-6 mb-6">
            <h2 class="text-lg font-bold text-white mb-4">Restaurant Images</h2>
            <p class="text-sm text-[#666] mb-6">These images appear to customers on the browse and restaurant pages.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Logo --}}
                <div>
                    <label class="text-xs text-[#888] block mb-2">Logo (max 2MB)</label>
                    @if($restaurant && $restaurant->logo)
                    <div class="relative inline-block mb-3">
                        <img src="{{ asset('storage/' . $restaurant->logo) }}" class="w-24 h-24 rounded-xl object-cover border border-[#2A2A2A]">
                        <button wire:click="removeRestaurantLogo" class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">✕</button>
                    </div>
                    @endif
                    @if($restaurantLogo)
                    <div class="mb-3">
                        <img src="{{ $restaurantLogo->temporaryUrl() }}" class="w-24 h-24 rounded-xl object-cover border border-[#FF6B2B]">
                        <p class="text-xs text-[#FF6B2B] mt-1">New logo preview</p>
                    </div>
                    @endif
                    <input wire:model="restaurantLogo" type="file" accept="image/*" class="block w-full text-sm text-[#888] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#FF6B2B]/10 file:text-[#FF6B2B] hover:file:bg-[#FF6B2B]/20 file:cursor-pointer file:transition">
                    @error('restaurantLogo')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    <div wire:loading wire:target="restaurantLogo" class="text-xs text-[#FF6B2B] mt-1">Uploading...</div>
                </div>

                {{-- Cover --}}
                <div>
                    <label class="text-xs text-[#888] block mb-2">Cover Photo (max 4MB)</label>
                    @if($restaurant && $restaurant->cover)
                    <div class="relative inline-block mb-3">
                        <img src="{{ asset('storage/' . $restaurant->cover) }}" class="w-full h-32 rounded-xl object-cover border border-[#2A2A2A]">
                        <button wire:click="removeRestaurantCover" class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">✕</button>
                    </div>
                    @endif
                    @if($restaurantCover)
                    <div class="mb-3">
                        <img src="{{ $restaurantCover->temporaryUrl() }}" class="w-full h-32 rounded-xl object-cover border border-[#FF6B2B]">
                        <p class="text-xs text-[#FF6B2B] mt-1">New cover preview</p>
                    </div>
                    @endif
                    <input wire:model="restaurantCover" type="file" accept="image/*" class="block w-full text-sm text-[#888] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#FF6B2B]/10 file:text-[#FF6B2B] hover:file:bg-[#FF6B2B]/20 file:cursor-pointer file:transition">
                    @error('restaurantCover')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    <div wire:loading wire:target="restaurantCover" class="text-xs text-[#FF6B2B] mt-1">Uploading...</div>
                </div>
            </div>

            <button wire:click="saveRestaurantImages" class="btn-primary mt-6">Save Images</button>
        </div>
        @endif
    </main>
</div>
