<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-900">
    
    {{-- Custom Styles untuk Background Animasi --}}
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 10s infinite ease-in-out alternate; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        
        /* Grid Pattern Halus */
        .bg-grid-slate {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        }
    </style>

    {{-- BACKGROUND LAYER --}}
    <div class="absolute inset-0 z-0">
        {{-- 1. Base Gradient (Biru Tua Profesional ke Slate) --}}
        <div class="absolute inset-0 bg-gradient-to-br from-[#0f172a] via-[#1e293b] to-[#0f172a]"></div>

        {{-- 2. Grid Pattern --}}
        <div class="absolute inset-0 bg-grid-slate opacity-[0.2]"></div>

        {{-- 3. Animated Glowing Blobs (Aksen Biru & Tosca Medis) --}}
        <div class="absolute top-0 -left-4 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-96 h-96 bg-teal-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-20 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    {{-- MAIN CARD --}}
    <div class="relative z-10 w-full max-w-[450px] px-4">
        
        {{-- Efek Glassmorphism Card --}}
        <div class="relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl shadow-2xl overflow-hidden">
            
            {{-- Hiasan Garis Atas --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-teal-400 to-blue-500"></div>

            <div class="p-8 md:p-10">
                
                {{-- HEADER: Logo & Judul --}}
                <div class="text-center mb-8">
                    {{-- AREA LOGO DIPERBESAR --}}
                    <div class="flex items-center justify-center gap-6 mb-8">
                        {{-- Logo Pemprov --}}
                        <div class="relative group">
                            {{-- Efek Glow di belakang logo --}}
                            <div class="absolute -inset-2 bg-gradient-to-r from-blue-600 to-teal-600 rounded-full blur-md opacity-20 group-hover:opacity-40 transition duration-300"></div>
                            {{-- Ukuran diperbesar ke h-20 (sekitar 80px) --}}
                            <img src="{{ asset('logo pemprov.png') }}" class="relative h-24 w-auto object-contain drop-shadow-lg transition-transform hover:scale-105 duration-300" alt="Pemprov Kalsel">
                        </div>
                        
                        {{-- Divider Vertikal (Tinggi disesuaikan) --}}
                        <div class="h-16 w-[2px] bg-gradient-to-b from-transparent via-white/20 to-transparent rounded-full"></div>
                        
                        {{-- Logo Germas --}}
                        <div class="relative group">
                             {{-- Efek Glow di belakang logo --}}
                             <div class="absolute -inset-2 bg-gradient-to-r from-teal-600 to-green-600 rounded-full blur-md opacity-20 group-hover:opacity-40 transition duration-300"></div>
                            {{-- Ukuran diperbesar ke h-20 (sekitar 80px) --}}
                            <img src="{{ asset('Logo GERMAS (Gerakan Masyarakat Hidup Sehat).png') }}" class="relative h-20 w-auto object-contain drop-shadow-lg transition-transform hover:scale-105 duration-300" alt="GERMAS">
                        </div>
                    </div>
                    
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-2">E-SAKIP Dinkes</h2>
                    <p class="text-blue-200/80 text-sm font-light">Sistem Akuntabilitas Kinerja Instansi Pemerintah</p>
                </div>

                {{-- FORM INPUT --}}
                <form wire:submit="login" class="space-y-6">
                    
                    {{-- Input Username --}}
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-blue-300 uppercase tracking-widest ml-1">NIP / Username</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-300/60 group-focus-within:text-blue-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" 
                                   wire:model="username" 
                                   class="w-full pl-12 pr-4 py-4 bg-slate-900/60 border border-white/10 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-200 text-base" 
                                   placeholder="Masukkan NIP Anda">
                        </div>
                        @error('username') <span class="text-red-400 text-xs ml-1 font-medium">{{ $message }}</span> @enderror
                    </div>

                    {{-- Input Password --}}
                    <div class="space-y-2" x-data="{ show: false }">
                        <label class="text-xs font-bold text-blue-300 uppercase tracking-widest ml-1">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-300/60 group-focus-within:text-blue-400 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" 
                                   wire:model="password" 
                                   class="w-full pl-12 pr-12 py-4 bg-slate-900/60 border border-white/10 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-200 text-base" 
                                   placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-slate-400 hover:text-white transition-colors">
                                <svg x-show="!show" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" style="display: none;" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('password') <span class="text-red-400 text-xs ml-1 font-medium">{{ $message }}</span> @enderror
                    </div>

                    {{-- Periode Selector --}}
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-blue-300 uppercase tracking-widest ml-1">Periode Renstra</label>
                        <div class="relative">
                            <select wire:model="periode" class="w-full pl-5 pr-10 py-4 bg-slate-900/60 border border-white/10 rounded-xl text-white appearance-none focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all cursor-pointer text-base">
                                <option value="2025-2029" class="bg-slate-900">Periode 2025 - 2029</option>
                                <option value="2030-2034" class="bg-slate-900">Periode 2030 - 2034</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Remember Me & Action --}}
                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center cursor-pointer select-none group">
                            <div class="relative">
                                <input type="checkbox" wire:model="remember" class="peer sr-only">
                                <div class="w-10 h-6 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </div>
                            <span class="ml-3 text-sm text-slate-300 group-hover:text-white transition-colors">Ingat sesi saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-teal-500 hover:from-blue-500 hover:to-teal-400 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 active:scale-95 transition-all duration-200 flex items-center justify-center gap-3 text-lg">
                        <span wire:loading.remove>MASUK APLIKASI</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            MEMPROSES...
                        </span>
                    </button>

                </form>

                {{-- Footer --}}
                <div class="mt-10 pt-6 border-t border-white/5 text-center">
                    <p class="text-[11px] text-slate-500 font-medium tracking-widest uppercase">
                        &copy; {{ date('Y') }} Dinas Kesehatan Provinsi Kalsel
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>