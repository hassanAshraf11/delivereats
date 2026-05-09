<div class="glass rounded-2xl p-8">
    <h1 class="text-2xl font-bold text-white mb-6 text-center">Create Account</h1>
    <form wire:submit="register" class="space-y-4">
        <div>
            <label class="text-xs text-[#888] block mb-1">Full Name</label>
            <input wire:model="name" type="text" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition" placeholder="Your name">
            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">Email</label>
            <input wire:model="email" type="email" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition" placeholder="you@example.com">
            @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">I am a...</label>
            <div class="grid grid-cols-3 gap-3">
                <label class="relative cursor-pointer">
                    <input wire:model="role" type="radio" value="customer" class="peer sr-only">
                    <div class="p-4 rounded-xl border border-[#2A2A2A] text-center peer-checked:border-[#FF6B2B] peer-checked:bg-[#FF6B2B]/10 transition">
                        <p class="text-2xl mb-1">🍽️</p>
                        <p class="text-sm font-semibold text-white">Customer</p>
                        <p class="text-xs text-[#666]">Order food</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input wire:model="role" type="radio" value="restaurant_owner" class="peer sr-only">
                    <div class="p-4 rounded-xl border border-[#2A2A2A] text-center peer-checked:border-[#FF6B2B] peer-checked:bg-[#FF6B2B]/10 transition">
                        <p class="text-2xl mb-1">🏪</p>
                        <p class="text-sm font-semibold text-white">Restaurant</p>
                        <p class="text-xs text-[#666]">Sell food</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input wire:model="role" type="radio" value="rider" class="peer sr-only">
                    <div class="p-4 rounded-xl border border-[#2A2A2A] text-center peer-checked:border-[#FF6B2B] peer-checked:bg-[#FF6B2B]/10 transition">
                        <p class="text-2xl mb-1">🏍️</p>
                        <p class="text-sm font-semibold text-white">Rider</p>
                        <p class="text-xs text-[#666]">Deliver food</p>
                    </div>
                </label>
            </div>
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">Password</label>
            <input wire:model="password" type="password" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition" placeholder="Min 8 characters">
            @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">Confirm Password</label>
            <input wire:model="password_confirmation" type="password" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition" placeholder="••••••••">
        </div>
        <button type="submit" class="w-full py-3 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-semibold rounded-xl shadow-lg shadow-[#FF6B2B]/25 transition">Create Account</button>
    </form>
    <p class="text-center text-sm text-[#666] mt-6">Already have an account? <a href="{{ route('login') }}" class="text-[#FF6B2B] hover:text-indigo-300">Sign in</a></p>
</div>
