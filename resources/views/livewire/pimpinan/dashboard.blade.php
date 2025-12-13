<div class="space-y-8 animate-fade-in-up font-sans text-slate-600 relative">
    
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>

    {{-- Background Decorations --}}
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-72 h-72 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        <div class="absolute -bottom-20 left-1/2 w-80 h-80 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    </div>

    {{-- Section 1: Filter & Action Bar --}}
    <div class="glass-card p-4 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 flex flex-col lg:flex-row gap-4 items-center justify-between relative z-20">
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto flex-1">
            <div class="relative group w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <select wire:model.live="periode" class="pl-11 w-full text-sm font-semibold border-slate-200 bg-slate-50/50 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-700 py-3 transition-all hover:bg-white cursor-pointer hover:shadow-sm">
                    <option>RPJMD 2021-2026</option>
                    <option>RPJMD 2027-2032</option>
                </select>
            </div>
            <div class="relative group w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <select wire:model.live="perangkat_daerah" class="pl-11 w-full text-sm font-semibold border-slate-200 bg-slate-50/50 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-700 py-3 transition-all hover:bg-white cursor-pointer hover:shadow-sm">
                    <option value="">Semua Perangkat Daerah</option>
                    <option>Dinas Kesehatan</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3 w-full lg:w-auto">
            <button class="flex-1 lg:flex-none bg-slate-800 hover:bg-slate-900 text-white px-6 py-3 rounded-2xl text-sm font-bold transition-all shadow-lg shadow-slate-800/20 hover:-translate-y-0.5 active:scale-95">
                Terapkan Filter
            </button>
            <button class="flex-1 lg:flex-none bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 px-6 py-3 rounded-2xl text-sm font-bold transition-colors hover:text-indigo-600 shadow-sm">
                Reset
            </button>
        </div>
    </div>

    {{-- Section 2: Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="group relative bg-white p-6 rounded-[30px] shadow-[0_15px_35px_-5px_rgba(0,0,0,0.06)] border border-indigo-50 hover:border-indigo-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-gradient-to-br from-indigo-100 to-indigo-50 text-indigo-600 rounded-2xl shadow-sm group-hover:rotate-6 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">RPJMD</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['capaian_rpjmd'] }}</h3>
                    <span class="text-xl font-bold text-slate-400">%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-5 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full shadow-[0_0_15px_rgba(99,102,241,0.4)] relative" style="width: {{ $stats['capaian_rpjmd'] > 100 ? 100 : $stats['capaian_rpjmd'] }}%">
                         <div class="absolute right-0 top-0 bottom-0 w-2 bg-white/50 animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="group relative bg-white p-6 rounded-[30px] shadow-[0_15px_35px_-5px_rgba(0,0,0,0.06)] border border-emerald-50 hover:border-emerald-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
             <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-600 rounded-2xl shadow-sm group-hover:-rotate-6 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total PK</span>
                </div>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['renstra_sinkron'] }}</h3>
                <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-[11px] font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ $stats['renstra_badge'] }}
                </div>
            </div>
        </div>

        <div class="group relative bg-white p-6 rounded-[30px] shadow-[0_15px_35px_-5px_rgba(0,0,0,0.06)] border border-amber-50 hover:border-amber-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-gradient-to-br from-amber-100 to-amber-50 text-amber-600 rounded-2xl shadow-sm group-hover:rotate-6 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Estimasi</span>
                </div>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['serapan_anggaran'] }}</h3>
                <p class="text-xs text-slate-500 mt-4 font-semibold bg-white/60 inline-block px-3 py-1.5 rounded-lg backdrop-blur-sm border border-slate-100 shadow-sm">
                    Pagu: <span class="font-bold text-slate-700">Rp {{ $stats['pagu_anggaran'] }}</span>
                </p>
            </div>
        </div>

        <div class="group relative bg-white p-6 rounded-[30px] shadow-[0_15px_35px_-5px_rgba(0,0,0,0.06)] border border-rose-50 hover:border-rose-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl overflow-hidden">
             <div class="absolute -right-6 -top-6 w-32 h-32 bg-rose-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-rose-100 to-rose-50 text-rose-600 rounded-2xl shadow-sm group-hover:-rotate-6 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kritis</span>
                    </div>
                    @if($stats['isu_kritis'] > 0)
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                    @endif
                </div>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['isu_kritis'] }}</h3>
                <p class="text-xs text-rose-500 mt-4 font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    Indikator Perlu Atensi
                </p>
            </div>
        </div>
    </div>

    {{-- Section 3: Charts & Highlights --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-8 bg-white rounded-[32px] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] border border-slate-100 p-8 flex flex-col relative overflow-hidden">
            <div class="flex items-center justify-between mb-10 z-10">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                        Tren Capaian Kinerja
                        @if($is_dummy_chart)
                        <span class="text-[10px] font-bold bg-indigo-50 text-indigo-500 px-2 py-1 rounded-lg border border-indigo-100 uppercase tracking-wide">Data Contoh</span>
                        @endif
                    </h3>
                    <p class="text-sm text-slate-400 font-medium mt-1">Analisis performa bulanan tahun berjalan</p>
                </div>
                <div class="hidden sm:flex bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
                    <button class="px-5 py-2.5 text-xs font-bold bg-white rounded-xl shadow-sm text-indigo-600 border border-slate-100">Grafik</button>
                    <button class="px-5 py-2.5 text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">Tabel</button>
                </div>
            </div>
            
            <div class="relative h-80 w-full flex items-end justify-between gap-4 flex-1 z-10 px-2">
                {{-- Grid Lines --}}
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="absolute top-0 w-full border-t border-dashed border-indigo-200 z-0 opacity-60"><span class="text-[10px] font-bold text-indigo-400 absolute -top-4 right-0 bg-white px-1">Target 100%</span></div>
                    @for($i=0; $i<5; $i++)
                        <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    @endfor
                </div>

                @foreach($chart_data as $index => $val)
                <div class="w-full relative group h-full flex items-end z-10">
                    {{-- Bar --}}
                    <div style="height: {{ $val > 100 ? 100 : ($val < 5 ? 5 : $val) }}%" 
                         class="w-full bg-gradient-to-t from-indigo-500 via-purple-500 to-indigo-400 rounded-t-2xl relative transition-all duration-500 group-hover:from-indigo-600 group-hover:to-purple-600 group-hover:-translate-y-1 shadow-sm group-hover:shadow-[0_10px_20px_-5px_rgba(99,102,241,0.4)] cursor-pointer">
                        
                        {{-- Tooltip --}}
                        <div class="opacity-0 group-hover:opacity-100 absolute -top-12 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] font-bold py-1.5 px-3 rounded-xl shadow-xl transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 whitespace-nowrap pointer-events-none z-20">
                            {{ $val }}%
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-slate-800 rotate-45"></div>
                        </div>
                    </div>
                    
                    {{-- X-Axis Label --}}
                    <div class="absolute -bottom-8 inset-x-0 text-center">
                        <span class="text-[10px] font-bold text-slate-400 group-hover:text-indigo-600 transition-colors uppercase tracking-wider">{{ $chart_labels[$index] ?? '' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="h-8"></div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-[32px] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] border border-slate-100 p-8 flex flex-col">
            <h3 class="text-xl font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                <span class="flex items-center justify-center w-10 h-10 rounded-2xl bg-yellow-100 text-yellow-600 shadow-sm">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                </span>
                Highlight Kinerja
            </h3>
            
            <div class="flex-1 space-y-4 overflow-y-auto pr-2 hide-scrollbar">
                @foreach($highlights as $item)
                <div wire:click="openHighlightModal('{{ $item['label'] == 'Top Performer' ? 'performer' : ($item['label'] == 'Perlu Perhatian' ? 'isu' : 'dokumen') }}')" 
                     class="group relative p-5 rounded-3xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.08)] hover:border-indigo-100 transition-all duration-300 cursor-pointer hover:scale-[1.02]">
                    <div class="flex items-start gap-5">
                        <div class="flex-shrink-0 p-3.5 rounded-2xl bg-white shadow-sm border border-slate-100 group-hover:scale-110 transition-transform duration-300 {{ $item['color'] }}">
                            @if($item['icon'] == 'star') <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @elseif($item['icon'] == 'warning') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            @else <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $item['label'] }}</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed font-medium">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <button wire:click="openHighlightModal" class="w-full mt-8 py-4 border-2 border-dashed border-slate-200 rounded-2xl text-xs font-bold text-slate-400 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all flex items-center justify-center gap-2 group">
                <span>Lihat Detail Lengkap</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </button>
        </div>
    </div>

    {{-- Section 4: Activities & Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8 bg-white rounded-[32px] shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] border border-slate-100 p-8">
            <h3 class="text-xl font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                 <div class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 </div>
                 Log Aktivitas Terbaru
            </h3>
            
            <div class="space-y-0 relative before:absolute before:inset-y-6 before:left-[27px] before:w-[2px] before:bg-slate-100">
                @foreach($activities as $act)
                <div class="relative pl-14 py-4 group">
                    <div class="absolute left-5 top-7 w-4 h-4 rounded-full border-[3px] border-white bg-slate-300 group-hover:bg-indigo-500 group-hover:scale-125 transition-all shadow-sm z-10 ring-4 ring-slate-50 group-hover:ring-indigo-100"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl hover:bg-slate-50 border border-transparent hover:border-slate-100 hover:shadow-sm transition-all duration-300">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-700 transition-colors leading-relaxed">{!! $act['aktivitas'] !!}</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-white px-2 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $act['waktu'] }}</span>
                                <span class="text-xs text-slate-500 flex items-center gap-1.5 font-semibold">
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div> 
                                    by {{ $act['user'] }}
                                </span>
                            </div>
                        </div>
                        <span class="px-3.5 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider {{ str_replace('bg-', 'bg-opacity-10 bg-', $act['status_color']) }} border border-transparent self-start sm:self-center shadow-sm">
                            {{ $act['status'] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-4 flex flex-col h-full">
            <div class="bg-gradient-to-br from-indigo-600 via-purple-700 to-indigo-800 rounded-[32px] shadow-[0_20px_50px_-20px_rgba(79,70,229,0.5)] p-8 text-white relative overflow-hidden flex-1 flex flex-col justify-between group">
                
                {{-- Decorative Blobs --}}
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-48 h-48 bg-white opacity-10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-40 h-40 bg-fuchsia-500 opacity-20 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-8 flex items-center gap-3">
                        <span class="p-2 bg-white/10 rounded-xl backdrop-blur-sm border border-white/10">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </span>
                        Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('matrik.dokumen') }}" class="flex items-center gap-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 p-4 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-lg group/item cursor-pointer">
                            <div class="p-2.5 bg-white/10 rounded-xl group-hover/item:bg-white/20 transition-colors">
                                <svg class="w-5 h-5 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <span class="text-sm font-bold block">Buat RPJMD</span>
                                <span class="text-[10px] text-indigo-200 font-medium">Input Data Perencanaan</span>
                            </div>
                        </a>
                        <a href="{{ route('pohon.kinerja') }}" class="flex items-center gap-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 p-4 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-lg group/item cursor-pointer">
                            <div class="p-2.5 bg-white/10 rounded-xl group-hover/item:bg-white/20 transition-colors">
                                <svg class="w-5 h-5 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div>
                                <span class="text-sm font-bold block">Tambah Pohon</span>
                                <span class="text-[10px] text-indigo-200 font-medium">Cascading Kinerja</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- NOTIFIKASI DEADLINE DINAMIS (DIPERBAIKI) --}}
                @if(isset($deadline_alert) && $deadline_alert)
                <div class="mt-8 pt-6 border-t border-white/10 relative z-10 animate-fade-in-up">
                    <div class="flex items-start gap-3 bg-indigo-900/40 p-4 rounded-2xl border border-white/10 backdrop-blur-md hover:bg-indigo-900/60 transition-colors cursor-pointer group/alert">
                        <span class="relative flex h-3 w-3 mt-1 shrink-0">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </span>
                        <div>
                            <p class="text-xs text-indigo-100 leading-relaxed font-medium">
                                <span class="block text-white font-bold mb-0.5 text-sm">Deadline Mendekat!</span>
                                {!! $deadline_alert['message'] !!} 
                                <span class="underline decoration-rose-400 decoration-2 underline-offset-2 font-bold text-white group-hover/alert:text-rose-200 transition-colors">
                                    {{ $deadline_alert['days'] }} hari lagi
                                </span>.
                            </p>
                            <div class="text-[10px] text-indigo-300 mt-1 font-mono">Batas: {{ $deadline_alert['date_human'] }}</div>
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-8 pt-6 border-t border-white/10 relative z-10">
                    <div class="flex items-center gap-3 bg-white/5 p-4 rounded-2xl border border-white/5">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs text-indigo-100 font-medium">Tidak ada jadwal pengukuran aktif saat ini.</p>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>

    {{-- ================================================================= --}}
    {{-- MODAL HIGHLIGHT KINERJA --}}
    {{-- ================================================================= --}}
    @if($isOpenHighlight)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Backdrop --}}
            <div wire:click="closeHighlightModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div class="relative inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl w-full">
                
                {{-- Header Modal --}}
                <div class="bg-white px-8 py-6 border-b border-slate-100 flex justify-between items-center sticky top-0 z-10">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-800 tracking-tight" id="modal-title">Detail Kinerja Organisasi</h3>
                        <p class="text-sm text-slate-400 font-medium mt-1">Pantau performa indikator dan dokumen secara rinci</p>
                    </div>
                    <button wire:click="closeHighlightModal" class="bg-slate-50 p-2 rounded-full text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Tab Navigation --}}
                <div class="bg-slate-50/80 backdrop-blur-sm px-8 pt-2 border-b border-slate-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button wire:click="switchTab('performer')" class="{{ $activeTab === 'performer' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-[3px] font-bold text-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            Top Performer Ranking
                        </button>
                        <button wire:click="switchTab('isu')" class="{{ $activeTab === 'isu' ? 'border-rose-500 text-rose-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-[3px] font-bold text-sm transition-colors flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Isu Kritis (Underperform)
                        </button>
                        <button wire:click="switchTab('dokumen')" class="{{ $activeTab === 'dokumen' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} whitespace-nowrap py-4 px-1 border-b-[3px] font-bold text-sm transition-colors flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Status Dokumen PK
                        </button>
                    </nav>
                </div>

                {{-- Modal Content Body --}}
                <div class="p-8 min-h-[400px] bg-slate-50/30">
                    
                    {{-- TAB 1: PERFORMER --}}
                    @if($activeTab === 'performer')
                        <div class="animate-fade-in-up">
                            <h4 class="text-sm font-bold text-slate-500 mb-6 uppercase tracking-wider">Peringkat Capaian Kinerja Unit</h4>
                            @if(count($detailPerformers) > 0)
                                <div class="overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-sm">
                                    <table class="min-w-full divide-y divide-slate-100">
                                        <thead class="bg-slate-50/80">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Peringkat</th>
                                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Jabatan / Unit</th>
                                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah Indikator</th>
                                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Rata-rata Capaian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-slate-100">
                                            @foreach($detailPerformers as $index => $item)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $index == 0 ? 'text-yellow-600' : 'text-slate-500' }}">
                                                    #{{ $index + 1 }}
                                                    @if($index == 0) <span class="ml-2 inline-block animate-bounce">👑</span> @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">{{ $item['jabatan'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-slate-500 font-medium">{{ $item['jumlah_indikator'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold {{ $item['score'] >= 90 ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : ($item['score'] >= 75 ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-amber-100 text-amber-700 border border-amber-200') }}">
                                                        {{ $item['score'] }}%
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <span class="font-medium">Belum ada data kinerja yang diinput.</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- TAB 2: ISU KRITIS --}}
                    @if($activeTab === 'isu')
                        <div class="animate-fade-in-up">
                            <h4 class="text-sm font-bold text-rose-600 mb-6 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Indikator Perlu Perhatian (Capaian < 50%)
                            </h4>
                            @if(count($detailIsuKritis) > 0)
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($detailIsuKritis as $isu)
                                    <div class="bg-rose-50 border border-rose-100 p-5 rounded-2xl flex flex-col sm:flex-row justify-between gap-6 hover:shadow-md transition-shadow">
                                        <div class="flex-1">
                                            <div class="inline-block px-2 py-0.5 bg-white text-rose-500 text-[10px] font-bold rounded border border-rose-200 mb-2">{{ $isu['jabatan'] }}</div>
                                            <h5 class="text-sm font-bold text-slate-800 leading-snug">{{ $isu['indikator'] }}</h5>
                                            <div class="flex items-center gap-4 mt-3 text-xs font-medium text-slate-500">
                                                <span class="bg-white px-2 py-1 rounded border border-rose-100">Target: <strong class="text-slate-700">{{ $isu['target'] }}</strong></span>
                                                <span class="bg-white px-2 py-1 rounded border border-rose-100">Realisasi: <strong class="text-slate-700">{{ $isu['realisasi'] }}</strong></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end min-w-[120px]">
                                            <div class="text-right">
                                                <span class="block text-2xl font-black text-rose-600">{{ $isu['capaian'] }}%</span>
                                                <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Capaian</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-16 text-emerald-500 bg-emerald-50/50 rounded-2xl border-2 border-dashed border-emerald-100">
                                    <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="font-bold text-lg">Luar Biasa!</p>
                                    <p class="text-sm font-medium opacity-80">Tidak ada indikator kritis. Semua kinerja on-track.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- TAB 3: DOKUMEN --}}
                    @if($activeTab === 'dokumen')
                         <div class="animate-fade-in-up">
                            <h4 class="text-sm font-bold text-emerald-600 mb-6 uppercase tracking-wider">Status Dokumen Perjanjian Kinerja</h4>
                            @if(count($detailDokumen) > 0)
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                                    {{-- Wrapper Scroll Horizontal --}}
                                    <div class="overflow-x-auto"> 
                                        <table class="min-w-full divide-y divide-slate-100">
                                            <thead class="bg-slate-50/80">
                                                <tr>
                                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Unit / Jabatan</th>
                                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Pejabat</th>
                                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tahun</th>
                                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                                                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Update Terakhir</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-slate-100">
                                                @foreach($detailDokumen as $doc)
                                                <tr class="hover:bg-slate-50 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">{{ $doc['jabatan'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">{{ $doc['pegawai'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-500 font-bold">{{ $doc['tahun'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold {{ $doc['status'] == 'Final' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                                            {{ $doc['status'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-400 font-medium">{{ $doc['tanggal'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-16 text-slate-400">
                                    <svg class="w-16 h-16 mx-auto text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Belum ada dokumen Perjanjian Kinerja.
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="bg-white px-8 py-5 border-t border-slate-100 flex justify-end">
                    <button wire:click="closeHighlightModal" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-slate-800/20 transition-all active:scale-95 text-sm">
                        Tutup Panel
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>