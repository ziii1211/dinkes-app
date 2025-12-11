<div class="min-h-screen bg-slate-50/50 p-6 space-y-8 font-sans text-slate-600">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold tracking-wide text-indigo-500 uppercase mb-1">Overview</p>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Pimpinan</h1>
            <p class="text-sm text-slate-500 mt-1">Monitoring Kinerja Utama Organisasi.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="hidden md:flex items-center text-xs font-medium text-slate-500 bg-white px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                System Online
            </div>
            <button class="bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-xl text-sm font-semibold border border-slate-200 shadow-sm transition-all hover:shadow-md flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Laporan
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-100 flex flex-col lg:flex-row gap-2">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-slate-400 text-xs font-semibold">Periode:</span>
                </div>
                <select wire:model.live="periode" class="pl-16 w-full text-sm border-0 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-slate-700 font-medium py-2.5 transition-colors group-hover:bg-slate-100">
                    <option>RPJMD 2021-2026</option>
                    <option>RPJMD 2027-2032</option>
                </select>
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-slate-400 text-xs font-semibold">SKPD:</span>
                </div>
                <select wire:model.live="perangkat_daerah" class="pl-14 w-full text-sm border-0 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-slate-700 font-medium py-2.5 transition-colors group-hover:bg-slate-100">
                    <option value="">Semua Perangkat Daerah</option>
                    <option>Dinas Kesehatan</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Card 1 --}}
        <div class="group bg-white p-5 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-indigo-50 group-hover:bg-indigo-100 p-2.5 rounded-xl text-indigo-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <p class="text-slate-500 text-sm font-medium">Capaian RPJMD</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['capaian_rpjmd'] }}%</h3>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-4 overflow-hidden">
                <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(99,102,241,0.5)]" style="width: {{ $stats['capaian_rpjmd'] > 100 ? 100 : $stats['capaian_rpjmd'] }}%"></div>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="group bg-white p-5 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-emerald-50 group-hover:bg-emerald-100 p-2.5 rounded-xl text-emerald-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-slate-500 text-sm font-medium">Dokumen PK</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['renstra_sinkron'] }}</h3>
            <p class="text-xs text-emerald-600 font-medium mt-2 bg-emerald-50 inline-block px-2 py-0.5 rounded-md border border-emerald-100">
                {{ $stats['renstra_badge'] }}
            </p>
        </div>

        {{-- Card 3 --}}
        <div class="group bg-white p-5 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-amber-50 group-hover:bg-amber-100 p-2.5 rounded-xl text-amber-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-slate-500 text-sm font-medium">Estimasi Serapan</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['serapan_anggaran'] }}</h3>
            <p class="text-xs text-slate-400 mt-1 font-medium">dari total pagu Rp {{ $stats['pagu_anggaran'] }}</p>
        </div>

        {{-- Card 4 --}}
        <div class="group bg-white p-5 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 hover:shadow-lg hover:shadow-rose-500/10 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-rose-50 group-hover:bg-rose-100 p-2.5 rounded-xl text-rose-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                @if($stats['isu_kritis'] > 0)
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                @endif
            </div>
            <p class="text-slate-500 text-sm font-medium">Indikator Underperform</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['isu_kritis'] }}</h3>
            <p class="text-xs text-rose-600 font-medium mt-2">Perlu tindak lanjut segera</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Chart Section --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-slate-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Analisis Tren Indikator</h3>
                    <p class="text-xs text-slate-400">Perbandingan performa antar triwulan</p>
                </div>
                <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-200">
                    <button class="px-3 py-1.5 text-xs font-semibold bg-white rounded-lg shadow-sm text-slate-700">Grafik</button>
                    <button class="px-3 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-700">Tabel</button>
                </div>
            </div>
            
            <div class="relative h-72 w-full flex items-end justify-between gap-2 px-2">
                {{-- Background Grid --}}
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                </div>

                @foreach($chart_data as $index => $val)
                <div class="w-full bg-slate-50 rounded-t-2xl relative group h-full flex items-end overflow-hidden">
                    <div style="height: {{ $val > 100 ? 100 : $val }}%" class="w-full bg-gradient-to-t from-indigo-600 to-violet-400 rounded-t-lg relative transition-all duration-500 hover:opacity-90">
                        {{-- Tooltip on Hover --}}
                        <div class="opacity-0 group-hover:opacity-100 absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] py-1 px-2 rounded shadow-xl transition-opacity whitespace-nowrap z-10 pointer-events-none">
                            Capaian: {{ $val }}%
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-slate-800 rotate-45"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-4 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                 @foreach($chart_labels as $label)
                 <span class="w-full text-center">{{ $label }}</span>
                 @endforeach
            </div>
        </div>

        {{-- Highlights Section (THEME GELAP/GRADIENT SEPERTI AKSI CEPAT) --}}
        <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl shadow-xl shadow-indigo-500/20 p-8 text-white relative overflow-hidden">
            
            {{-- Decorative Background --}}
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-indigo-400 opacity-20 rounded-full blur-xl"></div>

            <h3 class="text-lg font-bold text-white mb-6 relative z-10">Highlight Kinerja</h3>
            
            <div class="space-y-4 relative z-10">
                @foreach($highlights as $item)
                <div class="group p-4 rounded-2xl bg-white/10 border border-white/10 hover:bg-white/20 transition-all cursor-default backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        {{-- Icon Box (Putih Transparan agar kontras dengan Background Gelap) --}}
                        <div class="p-2.5 rounded-xl bg-white text-indigo-600 shadow-sm">
                            @if($item['icon'] == 'star')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @elseif($item['icon'] == 'warning')
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            @else
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white group-hover:text-indigo-100 transition-colors">{{ $item['label'] }}</h4>
                            <p class="text-xs text-indigo-100 mt-1 leading-relaxed opacity-90">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <button class="w-full mt-6 py-3 border border-dashed border-indigo-300/30 rounded-xl text-xs font-semibold text-indigo-100 hover:text-white hover:border-white/50 hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                Lihat Detail Laporan
            </button>
        </div>
    </div>

    {{-- Bottom Section (Log Aktivitas & Aksi Cepat) DIHAPUS SESUAI PERMINTAAN --}}

</div>