<div class="min-h-screen bg-slate-50/80 p-6 space-y-8 font-sans text-slate-600 animate-fade-in-up">
    
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="relative w-full rounded-[35px] overflow-hidden shadow-[0_20px_50px_-12px_rgba(79,70,229,0.15)] group">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-800"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] mix-blend-overlay"></div>
        <div class="absolute -right-10 -top-20 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-1000"></div>
        
        <div class="relative p-8 md:p-12 flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
            <div class="text-white space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-xs font-bold tracking-widest uppercase">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Dashboard
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight">Dinas Kesehatan</h1>
                <p class="text-indigo-100 font-medium text-lg max-w-xl leading-relaxed opacity-90">
                    Selamat datang kembali. Pantau kinerja SKPD, realisasi anggaran, dan capaian indikator strategis Anda hari ini.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/20 px-5 py-3 rounded-2xl text-sm font-bold transition-all hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </button>
                <button class="bg-white text-indigo-700 hover:bg-indigo-50 px-6 py-3 rounded-2xl text-sm font-bold shadow-xl shadow-indigo-900/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Input Data
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white p-2 rounded-[24px] shadow-sm border border-slate-100 flex flex-col lg:flex-row gap-2 items-center">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2 w-full">
            <div class="relative group w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <select wire:model.live="periode" class="pl-12 w-full text-sm font-semibold border-0 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-slate-700 py-3 transition-colors group-hover:bg-slate-100 cursor-pointer">
                    <option>RPJMD 2021-2026</option>
                    <option>RPJMD 2027-2032</option>
                </select>
            </div>
            <div class="relative group w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <select wire:model.live="perangkat_daerah" class="pl-12 w-full text-sm font-semibold border-0 bg-slate-50 rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-slate-700 py-3 transition-colors group-hover:bg-slate-100 cursor-pointer">
                    <option value="">Semua Perangkat Daerah</option>
                    <option>Dinas Kesehatan</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2 w-full lg:w-auto">
            <button class="flex-1 lg:flex-none bg-slate-800 hover:bg-slate-900 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-lg shadow-slate-200">
                Terapkan
            </button>
            <button class="flex-1 lg:flex-none bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 px-5 py-3 rounded-xl text-sm font-bold transition-colors">
                Reset
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="relative bg-white p-6 rounded-[28px] shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full transition-transform group-hover:scale-125"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-indigo-100 text-indigo-600 rounded-2xl shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Capaian RPJMD</span>
                </div>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['capaian_rpjmd'] }}<span class="text-2xl text-slate-400">%</span></h3>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.5)] transition-all duration-1000" style="width: {{ $stats['capaian_rpjmd'] > 100 ? 100 : $stats['capaian_rpjmd'] }}%"></div>
                </div>
            </div>
        </div>

        <div class="relative bg-white p-6 rounded-[28px] shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full transition-transform group-hover:scale-125"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dokumen PK</span>
                </div>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['renstra_sinkron'] }}</h3>
                <div class="mt-2 inline-flex px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-600 text-[11px] font-bold">
                    {{ $stats['renstra_badge'] }}
                </div>
            </div>
        </div>

        <div class="relative bg-white p-6 rounded-[28px] shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full transition-transform group-hover:scale-125"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-amber-100 text-amber-600 rounded-2xl shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estimasi Serapan</span>
                </div>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['serapan_anggaran'] }}</h3>
                <p class="text-xs text-slate-500 mt-2 font-medium">dari total pagu <span class="font-bold text-slate-700">Rp {{ $stats['pagu_anggaran'] }}</span></p>
            </div>
        </div>

        <div class="relative bg-white p-6 rounded-[28px] shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-rose-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-50 rounded-full transition-transform group-hover:scale-125"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-rose-100 text-rose-600 rounded-2xl shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kritis</span>
                    </div>
                    @if($stats['isu_kritis'] > 0)
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </span>
                    @endif
                </div>
                <h3 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $stats['isu_kritis'] }}</h3>
                <p class="text-xs text-rose-500 mt-2 font-bold">Indikator perlu tindak lanjut</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white rounded-[32px] shadow-[0_2px_20px_rgba(0,0,0,0.04)] border border-slate-100 p-8 flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Analisis Tren Indikator</h3>
                    <p class="text-sm text-slate-400 font-medium">Perbandingan performa antar triwulan</p>
                </div>
                <div class="bg-slate-50 p-1 rounded-xl border border-slate-200 hidden sm:flex">
                    <button class="px-4 py-1.5 text-xs font-bold bg-white rounded-lg shadow-sm text-indigo-600">Grafik</button>
                    <button class="px-4 py-1.5 text-xs font-medium text-slate-400 hover:text-slate-600 transition-colors">Tabel</button>
                </div>
            </div>
            
            <div class="relative h-72 w-full flex items-end justify-between gap-3 px-2 flex-1">
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none z-0">
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                </div>

                @foreach($chart_data as $index => $val)
                <div class="w-full bg-slate-50 rounded-t-2xl relative group h-full flex items-end overflow-hidden z-10 cursor-pointer">
                    <div style="height: {{ $val > 100 ? 100 : $val }}%" class="w-full bg-gradient-to-t from-indigo-500 to-purple-400 rounded-t-xl relative transition-all duration-500 group-hover:from-indigo-600 group-hover:to-purple-500 group-hover:shadow-[0_0_20px_rgba(99,102,241,0.4)]">
                        
                        <div class="opacity-0 group-hover:opacity-100 absolute -top-12 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] font-bold py-1.5 px-3 rounded-xl shadow-xl transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 whitespace-nowrap pointer-events-none z-20">
                            {{ $val }}%
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-slate-800 rotate-45"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="flex justify-between mt-6 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                 @foreach($chart_labels as $label)
                 <span class="w-full text-center">{{ $label }}</span>
                 @endforeach
            </div>
        </div>

        <div class="bg-white rounded-[32px] shadow-[0_2px_20px_rgba(0,0,0,0.04)] border border-slate-100 p-8 flex flex-col">
            <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                Highlight Kinerja
            </h3>
            
            <div class="flex-1 space-y-4 overflow-y-auto pr-1 hide-scrollbar">
                @foreach($highlights as $item)
                <div class="group relative p-4 rounded-2xl bg-slate-50/50 border border-slate-100 hover:bg-white hover:shadow-lg hover:shadow-indigo-500/5 hover:border-indigo-100 transition-all duration-300 cursor-default">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 p-3 rounded-xl bg-white shadow-sm border border-slate-100 group-hover:scale-110 transition-transform duration-300 {{ $item['color'] }}">
                            @if($item['icon'] == 'star')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @elseif($item['icon'] == 'warning')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $item['label'] }}</h4>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <button class="w-full mt-6 py-3 border border-dashed border-slate-200 rounded-xl text-xs font-bold text-slate-400 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all flex items-center justify-center gap-2 group">
                <span>Lihat Selengkapnya</span>
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white rounded-[32px] shadow-[0_2px_20px_rgba(0,0,0,0.04)] border border-slate-100 p-8">
            <h3 class="text-xl font-bold text-slate-800 mb-8">Log Aktivitas Terbaru</h3>
            
            <div class="space-y-6 relative before:absolute before:inset-y-2 before:left-[19px] before:w-[2px] before:bg-slate-100">
                @foreach($activities as $act)
                <div class="relative pl-10 group">
                    <div class="absolute left-3 top-2 w-4 h-4 rounded-full border-[3px] border-white bg-slate-200 group-hover:bg-indigo-500 group-hover:scale-125 transition-all shadow-sm z-10"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-700 transition-colors">{!! $act['aktivitas'] !!}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-slate-400 font-medium bg-slate-50 px-2 py-0.5 rounded border border-slate-100">{{ $act['waktu'] }}</span>
                                <span class="text-xs text-slate-500 flex items-center gap-1 font-semibold">
                                    by {{ $act['user'] }}
                                </span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ str_replace('bg-', 'bg-opacity-10 bg-', $act['status_color']) }} border border-transparent self-start sm:self-center">
                            {{ $act['status'] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[32px] shadow-2xl shadow-indigo-500/20 p-8 text-white relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-purple-400 opacity-20 rounded-full blur-xl"></div>
            
            <div class="relative z-10">
                <h3 class="text-lg font-bold mb-6">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('matrik.dokumen') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 p-4 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-lg group">
                        <svg class="w-6 h-6 text-indigo-200 group-hover:text-white mb-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-xs font-bold block">Buat RPJMD</span>
                    </a>
                    <a href="{{ route('pohon.kinerja') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 p-4 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-lg group">
                        <svg class="w-6 h-6 text-indigo-200 group-hover:text-white mb-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        <span class="text-xs font-bold block">Tambah Pohon</span>
                    </a>
                    <button class="col-span-2 bg-white text-indigo-700 hover:bg-indigo-50 py-3.5 rounded-2xl text-sm font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                        Lihat Semua Menu
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-white/10 relative z-10">
                <div class="flex items-start gap-3 bg-indigo-900/40 p-3.5 rounded-xl border border-white/5 backdrop-blur-sm">
                    <span class="relative flex h-2.5 w-2.5 mt-1 shrink-0">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                    </span>
                    <p class="text-xs text-indigo-100 leading-relaxed font-medium">
                        <span class="block text-white font-bold mb-0.5">Deadline Mendekat!</span>
                        Batas unggah realisasi triwulan IV tersisa <span class="underline decoration-rose-400 font-bold text-white">2 hari lagi</span>.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>