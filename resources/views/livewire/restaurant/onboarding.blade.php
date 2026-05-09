<div class="glass rounded-2xl p-8 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-white mb-2 text-center">Register Your Restaurant</h1>
    <p class="text-[#666] text-sm text-center mb-6">Fill in your restaurant details to get started on DeliverEats</p>

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="text-xs text-[#888] block mb-1">Restaurant Name *</label>
            <input wire:model="name" type="text" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition" placeholder="Your restaurant name">
            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">Cuisine Type *</label>
            <select wire:model="cuisine_type" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition">
                <option value="">Select cuisine</option>
                <option value="American">American</option>
                <option value="Italian">Italian</option>
                <option value="Japanese">Japanese</option>
                <option value="Middle Eastern">Middle Eastern</option>
                <option value="Chinese">Chinese</option>
                <option value="Indian">Indian</option>
                <option value="Mexican">Mexican</option>
                <option value="Thai">Thai</option>
                <option value="Other">Other</option>
            </select>
            @error('cuisine_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">Description *</label>
            <textarea wire:model="description" rows="3" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition" placeholder="Tell customers about your restaurant..."></textarea>
            @error('description')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-xs text-[#888] block mb-1">Address *</label>
            <input wire:model="address" type="text" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition" placeholder="Full street address">
            @error('address')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-[#888] block mb-1">Opens At</label>
                <input wire:model="opening_time" type="time" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition">
            </div>
            <div>
                <label class="text-xs text-[#888] block mb-1">Closes At</label>
                <input wire:model="closing_time" type="time" class="w-full px-4 py-3 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#FF6B2B]/50 transition">
            </div>
        </div>
        <button type="submit" class="w-full py-3.5 bg-[#FF6B2B] hover:bg-[#FF8A50] text-white font-bold rounded-xl shadow-lg shadow-[#FF6B2B]/25 transition mt-2">Register Restaurant</button>
    </form>
    <p class="text-xs text-[#555] text-center mt-4">Your restaurant will go live once approved by our team.</p>
</div>
