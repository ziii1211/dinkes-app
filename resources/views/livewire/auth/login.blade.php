<div class="flex items-center justify-center min-h-screen px-4 py-10">
    <div class="max-w-[420px] w-full bg-white rounded-[36px] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] p-10 sm:p-12 border border-gray-50 relative z-10">
        
        <div class="text-center mb-10 flex flex-col items-center justify-center space-y-6">
            <img src="{{ asset('logo-prov-kalsel.png') }}" alt="Logo Provinsi Kalsel" class="h-20 w-auto object-contain">
            
            <img src="{{ asset('logo-sakip (1).png') }}" alt="Logo SAKIP" class="h-24 w-auto object-contain drop-shadow-sm">
        </div>

        <form wire:submit="login" class="space-y-5">
            
            <div>
                <input type="text" 
                       wire:model="email" 
                       class="w-full bg-[#f8fafd] border border-gray-100 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block p-3.5 outline-none placeholder-gray-400 transition-all" 
                       placeholder="Username">
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <input type="password" 
                       wire:model="password" 
                       class="w-full bg-[#f8fafd] border border-gray-100 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block p-3.5 outline-none placeholder-gray-400 transition-all" 
                       placeholder="Password">
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="relative">
                <label class="absolute -top-2 left-3 bg-white px-1 text-[11px] font-medium text-gray-400">Periode</label>
                <select wire:model="periode" class="w-full bg-white border border-gray-100 text-gray-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block p-3.5 outline-none appearance-none cursor-pointer transition-all pt-4">
                    <option value="2025-2029">2025 - 2029</option>
                    <option value="2030-2034">2030 - 2034</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <div class="flex items-center pl-1">
                <input id="remember-me" wire:model="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-[4px] cursor-pointer">
                <label for="remember-me" class="ml-2.5 block text-sm text-gray-500 cursor-pointer font-medium">
                    Remember me
                </label>
            </div>

            <button type="submit" class="w-full text-white bg-[#0d6efd] hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-semibold rounded-xl text-sm px-5 py-3.5 text-center transition-all shadow-[0_4px_10px_rgba(13,110,253,0.2)] hover:shadow-[0_6px_15px_rgba(13,110,253,0.3)]">
                <span wire:loading.remove>Login</span>
                <span wire:loading class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-xs text-gray-400 font-medium">&copy; 2025</p>
        </div>
    </div>
</div>