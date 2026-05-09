<div>
    <h1 class="text-3xl font-bold text-white mb-2">Checkout</h1>
    <p class="text-[#666] mb-8">{{ $restaurantName ? 'Ordering from ' . $restaurantName : 'Your cart is empty' }}</p>

    @if(count($cart) > 0)
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Cart Items --}}
        <div class="flex-1">
            <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6">
                <h2 class="text-lg font-bold text-white mb-4">Order Items</h2>
                <div class="space-y-4">
                    @foreach($cart as $key => $item)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#1A1A1A]">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-white">{{ $item['name'] }}</p>
                            <p class="text-xs text-[#666]">EGP {{ number_format($item['price'], 2) }} each</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" class="w-7 h-7 rounded-lg bg-[#272727] hover:bg-[#333] text-white flex items-center justify-center text-sm font-bold">−</button>
                            <span class="text-white font-semibold w-6 text-center">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" class="w-7 h-7 rounded-lg bg-[#272727] hover:bg-[#333] text-white flex items-center justify-center text-sm font-bold">+</button>
                            <span class="text-white font-semibold ml-2 w-24 text-right">EGP {{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            <button wire:click="removeItem('{{ $key }}')" class="text-red-400 hover:text-red-300 ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button wire:click="clearCart" class="mt-4 text-xs text-red-400 hover:text-red-300">Clear Cart</button>
            </div>

            {{-- Delivery Details --}}
            <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 mt-6">
                <h2 class="text-lg font-bold text-white mb-4">Delivery Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-[#888] block mb-1">Delivery Address *</label>
                        <input wire:model="deliveryAddress" type="text" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition text-sm" placeholder="Street, Building, Floor, Apartment">
                        @error('deliveryAddress')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs text-[#888] block mb-1">📍 Pin Your Location on the Map</label>
                        <div id="cart-map-picker" style="height: 250px; border-radius: 12px;" class="border border-[#2A2A2A]"></div>
                        <p class="text-xs text-[#666] mt-1">Click on the map to set your delivery location</p>
                    </div>
                    <div>
                        <label class="text-xs text-[#888] block mb-1">Special Instructions</label>
                        <input wire:model="instructions" type="text" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition text-sm" placeholder="e.g. Ring the doorbell, no onions, etc.">
                    </div>
                </div>
            </div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const el = document.getElementById('cart-map-picker');
                    if (!el || el._leaflet_id) return;
                    const lat = {{ $this->lat }}, lng = {{ $this->lng }};
                    const map = L.map(el).setView([lat, lng], 14);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap'
                    }).addTo(map);
                    let marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                    marker.bindPopup('Delivery Location').openPopup();
                    map.on('click', function(e) {
                        marker.setLatLng(e.latlng);
                        @this.set('lat', e.latlng.lat);
                        @this.set('lng', e.latlng.lng);
                    });
                    marker.on('dragend', function(e) {
                        const pos = marker.getLatLng();
                        @this.set('lat', pos.lat);
                        @this.set('lng', pos.lng);
                    });
                    setTimeout(() => map.invalidateSize(), 200);
                });
            </script>
        </div>

        {{-- Order Summary --}}
        <div class="w-full lg:w-80">
            <div class="sticky top-24 rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6">
                <h2 class="text-lg font-bold text-white mb-4">Order Summary</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-[#888]">Subtotal</span><span class="text-white">EGP {{ number_format($subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-[#888]">Delivery Fee</span><span class="text-white">EGP {{ number_format($deliveryFee, 2) }}</span></div>
                    <div class="border-t border-[#2A2A2A] pt-3 flex justify-between text-lg">
                        <span class="font-bold text-white">Total</span>
                        <span class="font-bold text-[#FF6B2B]">EGP {{ number_format($total, 2) }}</span>
                    </div>
                </div>

                @auth
                <button wire:click="placeOrder" class="mt-6 w-full py-3.5 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-bold rounded-xl shadow-lg shadow-[#FF6B2B]/25 transition text-sm">
                    Place Order — EGP {{ number_format($total, 2) }}
                </button>
                <p class="text-xs text-[#555] text-center mt-3">Payment is simulated for this prototype</p>
                @else
                <a href="{{ route('login') }}" class="mt-6 block w-full py-3.5 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-bold rounded-xl shadow-lg shadow-[#FF6B2B]/25 transition text-sm text-center">Login to Place Order</a>
                @endauth
            </div>
        </div>
    </div>
    @else
    <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-16 text-center">
        <p class="text-4xl mb-4">🛒</p>
        <p class="text-xl text-[#888] mb-2">Your cart is empty</p>
        <p class="text-[#666] text-sm mb-6">Browse restaurants and add items to get started</p>
        <a href="{{ route('browse') }}" class="inline-flex px-6 py-3 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-semibold rounded-xl shadow-lg transition">Browse Restaurants</a>
    </div>
    @endif
</div>
