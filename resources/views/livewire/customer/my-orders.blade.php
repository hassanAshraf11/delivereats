<div>
    <h1 class="text-3xl font-bold text-white mb-8">My Orders</h1>
    @if($orders->count())
    <div class="space-y-4">
        @foreach($orders as $order)
        <a href="{{ route('track.order', $order->id) }}" class="block rounded-2xl bg-[#141414] border border-[#2A2A2A] p-5 hover:border-[#FF6B2B]/30 transition group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <span class="text-lg font-bold text-white group-hover:text-[#FF6B2B] transition">Order #{{ $order->id }}</span>
                    <span class="text-sm text-[#666]">{{ $order->restaurant->name ?? '' }}</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->status === 'delivered' ? 'bg-emerald-500/20 text-emerald-400' : ($order->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : ($order->status === 'placed' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-[#FF6B2B]/20 text-[#FF6B2B]')) }}">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div class="flex items-center gap-4 text-sm text-[#666]">
                <span>{{ $order->items->count() }} items</span>
                <span>EGP {{ number_format($order->total_amount, 2) }}</span>
                <span>{{ $order->created_at->diffForHumans() }}</span>
                @if($order->rider)<span>🏍️ {{ $order->rider->name }}</span>@endif
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-16 text-center">
        <p class="text-4xl mb-4">📦</p>
        <p class="text-xl text-[#888] mb-2">No orders yet</p>
        <a href="{{ route('browse') }}" class="inline-flex mt-4 px-6 py-3 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-semibold rounded-xl transition">Start Ordering</a>
    </div>
    @endif
</div>
