<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeliverEats — Food. Fast. Everywhere.</title>
    <meta name="description" content="Multi-restaurant food delivery platform with real-time tracking, surge pricing, and live GPS dispatch.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-glow {
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-[#0D0D0D] text-white antialiased">
    {{-- Hero --}}
    <section class="relative min-h-screen flex flex-col items-center justify-center px-6 overflow-hidden">
        {{-- Background Glows --}}
        <div class="hero-glow bg-[#FF6B2B] -top-40 -left-40" style="position:absolute"></div>
        <div class="hero-glow bg-[#FFC542] -bottom-40 -right-40" style="position:absolute; opacity:0.1"></div>

        {{-- Logo --}}
        <img src="/delivereats_logo.svg" alt="DeliverEats Logo" class="w-80 md:w-[420px] mb-8 animate-float">

        {{-- Tagline --}}
        <p class="text-lg md:text-xl text-[#888] max-w-lg text-center mb-10 leading-relaxed">
            Multi-restaurant food delivery ecosystem with real-time order tracking, live GPS dispatch, and dynamic surge pricing.
        </p>

        {{-- CTAs --}}
        <div class="flex flex-wrap gap-4 justify-center mb-16">
            <a href="{{ route('browse') }}" class="px-8 py-4 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-bold text-lg rounded-2xl shadow-lg shadow-[#FF6B2B]/30 transition-all hover:-translate-y-1 flex items-center gap-3">
                🍽️ Order Now →
            </a>
            <a href="{{ route('register') }}" class="px-8 py-4 bg-[#1A1A1A] hover:bg-[#272727] text-white font-semibold text-lg rounded-2xl border border-[#2A2A2A] hover:border-[#FF6B2B]/30 transition-all flex items-center gap-3">
                🏪 Partner with Us
            </a>
            <a href="{{ route('login') }}" class="px-8 py-4 bg-[#1A1A1A] hover:bg-[#272727] text-white font-semibold text-lg rounded-2xl border border-[#2A2A2A] hover:border-[#FF6B2B]/30 transition-all flex items-center gap-3">
                Login
            </a>
        </div>

        {{-- Feature Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-4xl w-full">
            @foreach([
                ['⚡', 'Real-Time Tracking', 'Live order status updates and rider GPS location broadcasting via Pusher.', '#FF6B2B'],
                ['💰', 'Surge Pricing Engine', 'Strategy Pattern with flat, multiplier, and time-based pricing strategies.', '#FFC542'],
                ['🔒', 'Order State Machine', 'Strict FSM with transition guards, event sourcing logs, and invalid-jump prevention.', '#22c55e'],
            ] as [$icon, $title, $desc, $color])
            <div class="glass-card p-6 group">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4" style="background: {{ $color }}15">{{ $icon }}</div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $title }}</h3>
                <p class="text-sm text-[#888] leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>

        {{-- More Features --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-4xl w-full mt-5">
            @foreach([
                ['🏍️', 'GPS Rider Dispatch', 'Nearest available rider assigned automatically using distance matrix calculations.', '#FF6B2B'],
                ['💳', 'Revenue Splitting', 'Automated payout calculation: platform commission, restaurant share, and rider delivery fee.', '#FFC542'],
                ['⭐', 'Ratings & Reviews', 'Polymorphic review system for restaurants and riders with star ratings.', '#22c55e'],
            ] as [$icon, $title, $desc, $color])
            <div class="glass-card p-6 group">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4" style="background: {{ $color }}15">{{ $icon }}</div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $title }}</h3>
                <p class="text-sm text-[#888] leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>


    </section>

    <footer class="border-t border-[#1A1A1A] py-8 text-center text-xs text-[#444]">
        &copy; {{ date('Y') }} DeliverEats. Food. Fast. Everywhere.
    </footer>
</body>
</html>
