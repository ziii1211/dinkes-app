<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-900 font-sans">
    
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0% { transform: translateX(-150%); }
            100% { transform: translateX(150%); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-shimmer { animation: shimmer 2.5s infinite; }
    </style>

    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1964&auto=format&fit=crop" 
             alt="Background" 
             class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/90 via-blue-900/80 to-slate-900/90 mix-blend-multiply"></div>
        
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>

    <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob animation-delay-4000"></div>

    <div class="relative z-10 w-full max-w-[440px] p-6 mx-4 animate-fade-in-up">
        
        <div class="absolute inset-0 bg-white/5 backdrop-blur-2xl rounded-[32px] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.36)]"></div>

        <div class="relative z-20 p-6 sm:p-8">
            
            <div class="text-center mb-10 group">
                <div class="flex items-center justify-center gap-4 mb-6 transition-transform duration-500 group-hover:scale-105">
                    <img src="{{ asset('logo-prov-kalsel.png') }}" alt="Kalsel" class="h-12 w-auto drop-shadow-[0_0_15px_rgba(255,255,255,0.3)]">
                    <div class="h-8 w-[1px] bg-gradient-to-b from-transparent via-white/50 to-transparent"></div>
                    <img src="{{ asset('logo-sakip (1).png') }}" alt="SAKIP" class="h-12 w-auto drop-shadow-[0_0_15px_rgba(255,255,255,0.3)]">
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight mb-2">Selamat Datang</h1>
                <p class="text-blue-200/80 text-sm">Masuk untuk mengelola kinerja instansi</p>
            </div>

            <form wire:submit="login" class="space-y-6">
                
                <div class="space-y-1 group">
                    <label class="text-xs font-semibold text-blue-200/70 ml-4 uppercase tracking-wider">Username</label>
                    <div class="relative transition-all duration-300 transform group-focus-within:scale-[1.02]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-300 group-focus-within:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" 
                               wire:model="username" 
                               class="w-full pl-12 pr-4 py-3.5 bg-black/20 border border-white/10 rounded-2xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent focus:bg-black/40 transition-all shadow-inner" 
                               placeholder="Masukkan username">
                    </div>
                    @error('username') <span class="text-red-400 text-xs ml-4 animate-pulse">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1 group" x-data="{ show: false }">
                    <label class="text-xs font-semibold text-blue-200/70 ml-4 uppercase tracking-wider">Password</label>
                    <div class="relative transition-all duration-300 transform group-focus-within:scale-[1.02]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-300 group-focus-within:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        
                        <input :type="show ? 'text' : 'password'" 
                               wire:model="password" 
                               class="w-full pl-12 pr-12 py-3.5 bg-black/20 border border-white/10 rounded-2xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent focus:bg-black/40 transition-all shadow-inner" 
                               placeholder="Masukkan password">
                        
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-blue-300 hover:text-white transition-colors focus:outline-none">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="h-5 w-5" style="display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password') <span class="text-red-400 text-xs ml-4 animate-pulse">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1 group">
                    <label class="text-xs font-semibold text-blue-200/70 ml-4 uppercase tracking-wider">Periode Renstra</label>
                    <div class="relative transition-all duration-300 transform group-focus-within:scale-[1.02]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-300 group-focus-within:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <select wire:model="periode" class="w-full pl-12 pr-10 py-3.5 bg-black/20 border border-white/10 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent focus:bg-black/40 transition-all shadow-inner appearance-none cursor-pointer">
                            <option value="2025-2029" class="text-gray-900 bg-white">Periode 2025 - 2029</option>
                            <option value="2030-2034" class="text-gray-900 bg-white">Periode 2030 - 2034</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-blue-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 px-2">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" wire:model="remember" class="sr-only">
                            <div class="block bg-gray-600 w-10 h-6 rounded-full border border-white/20 transition group-hover:border-blue-400 {{ $remember ? 'bg-blue-600' : '' }}"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform {{ $remember ? 'translate-x-4' : '' }}"></div>
                        </div>
                        <span class="ml-3 text-sm text-blue-100/80 group-hover:text-white transition-colors">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="relative w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl shadow-lg shadow-blue-900/50 transform hover:-translate-y-1 active:scale-95 transition-all duration-200 overflow-hidden group border border-white/10">
                    
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <span wire:loading.remove>MASUK APLIKASI</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            MEMPROSES...
                        </span>
                        <svg wire:loading.remove class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </span>

                    <div class="absolute inset-0 -translate-x-full group-hover:animate-shimmer bg-gradient-to-r from-transparent via-white/20 to-transparent z-0"></div>
                </button>

            </form>

            <div class="mt-8 text-center">
                <p class="text-[10px] text-blue-200/40 uppercase tracking-widest">&copy; 2026 Dinas Kesehatan Prov. Kalsel</p>
            </div>
        </div>
    </div>
</div>