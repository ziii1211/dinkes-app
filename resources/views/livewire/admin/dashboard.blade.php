<div class="space-y-8 animate-fade-in-up font-sans text-slate-600 bg-slate-50/50 min-h-screen p-6">
    
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .gradient-text {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>

    <div class="relative w-full rounded-[30px] overflow-hidden shadow-xl shadow-indigo-500/20 group">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-blue-600"></div>
        <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] mix-blend-overlay"></div>
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-white opacity-10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-1000"></div>
        
        <div class="relative p-8 md:p-10 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-xs font-bold tracking-wider uppercase">
                        Overview Dashboard
                    </span>
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                    Dinas Kesehatan
                </h1>
                <p class="text-indigo-100 text-sm md:text-base font-medium max-w-xl leading-relaxed">
                    Selamat datang di panel kinerja. Pantau capaian RPJMD, realisasi anggaran, dan performa indikator secara real-time.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative group">
                    <select wire:model.live="periode" class="appearance-none bg-white/10 backdrop-blur-md text-white border border-white/20 hover:bg-white/20 rounded-2xl py-3 pl-4 pr-10 text-sm font-semibold focus:ring-2 focus:ring-white/50 cursor-pointer transition-all">
                        <option class="text-slate-800" value="RPJMD 2021-2026">RPJMD 2021-2026</option>
                        <option class="text-slate-800" value="RPJMD 2027-2032">RPJMD 2027-2032</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                
                <button class="bg-white text-indigo-600 hover:bg-indigo-50 px-5 py-3 rounded-2xl text-sm font-bold shadow-lg transition-all hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Input Data
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-[24px] p-6 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-indigo-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-indigo-100 text-indigo-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Capaian RPJMD</span>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $stats['capaian_rpjmd'] }}%</h3>
                
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ $stats['capaian_rpjmd'] > 100 ? 100 : $stats['capaian_rpjmd'] }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[24px] p-6 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dokumen PK</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-extrabold text-slate-800">{{ $stats['renstra_sinkron'] }}</h3>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100">
                        {{ $stats['renstra_badge'] }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Dokumen telah terverifikasi</p>
            </div>
        </div>

        <div class="bg-white rounded-[24px] p-6 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-amber-100 text-amber-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estimasi Serapan</span>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $stats['serapan_anggaran'] }}</h3>
                <p class="text-xs text-slate-400 mt-2 font-medium">Dari Pagu: <span class="text-slate-600">Rp {{ $stats['pagu_anggaran'] }}</span></p>
            </div>
        </div>

        <div class="bg-white rounded-[24px] p-6 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:shadow-xl hover:shadow-rose-500/10 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-rose-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-rose-100 text-rose-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Underperform</span>
                    </div>
                    @if($stats['isu_kritis'] > 0)
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </span>
                    @endif
                </div>
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $stats['isu_kritis'] }}</h3>
                <p class="text-xs text-rose-500 mt-2 font-semibold bg-rose-50 inline-block px-2 py-0.5 rounded-lg border border-rose-100">Perlu Tindak Lanjut</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white rounded-[30px] p-8 shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Analisis Tren Indikator</h3>
                    <p class="text-xs text-slate-400 font-medium">Monitoring performa bulanan tahun berjalan</p>
                </div>
                <button class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-colors">
                    Lihat Detail
                </button>
            </div>
            
            <div class="relative h-72 w-full flex items-end justify-between gap-3 px-2">
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none z-0">
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                    <div class="border-b border-dashed border-slate-100 h-full w-full"></div>
                </div>

                @foreach($chart_data as $index => $val)
                <div class="w-full bg-slate-50 rounded-t-2xl relative group h-full flex items-end overflow-hidden z-10 cursor-pointer">
                    <div style="height: {{ $val > 100 ? 100 : $val }}%" class="w-full bg-gradient-to-t from-indigo-500 to-purple-400 rounded-t-xl relative transition-all duration-500 group-hover:from-indigo-600 group-hover:to-purple-500 group-hover:shadow-[0_0_20px_rgba(99,102,241,0.3)]">
                        <div class="opacity-0 group-hover:opacity-100 absolute -top-12 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] font-bold py-1.5 px-3 rounded-lg shadow-xl transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 whitespace-nowrap pointer-events-none">
                            {{ $val }}%
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-slate-800 rotate-45"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                 @foreach($chart_labels as $label)
                 <span class="w-full text-center">{{ $label }}</span>
                 @endforeach
            </div>
        </div>

        <div class="bg-white rounded-[30px] p-8 shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-slate-100 flex flex-col">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                Highlight Kinerja
            </h3>
            
            <div class="flex-1 space-y-4 overflow-y-auto pr-1 custom-scrollbar">
                @foreach($highlights as $item)
                <div class="group relative p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-lg hover:shadow-indigo-500/5 hover:border-indigo-100 transition-all duration-300 cursor-default">
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
                <span>Lihat Analisis Lengkap</span>
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white rounded-[30px] p-8 shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Log Aktivitas Terbaru</h3>
            
            <div class="space-y-6 relative before:absolute before:inset-y-0 before:left-[19px] before:w-[2px] before:bg-slate-100">
                @foreach($activities as $act)
                <div class="relative pl-10 group">
                    <div class="absolute left-3 top-2 w-4 h-4 rounded-full border-[3px] border-white bg-slate-200 group-hover:bg-indigo-500 group-hover:scale-125 transition-all shadow-sm z-10"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-700 transition-colors">{!! $act['aktivitas'] !!}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-slate-400 font-medium">{{ $act['waktu'] }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="text-xs text-slate-500 flex items-center gap-1 font-semibold">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $act['user'] }}
                                </span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ str_replace('bg-', 'bg-opacity-20 bg-', $act['status_color']) }} border border-transparent self-start sm:self-center">
                            {{ $act['status'] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[30px] shadow-2xl shadow-indigo-500/30 p-8 text-white relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-purple-500 opacity-30 rounded-full blur-2xl"></div>
            
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
                <div class="flex items-start gap-3 bg-indigo-900/40 p-3 rounded-xl border border-white/5 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2 mt-1.5 shrink-0">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                    <p class="text-xs text-indigo-100 leading-relaxed font-medium">
                        Batas unggah realisasi triwulan IV tersisa <span class="font-bold text-white underline decoration-rose-400">2 hari lagi</span>.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>