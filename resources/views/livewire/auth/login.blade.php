<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#0B1120] font-sans selection:bg-blue-500 selection:text-white">
    
    <style>
        /* Animasi Pergerakan Lambat (Drift) */
        @keyframes drift {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-drift { animation: drift 12s infinite ease-in-out alternate; }
        .animation-delay-2000 { animation-delay: -4s; }
        .animation-delay-4000 { animation-delay: -8s; }

        /* Modern Grid Pattern (CSS Only - No Image) */
        .bg-grid-white {
            background-size: 50px 50px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            mask-image: linear-gradient(to bottom, transparent, black 20%, black 80%, transparent);
            -webkit-mask-image: linear-gradient(to bottom, transparent, black 20%, black 80%, transparent);
        }
    </style>

    {{-- BACKGROUND LAYER: Desain Grafis Modern (Tanpa Gambar) --}}
    <div class="absolute inset-0 z-0">
        {{-- 1. Base Color Deep Dark Blue --}}
        <div class="absolute inset-0 bg-[#0B1120]"></div>

        {{-- 2. Animated Glowing Orbs (Bola Cahaya) --}}
        <div class="absolute top-[-10%] left-[-10%] w-[600px] h-[600px] bg-blue-700/20 rounded-full blur-[100px] animate-drift mix-blend-screen"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-indigo-600/20 rounded-full blur-[100px] animate-drift animation-delay-2000 mix-blend-screen"></div>
        <div class="absolute top-[40%] left-[40%] w-[400px] h-[400px] bg-cyan-600/10 rounded-full blur-[80px] animate-drift animation-delay-4000 mix-blend-screen"></div>

        {{-- 3. Technical Grid Pattern --}}
        <div class="absolute inset-0 bg-grid-white opacity-40"></div>

        {{-- 4. Vignette & Noise Texture Overlay (Optional for detail) --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#0B1120] via-transparent to-[#0B1120] opacity-80"></div>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <div class="relative z-10 w-full max-w-[440px] px-6 transition-all duration-500 transform hover:scale-[1.01]">
        
        {{-- Glass Container --}}
        <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-[0_0_40px_-10px_rgba(0,0,0,0.5)] overflow-hidden ring-1 ring-white/5">
            
            {{-- Top Accent Line --}}
            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-50"></div>
            
            <div class="p-8">
                
                {{-- Header Section --}}
                <div class="text-center mb-8">
                    {{-- Logo Group --}}
                    {{-- UPDATED: Logo Kalsel & GERMAS Berdampingan --}}
                    <div class="flex items-center justify-center gap-6 mb-6">
                        {{-- 1. Logo Provinsi Kalsel --}}
                        <img src="{{ asset('logo-prov-kalsel.png') }}" 
                             class="h-20 w-auto drop-shadow-[0_0_15px_rgba(255,255,255,0.15)] transition-transform hover:scale-110 duration-300" 
                             alt="Pemprov Kalsel">
                        
                        {{-- Divider Vertikal Halus --}}
                        <div class="h-12 w-[1px] bg-gradient-to-b from-transparent via-white/30 to-transparent"></div>
                        
                        {{-- 2. Logo GERMAS --}}
                        <img src="{{ asset('Logo GERMAS (Gerakan Masyarakat Hidup Sehat).png') }}" 
                             class="h-20 w-auto drop-shadow-[0_0_15px_rgba(255,255,255,0.15)] transition-transform hover:scale-110 duration-300" 
                             alt="Logo GERMAS">
                    </div>
                    
                    <h2 class="text-2xl font-bold text-white tracking-tight">Selamat Datang</h2>
                    <p class="text-slate-400 text-sm mt-1">Sistem Akuntabilitas Kinerja Instansi</p>
                </div>

                {{-- Login Form --}}
                <form wire:submit="login" class="space-y-5">
                    
                    {{-- Username Input --}}
                    <div class="space-y-1.5 group">
                        <label class="text-[11px] font-bold text-blue-300/80 uppercase tracking-widest ml-1">Username</label>
                        <div class="relative group-focus-within:shadow-[0_0_20px_-5px_rgba(59,130,246,0.3)] transition-shadow rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" 
                                   wire:model="username" 
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-900/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 focus:bg-slate-900/80 transition-all duration-200" 
                                   placeholder="Masukkan NIP / Username">
                        </div>
                        @error('username') <span class="text-red-400 text-xs ml-1 flex items-center gap-1 animate-pulse">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password Input --}}
                    <div class="space-y-1.5 group" x-data="{ show: false }">
                        <label class="text-[11px] font-bold text-blue-300/80 uppercase tracking-widest ml-1">Password</label>
                        <div class="relative group-focus-within:shadow-[0_0_20px_-5px_rgba(59,130,246,0.3)] transition-shadow rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" 
                                   wire:model="password" 
                                   class="w-full pl-11 pr-12 py-3.5 bg-slate-900/50 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 focus:bg-slate-900/80 transition-all duration-200" 
                                   placeholder="Masukkan Password">
                            
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-slate-500 hover:text-white transition-colors">
                                <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('password') <span class="text-red-400 text-xs ml-1 flex items-center gap-1 animate-pulse">{{ $message }}</span> @enderror
                    </div>

                    {{-- Periode Selector --}}
                    <div class="space-y-1.5 group">
                        <label class="text-[11px] font-bold text-blue-300/80 uppercase tracking-widest ml-1">Periode Renstra</label>
                        <div class="relative group-focus-within:shadow-[0_0_20px_-5px_rgba(59,130,246,0.3)] transition-shadow rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <select wire:model="periode" class="w-full pl-11 pr-10 py-3.5 bg-slate-900/50 border border-white/10 rounded-xl text-white appearance-none focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 focus:bg-slate-900/80 transition-all cursor-pointer">
                                <option value="2025-2029" class="bg-slate-900 text-slate-200">Periode 2025 - 2029</option>
                                <option value="2030-2034" class="bg-slate-900 text-slate-200">Periode 2030 - 2034</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center pt-2">
                        <label class="flex items-center cursor-pointer group select-none">
                            <div class="relative">
                                <input type="checkbox" wire:model="remember" class="peer sr-only">
                                <div class="w-9 h-5 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                            </div>
                            <span class="ml-3 text-sm text-slate-400 group-hover:text-blue-300 transition-colors">Ingat sesi saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" 
                            class="relative w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-[0_5px_20px_-5px_rgba(37,99,235,0.4)] hover:shadow-[0_8px_25px_-5px_rgba(37,99,235,0.5)] transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95 group overflow-hidden border border-white/10">
                        
                        {{-- Button Shine Effect --}}
                        <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12"></div>
                        
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <span wire:loading.remove>MASUK APLIKASI</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                MEMPROSES...
                            </span>
                        </span>
                    </button>

                </form>

                {{-- Footer --}}
                <div class="mt-8 pt-6 border-t border-white/5 text-center">
                    <p class="text-[10px] font-medium text-slate-500/80 uppercase tracking-widest">
                        &copy; 2026 Dinas Kesehatan Provinsi Kalimantan Selatan
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>