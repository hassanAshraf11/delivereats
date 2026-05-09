<div wire:poll.5s>
    <a href="{{ route('my.orders') }}" class="text-sm text-[#666] hover:text-[#FF6B2B] transition mb-4 inline-block">← Back to My Orders</a>

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Map --}}
        <div class="flex-1">
            <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 mb-6">
                <h2 class="text-lg font-bold text-white mb-4">📍 Delivery Map</h2>
                <div id="track-order-map" style="height: 300px; border-radius: 12px;" class="border border-[#2A2A2A]"></div>
            </div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                function initTrackMap() {
                    const el = document.getElementById('track-order-map');
                    if (!el) return;
                    
                    if (window.trackMapInstance) {
                        window.trackMapInstance.remove();
                    }

                    const deliveryLat = {{ $order->lat ?? 30.0444 }};
                    const deliveryLng = {{ $order->lng ?? 31.2357 }};
                    const restLat = {{ $order->restaurant->lat ?? 30.0444 }};
                    const restLng = {{ $order->restaurant->lng ?? 31.2357 }};
                    
                    const map = L.map(el).setView([deliveryLat, deliveryLng], 14);
                    window.trackMapInstance = map;

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap'
                    }).addTo(map);

                    // Restaurant
                    L.marker([restLat, restLng], {
                        icon: L.divIcon({className:'', html:'<div style="font-size:24px">🏪</div>', iconSize:[30,30], iconAnchor:[15,15]})
                    }).addTo(map).bindPopup('<b>{{ addslashes($order->restaurant->name ?? "Restaurant") }}</b>');

                    // Delivery location
                    L.marker([deliveryLat, deliveryLng], {
                        icon: L.divIcon({className:'', html:'<div style="font-size:24px">📍</div>', iconSize:[30,30], iconAnchor:[15,15]})
                    }).addTo(map).bindPopup('<b>Delivery Location</b><br>{{ addslashes($order->delivery_address) }}');

                    @if($order->rider)
                        // Rider actual or simulated near restaurant
                        const riderLat = {{ $order->rider->current_lat ?? 'restLat + 0.003' }};
                        const riderLng = {{ $order->rider->current_lng ?? 'restLng + 0.002' }};
                        
                        L.marker([riderLat, riderLng], {
                            icon: L.divIcon({className:'', html:'<div style="font-size:24px">🏍️</div>', iconSize:[30,30], iconAnchor:[15,15]})
                        }).addTo(map).bindPopup('<b>Rider: {{ addslashes($order->rider->name) }}</b>');
                    @endif

                    // Fit bounds
                    const bounds = L.latLngBounds([[restLat, restLng], [deliveryLat, deliveryLng]]);
                    map.fitBounds(bounds.pad(0.3));
                    setTimeout(() => map.invalidateSize(), 200);
                }

                document.addEventListener('DOMContentLoaded', initTrackMap);
                document.addEventListener('livewire:navigated', initTrackMap);
                window.addEventListener('init-track-map', initTrackMap);
            </script>

            {{-- Order Details --}}
            <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold text-white">Order #{{ $order->id }}</h1>
                    <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $order->status === 'delivered' ? 'bg-emerald-500/20 text-emerald-400' : ($order->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : 'bg-[#FF6B2B]/20 text-[#FF6B2B]') }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <p class="text-sm text-[#888] mb-1">🏪 {{ $order->restaurant->name ?? 'Restaurant' }}</p>
                <p class="text-sm text-[#666]">📍 {{ $order->delivery_address }}</p>
                @if($order->rider)<p class="text-sm text-[#888] mt-1">🏍️ Rider: {{ $order->rider->name }}</p>@endif
                @if($order->instructions)<p class="text-sm text-amber-400/70 mt-1">📝 {{ $order->instructions }}</p>@endif
            </div>

            {{-- Items --}}
            <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 mb-6">
                <h2 class="text-lg font-bold text-white mb-4">Items</h2>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm py-2 border-b border-[#2A2A2A] last:border-0">
                        <span class="text-[#B0B0B0]">{{ $item->quantity }}x {{ $item->menuItem->name ?? 'Item' }}</span>
                        <span class="text-white font-medium">EGP {{ number_format($item->price * $item->quantity, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between text-sm pt-2"><span class="text-[#666]">Delivery Fee</span><span class="text-white">EGP {{ number_format($order->delivery_fee, 2) }}</span></div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-[#2A2A2A]"><span class="text-white">Total</span><span class="text-[#FF6B2B]">EGP {{ number_format($order->total_amount, 2) }}</span></div>
                </div>
            </div>

            {{-- Actions --}}
            @if($order->status === 'placed')
            <button wire:click="cancelOrder" wire:confirm="Cancel this order?" class="px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 transition">Cancel Order</button>
            @endif

            @if($order->status === 'delivered' && !$hasReviewed)
            <button wire:click="$set('showReviewForm', true)" class="px-6 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl shadow-lg transition">⭐ Leave a Review</button>
            @endif

            @if($showReviewForm)
            <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 mt-4">
                <h2 class="text-lg font-bold text-white mb-6">Rate Your Experience</h2>
                
                <div class="flex flex-col md:flex-row gap-6">
                    {{-- Restaurant Rating --}}
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-white mb-2">Rate {{ $order->restaurant->name }}</h3>
                        <div class="flex gap-2 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                            <button wire:click="$set('restaurantRating', {{ $i }})" class="text-2xl {{ $restaurantRating >= $i ? 'text-amber-400' : 'text-slate-700' }} hover:text-amber-300 transition">★</button>
                            @endfor
                        </div>
                        <input wire:model="restaurantComment" type="text" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition text-sm" placeholder="How was the food?">
                    </div>

                    {{-- Rider Rating --}}
                    @if($order->rider)
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-white mb-2">Rate your Rider ({{ $order->rider->name }})</h3>
                        <div class="flex gap-2 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                            <button wire:click="$set('riderRating', {{ $i }})" class="text-2xl {{ $riderRating >= $i ? 'text-amber-400' : 'text-slate-700' }} hover:text-amber-300 transition">★</button>
                            @endfor
                        </div>
                        <input wire:model="riderComment" type="text" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition text-sm" placeholder="How was the delivery?">
                    </div>
                    @endif
                </div>

                @if(session('error'))
                    <p class="text-red-400 text-sm mt-4">{{ session('error') }}</p>
                @endif
                <button wire:click="submitReview" class="mt-6 px-6 py-2.5 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-semibold rounded-xl transition w-full md:w-auto">Submit Review</button>
            </div>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="w-full lg:w-72">
            <div class="sticky top-24 rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6">
                <h2 class="text-lg font-bold text-white mb-4">Order Timeline</h2>
                <div class="space-y-0">
                    @foreach($order->stateLogs as $i => $log)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full {{ $log->new_state === 'delivered' ? 'bg-emerald-400' : ($log->new_state === 'cancelled' ? 'bg-red-400' : 'bg-[#FF6B2B]') }}"></div>
                            @if(!$loop->last)<div class="w-0.5 h-8 bg-[#272727]"></div>@endif
                        </div>
                        <div class="pb-6">
                            <p class="text-sm font-semibold text-white -mt-0.5">{{ ucfirst(str_replace('_', ' ', $log->new_state)) }}</p>
                            <p class="text-xs text-[#666]">{{ $log->created_at->format('M d, H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
