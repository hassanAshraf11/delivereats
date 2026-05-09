<div class="flex min-h-screen" wire:poll.5s>
    {{-- Sidebar --}}
    <aside class="w-64 bg-[#111111] border-r border-[#2A2A2A] p-6 flex flex-col fixed h-full z-10">
        <a href="/" class="text-2xl font-black text-white mb-1"><img src="/delivereats_logo.svg" alt="DeliverEats" class="h-9"></a>
        <p class="text-xs text-[#666] mb-8">Rider Portal</p>
        
        @if($rider)
        <div class="mb-8 p-4 rounded-2xl border {{ $rider->is_online ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-[#1A1A1A] border-[#2A2A2A]' }} text-center transition-all duration-300">
            <div class="w-16 h-16 mx-auto rounded-full {{ $rider->is_online ? 'bg-emerald-500 shadow-lg shadow-emerald-500/40' : 'bg-slate-600' }} flex items-center justify-center text-white font-bold text-3xl mb-3 transition-all duration-300">
                🏍️
            </div>
            <p class="text-white font-bold">{{ Auth::user()->name }}</p>
            <p class="text-xs {{ $rider->is_online ? 'text-emerald-400' : 'text-[#888]' }} mb-4 font-medium uppercase tracking-wider">{{ $rider->is_online ? 'Online' : 'Offline' }}</p>
            
            <button wire:click="toggleStatus" class="w-full py-2.5 rounded-xl text-sm font-bold transition {{ $rider->is_online ? 'bg-white text-[#111] hover:bg-gray-200' : 'bg-emerald-500 text-white hover:bg-emerald-400' }}">
                {{ $rider->is_online ? 'Go Offline' : 'Go Online' }}
            </button>
        </div>
        @endif

        <nav class="space-y-1 flex-1">
            <button wire:click="switchTab('overview')" class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center gap-3 {{ $activeTab === 'overview' ? 'bg-[#FF6B2B]/10 text-[#FF6B2B]' : 'text-[#888] hover:bg-[#1A1A1A] hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Overview
            </button>
            <button wire:click="switchTab('orders')" class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center gap-3 {{ $activeTab === 'orders' ? 'bg-[#FF6B2B]/10 text-[#FF6B2B]' : 'text-[#888] hover:bg-[#1A1A1A] hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Active Deliveries
                @if($activeOrders->count() > 0)
                    <span class="ml-auto bg-[#FF6B2B] text-white text-xs px-2 py-0.5 rounded-full">{{ $activeOrders->count() }}</span>
                @endif
            </button>
            <button wire:click="switchTab('history')" class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center gap-3 {{ $activeTab === 'history' ? 'bg-[#FF6B2B]/10 text-[#FF6B2B]' : 'text-[#888] hover:bg-[#1A1A1A] hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                History
            </button>
            <button wire:click="switchTab('payouts')" class="w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition flex items-center gap-3 {{ $activeTab === 'payouts' ? 'bg-[#FF6B2B]/10 text-[#FF6B2B]' : 'text-[#888] hover:bg-[#1A1A1A] hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Earnings
            </button>
        </nav>

        <div class="pt-4 border-t border-[#2A2A2A]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-[#888] hover:text-[#FF6B2B] transition flex items-center gap-2 px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="ml-64 flex-1 p-8 bg-[#0a0f1a] min-h-screen">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        @if(!$rider)
            <div class="flex flex-col items-center justify-center h-full py-20">
                <div class="w-24 h-24 bg-[#1A1A1A] rounded-full flex items-center justify-center mb-6 border border-[#2A2A2A]">
                    <span class="text-4xl">⚠️</span>
                </div>
                <h1 class="text-3xl font-bold text-white mb-4">Account Not Fully Set Up</h1>
                <p class="text-[#888] text-center max-w-md">Your rider profile hasn't been completely created. Please contact an admin or complete your onboarding to start delivering.</p>
            </div>
        @else

            {{-- OVERVIEW TAB --}}
            @if($activeTab === 'overview')
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
                        <p class="text-[#888] mt-1">Here is what's happening today.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#FF6B2B]/5 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                        <p class="text-xs text-[#888] font-semibold uppercase tracking-wider mb-2">Today's Earnings</p>
                        <p class="text-3xl font-black text-[#FF6B2B]">EGP {{ number_format($todayEarnings, 2) }}</p>
                    </div>
                    
                    <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                        <p class="text-xs text-[#888] font-semibold uppercase tracking-wider mb-2">Today's Deliveries</p>
                        <p class="text-3xl font-black text-white">{{ $todayDeliveries }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                        <p class="text-xs text-[#888] font-semibold uppercase tracking-wider mb-2">Total Deliveries</p>
                        <p class="text-3xl font-black text-white">{{ $totalDeliveries }}</p>
                    </div>

                    <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-400/5 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                        <p class="text-xs text-[#888] font-semibold uppercase tracking-wider mb-2">Rating</p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-3xl font-black text-white">{{ $avgRating ? number_format($avgRating, 1) : 'New' }}</p>
                            @if($avgRating)
                                <span class="text-amber-400 text-xl">★</span>
                                <span class="text-xs text-[#666]">({{ $reviewCount }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <h2 class="text-xl font-bold text-white mb-4">Current Status</h2>
                        @if(!$rider->is_online)
                            <div class="rounded-2xl border border-dashed border-[#2A2A2A] p-10 text-center bg-[#111]">
                                <div class="w-20 h-20 bg-[#1A1A1A] rounded-full mx-auto flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-[#666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-2">You are currently Offline</h3>
                                <p class="text-[#888] mb-6">Go online to start receiving delivery requests.</p>
                                <button wire:click="toggleStatus" class="px-8 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-bold shadow-lg shadow-emerald-500/25 transition">
                                    Go Online Now
                                </button>
                            </div>
                        @elseif($activeOrders->isEmpty())
                            <div class="rounded-2xl border border-[#2A2A2A] bg-[#141414] p-10 text-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>
                                <div class="w-20 h-20 bg-emerald-500/10 rounded-full mx-auto flex items-center justify-center mb-4 relative z-10">
                                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping absolute"></div>
                                    <div class="w-3 h-3 bg-emerald-500 rounded-full relative"></div>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-2 relative z-10">Looking for deliveries...</h3>
                                <p class="text-[#888] relative z-10">You are online. Wait here for an order to be assigned to you.</p>
                            </div>
                        @else
                            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/5 p-6">
                                <h3 class="text-lg font-bold text-emerald-400 mb-2">You have active orders!</h3>
                                <p class="text-white mb-4">You have {{ $activeOrders->count() }} delivery in progress.</p>
                                <button wire:click="switchTab('orders')" class="px-6 py-2 rounded-xl bg-emerald-500 text-white font-bold text-sm">View Deliveries</button>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <h2 class="text-xl font-bold text-white mb-4">Recent Reviews</h2>
                        <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] p-6">
                            @if($recentReviews->isEmpty())
                                <p class="text-[#666] text-sm text-center py-4">No reviews yet.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($recentReviews as $review)
                                        <div class="border-b border-[#2A2A2A] last:border-0 pb-4 last:pb-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-semibold text-white">{{ $review->user->name ?? 'Customer' }}</span>
                                                <div class="flex text-amber-400 text-sm">
                                                    @for($i = 0; $i < $review->rating; $i++) ★ @endfor
                                                </div>
                                            </div>
                                            @if($review->comment)
                                                <p class="text-xs text-[#888] italic">"{{ $review->comment }}"</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ORDERS TAB --}}
            @if($activeTab === 'orders')
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Active Deliveries</h1>
                        <p class="text-[#888] mt-1">Orders you are currently assigned to.</p>
                    </div>
                </div>
                
                @if($activeOrders->isEmpty())
                    <div class="text-center py-20 rounded-2xl border border-dashed border-[#2A2A2A] bg-[#111]">
                        <div class="text-5xl mb-4">📭</div>
                        <h3 class="text-xl font-bold text-white mb-2">No active orders</h3>
                        <p class="text-[#888]">Stay online to receive new delivery requests.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($activeOrders as $order)
                        <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] overflow-hidden group">
                            <div class="bg-[#1A1A1A] p-4 flex justify-between items-center border-b border-[#2A2A2A]">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#FF6B2B]/10 flex items-center justify-center text-[#FF6B2B] font-bold">
                                        #{{ $order->id }}
                                    </div>
                                    <div>
                                        <span class="px-2.5 py-1 text-[10px] rounded-full bg-[#FF6B2B]/20 text-[#FF6B2B] uppercase font-bold tracking-wider">{{ str_replace('_', ' ', $order->status) }}</span>
                                        <p class="text-xs text-[#666] mt-1">{{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-[#666] uppercase tracking-wider font-bold mb-0.5">Delivery Fee</p>
                                    <p class="text-xl font-black text-emerald-400">EGP {{ number_format($order->delivery_fee, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row gap-6 relative">
                                    <!-- Connection Line for Desktop -->
                                    <div class="hidden md:block absolute top-1/2 left-8 right-8 h-0.5 bg-gradient-to-r from-[#FF6B2B]/20 via-[#2A2A2A] to-emerald-500/20 -translate-y-1/2 z-0"></div>
                                    
                                    <!-- Pickup Node -->
                                    <div class="flex-1 rounded-xl bg-[#0a0f1a] border border-[#2A2A2A] p-5 relative z-10 shadow-lg">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-8 h-8 rounded-full bg-[#FF6B2B]/20 flex items-center justify-center text-[#FF6B2B] text-sm">🏪</div>
                                            <p class="text-xs text-[#888] uppercase tracking-wider font-bold">Pickup From</p>
                                        </div>
                                        <p class="text-lg font-bold text-white mb-1">{{ $order->restaurant->name }}</p>
                                        <p class="text-sm text-[#888]">{{ $order->restaurant->address }}</p>
                                        <div class="mt-4 pt-4 border-t border-[#2A2A2A]">
                                            <p class="text-xs text-[#666] mb-2 font-bold">Items to pick up:</p>
                                            <ul class="text-sm text-[#ccc] space-y-1">
                                                @foreach($order->items as $item)
                                                    <li>• {{ $item->quantity }}x {{ $item->menuItem->name ?? 'Item' }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Delivery Node -->
                                    <div class="flex-1 rounded-xl bg-[#0a0f1a] border border-[#2A2A2A] p-5 relative z-10 shadow-lg">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-sm">📍</div>
                                            <p class="text-xs text-[#888] uppercase tracking-wider font-bold">Deliver To</p>
                                        </div>
                                        <p class="text-lg font-bold text-white mb-1">{{ $order->customer->name }}</p>
                                        <p class="text-sm text-[#888]">{{ $order->delivery_address }}</p>
                                        @if($order->instructions)
                                            <div class="mt-4 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-start gap-2">
                                                <span class="text-amber-500">📝</span> 
                                                <span>{{ $order->instructions }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-[#2A2A2A]">
                                    <a href="https://www.google.com/maps/dir/?api=1&origin={{ $rider->current_lat ?? $order->restaurant->lat }},{{ $rider->current_lng ?? $order->restaurant->lng }}&destination={{ $order->lat }},{{ $order->lng }}" target="_blank" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[#2A2A2A] text-white hover:bg-[#333] transition flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                        Open Map
                                    </a>
                                    
                                    @if(in_array($order->status, ['confirmed', 'preparing']))
                                        <button wire:click="pickupOrder({{ $order->id }})" class="px-8 py-2.5 rounded-xl text-sm font-bold bg-[#FF6B2B] text-white hover:bg-[#FF8A50] transition shadow-lg shadow-[#FF6B2B]/20">
                                            Confirm Pickup
                                        </button>
                                    @elseif($order->status === 'on_the_way')
                                        <button wire:click="deliverOrder({{ $order->id }})" class="px-8 py-2.5 rounded-xl text-sm font-bold bg-emerald-500 text-white hover:bg-emerald-400 transition shadow-lg shadow-emerald-500/20">
                                            Mark Delivered
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            @endif

            {{-- HISTORY TAB --}}
            @if($activeTab === 'history')
                <div class="flex justify-between items-end mb-8">
                    <h1 class="text-3xl font-bold text-white">Delivery History</h1>
                </div>
                
                <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#2A2A2A] bg-[#1A1A1A]">
                                <th class="p-5 text-xs text-[#888] font-semibold uppercase tracking-wider">Order</th>
                                <th class="p-5 text-xs text-[#888] font-semibold uppercase tracking-wider">Date</th>
                                <th class="p-5 text-xs text-[#888] font-semibold uppercase tracking-wider">Destination</th>
                                <th class="p-5 text-xs text-[#888] font-semibold uppercase tracking-wider">Status</th>
                                <th class="p-5 text-xs text-[#888] font-semibold uppercase tracking-wider text-right">Earned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pastOrders as $order)
                            <tr class="border-b border-[#2A2A2A] last:border-0 hover:bg-[#1A1A1A] transition">
                                <td class="p-5 text-sm font-bold text-white">#{{ $order->id }}</td>
                                <td class="p-5 text-sm text-[#888]">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td class="p-5 text-sm text-[#ccc] truncate max-w-[200px]">{{ $order->delivery_address }}</td>
                                <td class="p-5">
                                    <span class="px-2.5 py-1 text-xs rounded-md font-bold {{ $order->status === 'delivered' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="p-5 text-sm font-bold text-emerald-400 text-right">EGP {{ number_format($order->delivery_fee, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <p class="text-3xl mb-3">📜</p>
                                    <p class="text-[#888]">No past deliveries yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- PAYOUTS TAB --}}
            @if($activeTab === 'payouts')
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Earnings & Payouts</h1>
                        <p class="text-[#888] mt-1">Track your income and pending transfers.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-[#141414] to-[#1A1A1A] border border-[#2A2A2A] relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
                        <p class="text-xs text-[#888] font-bold uppercase tracking-wider mb-2">Total Earned All Time</p>
                        <p class="text-3xl font-black text-emerald-400">EGP {{ number_format($totalEarnings, 2) }}</p>
                    </div>
                    
                    <div class="p-6 rounded-2xl bg-[#141414] border border-[#2A2A2A]">
                        <p class="text-xs text-[#888] font-bold uppercase tracking-wider mb-2">Available for Payout</p>
                        <p class="text-3xl font-black text-white">EGP {{ number_format($payouts->where('is_paid', false)->sum('rider_amount'), 2) }}</p>
                    </div>
                    
                    <div class="p-6 rounded-2xl bg-[#141414] border border-[#2A2A2A]">
                        <p class="text-xs text-[#888] font-bold uppercase tracking-wider mb-2">Total Paid Out</p>
                        <p class="text-3xl font-black text-[#666]">EGP {{ number_format($payouts->where('is_paid', true)->sum('rider_amount'), 2) }}</p>
                    </div>
                </div>

                <div class="rounded-2xl bg-[#141414] border border-[#2A2A2A] overflow-hidden">
                    <div class="p-5 border-b border-[#2A2A2A] bg-[#1A1A1A]">
                        <h3 class="text-lg font-bold text-white">Payout History</h3>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#2A2A2A] bg-[#1A1A1A]/50">
                                <th class="p-4 text-xs text-[#888] font-semibold uppercase tracking-wider">Order</th>
                                <th class="p-4 text-xs text-[#888] font-semibold uppercase tracking-wider">Date</th>
                                <th class="p-4 text-xs text-[#888] font-semibold uppercase tracking-wider">Amount</th>
                                <th class="p-4 text-xs text-[#888] font-semibold uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                            <tr class="border-b border-[#2A2A2A] last:border-0 hover:bg-[#1A1A1A] transition">
                                <td class="p-4 text-sm font-medium text-white">Order #{{ $payout->order_id }}</td>
                                <td class="p-4 text-sm text-[#888]">{{ $payout->created_at->format('M d, Y h:i A') }}</td>
                                <td class="p-4 text-sm font-bold text-emerald-400">EGP {{ number_format($payout->rider_amount, 2) }}</td>
                                <td class="p-4 text-right">
                                    <span class="px-2.5 py-1 text-xs rounded-md font-bold uppercase tracking-wider {{ $payout->is_paid ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                        {{ $payout->is_paid ? 'Paid' : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center">
                                    <p class="text-3xl mb-3">💰</p>
                                    <p class="text-[#888]">No earnings to show yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        @endif
    </main>
</div>
