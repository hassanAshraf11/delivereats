<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DeliverEats' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#0D0D0D] text-[#B0B0B0] antialiased min-h-screen">
    {{-- Top Nav --}}
    <nav class="sticky top-0 z-50 bg-[#0D0D0D]/90 backdrop-blur-xl border-b border-[#1A1A1A]">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('browse') }}" class="flex items-center gap-2">
                <img src="/delivereats_logo.svg" alt="DeliverEats" class="h-10">
            </a>
            <div class="flex items-center gap-5">
                <a href="{{ route('browse') }}" class="text-sm text-[#888] hover:text-white transition">Browse</a>
                <a href="{{ route('cart') }}" class="relative text-sm text-[#888] hover:text-white transition flex items-center gap-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Cart
                    @php $cc = collect(session('cart', []))->sum('quantity'); @endphp
                    @if($cc > 0)<span class="absolute -top-2 -right-3 w-5 h-5 bg-[#FF6B2B] text-white text-xs rounded-full flex items-center justify-center font-bold">{{ $cc }}</span>@endif
                </a>
                @auth
                    <a href="{{ route('my.orders') }}" class="text-sm text-[#888] hover:text-white transition">My Orders</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-[#555] hover:text-red-400 transition">Logout</button>
                    </form>
                    <span class="text-xs text-[#666] border border-[#2A2A2A] px-2.5 py-1 rounded-lg">{{ Auth::user()->name }}</span>
                @else
                    <a href="{{ route('login') }}" class="text-sm px-5 py-2 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white rounded-xl font-semibold transition shadow-sm shadow-[#FF6B2B]/20">Login</a>
                    <a href="{{ route('register') }}" class="text-sm text-[#888] hover:text-white transition">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8">
        @if(session('success'))<div class="mb-4 p-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">{{ session('error') }}</div>@endif
        {{ $slot }}
    </main>

    <footer class="border-t border-[#1A1A1A] py-8 text-center text-xs text-[#444]">
        &copy; {{ date('Y') }} DeliverEats. Food. Fast. Everywhere.
    </footer>
    @livewireScripts
</body>
</html>
