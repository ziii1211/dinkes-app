<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#0a0f1d] font-sans">
    
    {{-- Style Background (Tetap Dipertahankan karena bagus) --}}
    <style>
        @keyframes subtle-float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .animate-subtle { animation: subtle-float 15s infinite ease-in-out; }
        .mesh-gradient {
            background-color: #0a0f1d;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 58, 138, 0.3) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(20, 184, 166, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.5) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(37, 99, 235, 0.2) 0px, transparent 50%);
        }
        .bg-dots {
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .logo-shadow { filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.2)); }
    </style>

    <div class="absolute inset-0 z-0 mesh-gradient">
        <div class="absolute inset-0 bg-dots"></div>
        <div class="absolute top-[10%] left-[15%] w-72 h-72 bg-blue-600/10 rounded-full blur-[100px] animate-subtle"></div>
        <div class="absolute bottom-[10%] right-[15%] w-80 h-80 bg-teal-500/10 rounded-full blur-[100px] animate-subtle" style="animation-delay: -5s"></div>
    </div>

    <div class="relative z-10 w-full max-w-[440px] px-6 py-6">
        <div class="bg-white/[0.03] backdrop-blur-xl border border-white/10 rounded-[2rem] shadow-2xl overflow-hidden">
            <div class="h-1.5 w-full bg-gradient-to-r from-blue-600 via-teal-400 to-blue-600"></div>

            <div class="p-8 md:p-9">
                <div class="text-center mb-6">
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <img src="{{ asset('logo pemprov.png') }}" class="h-16 w-auto logo-shadow" alt="Pemprov Kalsel">
                        <div class="h-10 w-px bg-white/10"></div>
                        <img src="{{ asset('Logo GERMAS (Gerakan Masyarakat Hidup Sehat).png') }}" class="h-12 w-auto logo-shadow brightness-110" alt="GERMAS">
                    </div>
                    <h2 class="text-2xl font-extrabold text-white tracking-tight mb-1">E-SAKIP <span class="text-blue-400">Dinkes</span></h2>
                    <p class="text-slate-400 text-xs font-medium leading-relaxed">Sistem Akuntabilitas Kinerja Instansi Pemerintah<br>Provinsi Kalimantan Selatan</p>
                </div>

                @if (session()->has('error'))
                    <div class="mb-4 bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-red-400 text-xs text-center font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit="login" class="space-y-4">
                    
                    {{-- INPUT USERNAME (KEMBALI KE TEXT INPUT) --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-blue-400/80 uppercase tracking-[0.15em] ml-1">Username / NIP</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </div>
                            
                            {{-- INI YANG DIUBAH: DARI SELECT KEMBALI KE INPUT TEXT --}}
                            <input type="text" 
                                   wire:model="username" 
                                   class="w-full pl-10 pr-4 py-3 bg-white/[0.03] border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 text-sm" 
                                   placeholder="Masukkan Username atau NIP" required autofocus>
                        </div>
                        @error('username') <span class="text-red-400 text-[10px] mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    {{-- INPUT PASSWORD --}}
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <label class="text-[10px] font-bold text-blue-400/80 uppercase tracking-[0.15em] ml-1">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" 
                                   wire:model="password" 
                                   class="w-full pl-10 pr-10 py-3 bg-white/[0.03] border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 text-sm" 
                                   placeholder="Masukkan Password" required>
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-white transition-colors">
                                <template x-if="!show"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></template>
                                <template x-if="show"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24L1 1l22 22"/></svg></template>
                            </button>
                        </div>
                        @error('password') <span class="text-red-400 text-[10px] mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    {{-- PERIODE RENSTRA --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-blue-400/80 uppercase tracking-[0.15em] ml-1">Periode Renstra</label>
                        <div class="relative">
                            <select wire:model="periode" class="w-full px-4 py-3 bg-white/[0.03] border border-white/10 rounded-xl text-white appearance-none focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer text-sm">
                                <option value="2025-2029" class="bg-[#1e293b]">Periode 2025 - 2029</option>
                                <option value="2030-2034" class="bg-[#1e293b]">Periode 2030 - 2034</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center pt-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model="remember" class="w-3.5 h-3.5 rounded border-white/10 bg-white/5 text-blue-600 focus:ring-offset-0 focus:ring-blue-600/50">
                            <span class="ml-2.5 text-xs text-slate-400 group-hover:text-slate-200 transition-colors">Ingat sesi saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full mt-2 py-3.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-bold rounded-xl shadow-[0_0_15px_rgba(37,99,235,0.2)] transition-all duration-300 flex items-center justify-center gap-3 active:scale-[0.98] text-sm tracking-wide">
                        <span wire:loading.remove>MASUK APLIKASI</span>
                        <span wire:loading class="flex items-center gap-2">MEMPROSES...</span>
                    </button>
                </form>

                <div class="mt-8 text-center border-t border-white/5 pt-4">
                    <p class="text-[9px] text-slate-500 font-bold tracking-[0.2em] uppercase">&copy; {{ date('Y') }} Dinas Kesehatan Provinsi Kalsel</p>
                </div>
            </div>
        </div>
    </div>
</div>