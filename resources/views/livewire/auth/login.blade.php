<div class="glass rounded-2xl p-8">
    @if(session('success'))<div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm text-center">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">{{ session('error') }}</div>@endif
    <h1 class="text-2xl font-bold text-white mb-6 text-center">Welcome Back</h1>
    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="text-xs text-[#888] block mb-1">Email</label>
            <input wire:model="email" type="email" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 focus:border-[#FF6B2B] transition" placeholder="you@example.com">
            @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">Password</label>
            <input wire:model="password" type="password" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 focus:border-[#FF6B2B] transition" placeholder="••••••••">
        </div>
        <label class="flex items-center gap-2 text-sm text-[#888]">
            <input wire:model="remember" type="checkbox" class="rounded bg-[#1A1A1A] border-[#2A2A2A] text-indigo-600 focus:ring-[#FF6B2B]">
            Remember me
        </label>
        <button type="submit" class="w-full py-3 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-semibold rounded-xl shadow-lg shadow-[#FF6B2B]/25 transition">Sign In</button>
    </form>
    <p class="text-center text-sm text-[#666] mt-6">Don't have an account? <a href="{{ route('register') }}" class="text-[#FF6B2B] hover:text-indigo-300">Register</a></p>
</div>
