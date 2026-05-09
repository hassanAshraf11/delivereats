<div>
    {{-- Restaurant Header --}}
    @if($restaurant->cover)
    <div class="rounded-2xl overflow-hidden border border-[#2A2A2A] mb-8 relative">
        <img src="{{ asset('storage/' . $restaurant->cover) }}" class="w-full h-48 object-cover" alt="{{ $restaurant->name }}">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0a0f1a] via-transparent to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6 flex items-end gap-4">
            @if($restaurant->logo)
            <img src="{{ asset('storage/' . $restaurant->logo) }}" class="w-16 h-16 rounded-xl object-cover border-2 border-[#2A2A2A] shadow-lg flex-shrink-0">
            @endif
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">{{ $restaurant->name }}</h1>
                <p class="text-[#B0B0B0] text-sm">{{ $restaurant->description }}</p>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-3 text-sm mb-8 px-1">
        <span class="px-3 py-1 bg-[#FF6B2B]/20 text-[#FF6B2B] rounded-full font-medium">{{ $restaurant->cuisine_type }}</span>
        <span class="text-[#666]">⏰ {{ $restaurant->opening_time }} - {{ $restaurant->closing_time }}</span>
        <span class="text-[#666]">📍 {{ $restaurant->address }}</span>
        @if($restaurant->reviews_avg_rating)
        <span class="flex items-center gap-1 text-amber-400 font-semibold">★ {{ number_format($restaurant->reviews_avg_rating, 1) }} <span class="text-[#666] font-normal">({{ $restaurant->reviews_count }} reviews)</span></span>
        @endif
    </div>
    @else
    <div class="rounded-2xl bg-gradient-to-r from-[#FF6B2B]/20 via-[#FF8A50]/15 to-[#FFC542]/10 border border-[#2A2A2A] p-8 mb-8">
        <div class="flex items-start gap-4">
            @if($restaurant->logo)
            <img src="{{ asset('storage/' . $restaurant->logo) }}" class="w-16 h-16 rounded-xl object-cover border border-[#2A2A2A] flex-shrink-0">
            @endif
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">{{ $restaurant->name }}</h1>
                <p class="text-[#888] mb-3">{{ $restaurant->description }}</p>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="px-3 py-1 bg-[#FF6B2B]/20 text-[#FF6B2B] rounded-full font-medium">{{ $restaurant->cuisine_type }}</span>
                    <span class="text-[#666]">⏰ {{ $restaurant->opening_time }} - {{ $restaurant->closing_time }}</span>
                    <span class="text-[#666]">📍 {{ $restaurant->address }}</span>
                    @if($restaurant->reviews_avg_rating)
                    <span class="flex items-center gap-1 text-amber-400 font-semibold">★ {{ number_format($restaurant->reviews_avg_rating, 1) }} <span class="text-[#666] font-normal">({{ $restaurant->reviews_count }} reviews)</span></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="flex gap-8">
        {{-- Menu --}}
        <div class="flex-1">
            @foreach($restaurant->menuCategories as $category)
            @if($category->menuItems->count())
            <div class="mb-8">
                <h2 class="text-xl font-bold text-white mb-4 pb-2 border-b border-[#2A2A2A]">{{ $category->name }}</h2>
                <div class="space-y-3">
                    @foreach($category->menuItems as $item)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-[#141414] border border-[#2A2A2A] hover:border-[#FF6B2B]/20 transition group">
                        <div class="flex items-center gap-4 flex-1 mr-4">
                            @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0 border border-[#2A2A2A]" alt="{{ $item->name }}">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-white">{{ $item->name }}</h3>
                                <p class="text-xs text-[#666] mt-0.5 line-clamp-1">{{ $item->description }}</p>
                                @if($item->variants->count())
                                <div class="flex gap-1 mt-1">
                                    @foreach($item->variants as $v)
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-[#1A1A1A] text-[#888]">{{ $v->name }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[#FF6B2B] font-bold text-sm whitespace-nowrap">EGP {{ number_format($item->base_price, 2) }}</span>
                            <button wire:click="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->base_price }})" class="w-8 h-8 rounded-lg bg-[#FF6B2B] hover:bg-[#FF8A50] text-white flex items-center justify-center text-lg font-bold transition opacity-0 group-hover:opacity-100 focus:opacity-100">+</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>

        {{-- Cart Sidebar --}}
        <div class="w-80 hidden lg:block">
            <div class="sticky top-24 rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6">
                <h3 class="text-lg font-bold text-white mb-4">Your Order</h3>
                @if(count($cart) > 0)
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                    @foreach($cart as $key => $item)
                    @if(($item['restaurant_id'] ?? null) == $restaurantId)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex-1 min-w-0 mr-2">
                            <p class="text-white truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-[#666]">{{ $item['quantity'] }}x EGP {{ number_format($item['price'], 2) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-white font-semibold text-xs">EGP {{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            <button wire:click="removeFromCart('{{ $key }}')" class="text-red-400 hover:text-red-300 text-xs">✕</button>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                <div class="border-t border-[#2A2A2A] pt-3 mb-4">
                    <div class="flex justify-between text-sm"><span class="text-[#888]">Subtotal</span><span class="text-white font-semibold">EGP {{ number_format($cartTotal, 2) }}</span></div>
                </div>
                <a href="{{ route('cart') }}" class="block w-full text-center py-3 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-semibold rounded-xl shadow-lg shadow-[#FF6B2B]/25 transition">Go to Checkout →</a>
                @else
                <p class="text-[#666] text-sm text-center py-4">Add items to start your order</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Mobile Cart Bar --}}
    @if($cartCount > 0)
    <div class="lg:hidden fixed bottom-0 left-0 right-0 p-4 bg-[#0a0f1a]/95 backdrop-blur-xl border-t border-[#2A2A2A] z-50">
        <a href="{{ route('cart') }}" class="flex items-center justify-between w-full py-3 px-6 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-semibold rounded-xl shadow-lg transition">
            <span>{{ $cartCount }} items</span>
            <span>View Cart · EGP {{ number_format($cartTotal, 2) }}</span>
        </a>
    </div>
    @endif

    {{-- Reviews --}}
    @if($reviews->count())
    <div class="mt-12">
        <h2 class="text-xl font-bold text-white mb-4">Customer Reviews</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($reviews as $review)
            <div class="p-4 rounded-xl bg-[#141414] border border-[#2A2A2A]">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-white">{{ $review->user->name ?? 'Customer' }}</p>
                    <span class="text-amber-400 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                </div>
                @if($review->comment)<p class="text-sm text-[#888]">{{ $review->comment }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
