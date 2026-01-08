<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#030712] font-sans selection:bg-indigo-500 selection:text-white">
    
    {{-- CUSTOM CSS & ANIMATIONS --}}
    <style>
        /* Floating Animation Background */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 10s infinite alternate;
        }
        .delay-2000 { animation-delay: 2s; }
        .delay-4000 { animation-delay: 4s; }

        /* Noise Texture */
        .bg-noise {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.05'/%3E%3C/svg%3E");
        }

        /* Glassmorphism Card */
        .glass-premium {
            background: rgba(17, 24, 39, 0.6); 
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 40px -10px rgba(0, 0, 0, 0.6);
        }

        /* Animasi Masuk */
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-enter { animation: fade-in-up 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; }

        /* Animasi Gradient Berputar untuk Logo */
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
    </style>

    {{-- BACKGROUND LAYERS --}}
    <div class="absolute inset-0 z-0">
        {{-- Base Gradient --}}
        <div class="absolute inset-0 bg-gradient-to-b from-[#0f172a] to-[#020617]"></div>
        
        {{-- Animated Blobs (Orbs) --}}
        <div class="absolute top-0 -left-4 w-96 h-96 bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[100px] animate-blob"></div>
        <div class="absolute top-0 -right-4 w-96 h-96 bg-blue-500/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob delay-2000"></div>
        <div class="absolute -bottom-32 left-20 w-96 h-96 bg-teal-500/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob delay-4000"></div>

        {{-- Noise Texture --}}
        <div class="absolute inset-0 bg-noise opacity-30 mix-blend-soft-light pointer-events-none"></div>
    </div>

    {{-- MAIN LOGIN CARD --}}
    <div class="relative z-10 w-full max-w-[420px] p-6 animate-enter">
        <div class="glass-premium rounded-[2.5rem] overflow-hidden relative group border-t border-white/10">
            
            {{-- Decorative Light Beams --}}
            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-indigo-400 to-transparent opacity-50"></div>
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-indigo-400 to-transparent opacity-10"></div>

            <div class="p-8 sm:p-10 relative z-20">
                
                {{-- HEADER: LOGO & TITLE --}}
                <div class="text-center mb-10">
                    
                    {{-- WADAH LOGO DENGAN ANIMASI --}}
                    <div class="relative inline-block mb-6 group/logo">
                        {{-- 1. Animasi Gradient Berputar di Belakang Logo --}}
                        <div class="absolute -inset-1 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500 opacity-75 blur-md animate-spin-slow group-hover/logo:opacity-100 transition duration-1000"></div>
                        
                        {{-- 2. Wadah Putih Transparan (Capsule) --}}
                        <div class="relative bg-slate-900/80 backdrop-blur-xl rounded-full p-2 border border-white/10 shadow-2xl flex items-center justify-center gap-5 px-8 py-4">
                            {{-- Logo Pemprov --}}
                            <img src="{{ asset('logo pemprov.png') }}" class="h-20 w-auto drop-shadow-md filter hover:brightness-110 transition-all duration-300" alt="Pemprov Kalsel">
                            
                            {{-- Separator --}}
                            <div class="h-12 w-px bg-white/20 rounded-full"></div>
                            
                            {{-- Logo GERMAS --}}
                            <img src="{{ asset('Logo GERMAS (Gerakan Masyarakat Hidup Sehat).png') }}" class="h-16 w-auto drop-shadow-md filter hover:brightness-110 transition-all duration-300" alt="Germas">
                        </div>
                    </div>

                    <h1 class="text-3xl font-black text-white tracking-tight">
                        E-SAKIP <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">DINKES</span>
                    </h1>
                    <p class="text-slate-400 text-[11px] font-medium tracking-[0.2em] uppercase mt-2">Sistem Akuntabilitas Kinerja</p>
                </div>

                {{-- ALERT ERROR --}}
                @if (session()->has('error'))
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-xs font-medium flex items-center gap-3 animate-enter" style="animation-delay: 0.1s">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{-- LOGIN FORM --}}
                <form wire:submit="login" class="space-y-6 animate-enter" style="animation-delay: 0.2s">

                    {{-- [BARU] Input Role Selector (Dropdown) --}}
                    <div class="group relative">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-indigo-400 transition-colors">
                            Masuk Sebagai
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <select wire:model="role" class="w-full pl-11 pr-10 py-3.5 bg-slate-900/60 border border-slate-700/50 rounded-2xl text-slate-200 appearance-none focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:bg-slate-900/90 transition-all duration-300 text-sm font-medium cursor-pointer">
                                <option value="pegawai" class="bg-slate-900">Pegawai</option>
                                <option value="pimpinan" class="bg-slate-900">Pimpinan</option>
                                <option value="admin" class="bg-slate-900">Administrator</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('role') <span class="text-red-400 text-xs mt-1 ml-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Input 1: Username / NIP --}}
                    <div class="group relative">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-indigo-400 transition-colors">
                            Username / NIP
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <input type="text" wire:model="login_id" 
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-900/60 border border-slate-700/50 rounded-2xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:bg-slate-900/90 transition-all duration-300 text-sm font-medium" 
                                placeholder="Masukkan Username atau NIP" required autofocus>
                        </div>
                        @error('login_id') <span class="text-red-400 text-xs mt-1 ml-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Input 2: Password --}}
                    <div class="group relative" x-data="{ show: false }">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-indigo-400 transition-colors">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" wire:model="password" 
                                class="w-full pl-11 pr-11 py-3.5 bg-slate-900/60 border border-slate-700/50 rounded-2xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:bg-slate-900/90 transition-all duration-300 text-sm font-medium" 
                                placeholder="••••••••" required>
                            
                            {{-- Toggle Show Password --}}
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-300 transition-colors focus:outline-none">
                                <template x-if="!show">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.575-3.107m5.858.858a3 3 0 114.243 4.243m-9.965-9.966l16.1 16.1"></path></svg>
                                </template>
                                <template x-if="show">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </template>
                            </button>
                        </div>
                        @error('password') <span class="text-red-400 text-xs mt-1 ml-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Periode Selector --}}
                    <div class="group relative">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-indigo-400 transition-colors">Tahun Perencanaan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <select wire:model="periode" class="w-full pl-11 pr-10 py-3.5 bg-slate-900/60 border border-slate-700/50 rounded-2xl text-slate-300 appearance-none focus:outline-none focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 focus:bg-slate-900/90 transition-all duration-300 text-sm font-medium cursor-pointer">
                                <option value="2025-2029" class="bg-slate-900">Renstra 2025 - 2029</option>
                                <option value="2030-2034" class="bg-slate-900">Renstra 2030 - 2034</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Action Area --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="remember" class="peer sr-only">
                                    <div class="w-5 h-5 border-2 border-slate-600 rounded-lg bg-transparent peer-checked:bg-indigo-500 peer-checked:border-indigo-500 transition-all duration-200"></div>
                                    <svg class="w-3 h-3 text-white absolute top-1 left-1 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="ml-2.5 text-xs text-slate-400 group-hover:text-slate-300 transition-colors select-none">Ingat Saya</span>
                            </label>
                        </div>

                        <button type="submit" class="group relative w-full py-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-[0.98] overflow-hidden">
                            {{-- Shine Effect --}}
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out skew-y-6"></div>
                            
                            <span wire:loading.remove class="relative flex items-center justify-center gap-2">
                                MASUK APLIKASI
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                            
                            <span wire:loading class="relative flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Authenticating...
                            </span>
                        </button>
                    </div>
                </form>

                {{-- FOOTER --}}
                <div class="mt-8 pt-6 border-t border-slate-700/30 text-center animate-enter" style="animation-delay: 0.3s">
                    <p class="text-[10px] text-slate-500 font-bold tracking-[0.2em] uppercase">
                        &copy; {{ date('Y') }} Dinas Kesehatan Prov. Kalsel
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>