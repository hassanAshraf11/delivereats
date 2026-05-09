<div>
    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Restaurants</h1>
            <p class="text-[#666] mt-1">Find your favourite food from {{ $restaurants->count() }} places</p>
        </div>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search restaurants..." class="w-full md:w-72 px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition text-sm">
    </div>

    {{-- Cuisine Filters --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <button wire:click="$set('cuisine', '')" class="px-4 py-2 rounded-full text-sm font-medium transition {{ $cuisine === '' ? 'bg-[#FF6B2B] text-white' : 'bg-[#1A1A1A] text-[#888] hover:text-white border border-[#2A2A2A]' }}">All</button>
        @foreach($cuisines as $c)
        <button wire:click="$set('cuisine', '{{ $c }}')" class="px-4 py-2 rounded-full text-sm font-medium transition {{ $cuisine === $c ? 'bg-[#FF6B2B] text-white' : 'bg-[#1A1A1A] text-[#888] hover:text-white border border-[#2A2A2A]' }}">{{ $c }}</button>
        @endforeach
    </div>

    {{-- Restaurant Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($restaurants as $restaurant)
        <a href="{{ route('restaurant.show', $restaurant->id) }}" class="group block rounded-2xl bg-[#141414] border border-[#2A2A2A] overflow-hidden hover:border-[#FF6B2B]/30 hover:shadow-xl hover:shadow-[#FF6B2B]/5 transition-all duration-300 hover:-translate-y-1">
            {{-- Cover --}}
            @if($restaurant->cover)
            <div class="h-36 relative overflow-hidden">
                <img src="{{ asset('storage/' . $restaurant->cover) }}" class="w-full h-full object-cover" alt="{{ $restaurant->name }}">
                @if($restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" class="absolute bottom-3 left-3 w-12 h-12 rounded-xl object-cover border-2 border-[#141414] shadow-lg" alt="{{ $restaurant->name }}">
                @endif
            </div>
            @else
            <div class="h-36 bg-gradient-to-br from-[#FF6B2B]/20 via-[#FF8A50]/15 to-[#FFC542]/10 flex items-center justify-center relative">
                <span class="text-5xl">{{ ['🍔','🍕','🍣','🥙','🥡'][array_search($restaurant->cuisine_type, ['American','Italian','Japanese','Middle Eastern','Chinese']) ?: 0] }}</span>
                @if($restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" class="absolute bottom-3 left-3 w-12 h-12 rounded-xl object-cover border-2 border-[#141414] shadow-lg" alt="{{ $restaurant->name }}">
                @endif
            </div>
            @endif
            <div class="p-5">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-lg font-bold text-white group-hover:text-[#FF6B2B] transition">{{ $restaurant->name }}</h3>
                    @if($restaurant->reviews_avg_rating)
                    <span class="flex items-center gap-1 text-sm text-amber-400 font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        {{ number_format($restaurant->reviews_avg_rating, 1) }}
                        <span class="text-[#666] text-xs">({{ $restaurant->reviews_count }})</span>
                    </span>
                    @endif
                </div>
                <p class="text-sm text-[#666] mb-3 line-clamp-2">{{ $restaurant->description }}</p>
                <div class="flex items-center gap-3 text-xs text-[#666]">
                    <span class="px-2 py-1 bg-[#1A1A1A] rounded-lg">{{ $restaurant->cuisine_type }}</span>
                    <span>⏰ {{ $restaurant->opening_time }} - {{ $restaurant->closing_time }}</span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-3 text-center py-16">
            <p class="text-4xl mb-4">🔍</p>
            <p class="text-[#666] text-lg">No restaurants found.</p>
            <p class="text-[#555] text-sm mt-1">Try a different search or filter.</p>
        </div>
        @endforelse
    </div>
</div>
