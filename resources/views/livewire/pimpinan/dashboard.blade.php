<div class="min-h-screen bg-[#F8FAFC] font-sans text-slate-600 relative overflow-x-hidden selection:bg-indigo-500 selection:text-white pb-20">
    
    <x-slot:title>Dashboard Pimpinan</x-slot>
    {{-- CUSTOM STYLES & ANIMATIONS --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Smooth Scroll & Custom Scrollbar */
        .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Animation Keyframes */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes shine {
            100% { left: 125%; }
        }
        @keyframes entry {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-entry { animation: entry 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .animate-shine { position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), transparent); transform: skewX(-20deg); animation: shine 3s infinite; }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        /* Glassmorphism Utilities */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        }
    </style>

    {{-- DYNAMIC BACKGROUND MESH --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-[800px] h-[800px] bg-indigo-200/20 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-[600px] h-[600px] bg-fuchsia-200/20 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-float" style="animation-delay: 2s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[900px] bg-blue-100/30 rounded-full mix-blend-multiply filter blur-[120px] opacity-50"></div>
    </div>

    {{-- NAVIGATION / HEADER (Floating Glass) --}}
    <div class="sticky top-4 z-50 px-4 mb-8">
        <div class="max-w-[1600px] mx-auto glass rounded-2xl px-6 py-4 shadow-sm transition-all hover:shadow-md animate-entry">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- Brand --}}
                <div class="flex items-center gap-4">
                    <div class="relative group cursor-pointer">
                        <div class="absolute -inset-2 bg-gradient-to-r from-indigo-500 to-fuchsia-500 rounded-full blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                        <div class="relative w-12 h-12 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm border border-slate-100">
                            <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-jakarta font-bold text-slate-800 tracking-tight">Dashboard Pimpinan</h1>
                        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Monitoring Mode
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative group">
                        <select wire:model.live="periode" class="appearance-none pl-10 pr-8 py-2.5 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500/20 cursor-pointer w-full sm:w-48 transition-all hover:bg-slate-100">
                            <option>Renstra 2026-2030</option>
                        </select>
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>

                    <div class="relative group">
                        <select wire:model.live="perangkat_daerah" class="appearance-none pl-10 pr-8 py-2.5 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500/20 cursor-pointer w-full sm:w-64 transition-all hover:bg-slate-100">
                            <option value="">Semua Unit Kerja</option>
                            @foreach($jabatans as $jab)
                                <option value="{{ $jab->id }}">
                                    @if($jab->level == 0)
                                        {{ strtoupper($jab->nama) }}
                                    @else
                                        {{ $jab->nama }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="max-w-[1600px] mx-auto px-6 space-y-8 font-jakarta">

        {{-- ALERT BANNER (Info Jadwal) --}}
        @if($deadline)
        <div class="animate-entry relative overflow-hidden rounded-3xl bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 shadow-xl shadow-indigo-500/20 p-1">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-soft-light"></div>
            <div class="bg-white/10 backdrop-blur-sm rounded-[20px] p-6 relative z-10">
                <div class="animate-shine"></div>
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-white">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 border border-white/20 flex items-center justify-center backdrop-blur-md shadow-inner">
                            <svg class="w-7 h-7 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Periode Penilaian: {{ $bulan_nama }} {{ $deadline->tahun }}</h3>
                            <p class="text-indigo-100 font-medium text-sm mt-0.5 opacity-90">
                                @if($sisa_hari > 0)
                                    Sisa waktu input data bagi unit kerja: <span class="bg-white text-indigo-700 px-2 py-0.5 rounded-md font-bold mx-1 shadow-sm">{{ $sisa_hari }} Hari</span>.
                                @else
                                    <span class="bg-rose-500 text-white px-2 py-0.5 rounded-md font-bold mx-1 shadow-sm">Ditutup!</span> Periode input data telah berakhir.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Banner Jika Tidak Ada Jadwal Aktif --}}
        <div class="animate-entry relative overflow-hidden rounded-3xl bg-slate-100 border border-slate-200 p-6">
            <div class="flex items-center gap-4 text-slate-500">
                <div class="w-10 h-10 rounded-xl bg-slate-200 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-700">Tidak ada jadwal aktif</h3>
                    <p class="text-xs">Saat ini sistem tidak dalam periode pengukuran kinerja aktif.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- BENTO GRID LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-entry delay-100">
            
            {{-- CHART SECTION --}}
            <div class="lg:col-span-8 glass-card rounded-[2.5rem] p-8 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-800">Analisis Capaian Global</h3>
                        <p class="text-sm text-slate-400 font-medium">Monitoring performa rata-rata seluruh unit</p>
                    </div>
                    @if($is_dummy_chart)
                    <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-bold border border-amber-100 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Simulasi
                    </span>
                    @endif
                </div>

                {{-- Chart Container --}}
                <div class="h-[340px] w-full flex items-end justify-between gap-2 sm:gap-4 relative z-10 px-2 pb-2">
                    {{-- Guide Lines --}}
                    <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-30">
                        <div class="border-t border-dashed border-slate-300 w-full h-0"></div>
                        <div class="border-t border-dashed border-slate-300 w-full h-0"></div>
                        <div class="border-t border-dashed border-slate-300 w-full h-0"></div>
                        <div class="border-t border-slate-200 w-full h-0"></div>
                    </div>

                    @foreach($chart_data as $index => $val)
                    <div class="w-full h-full flex flex-col justify-end group/bar cursor-pointer relative">
                        <div class="relative w-full flex items-end justify-center h-full">
                            {{-- Tooltip --}}
                            <div class="opacity-0 group-hover/bar:opacity-100 absolute bottom-full mb-3 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] font-bold py-1.5 px-3 rounded-lg shadow-xl transition-all duration-300 transform translate-y-2 group-hover/bar:translate-y-0 z-20 whitespace-nowrap">
                                {{ $val }}%
                                <svg class="absolute -bottom-1 left-1/2 -translate-x-1/2 text-slate-800 w-2 h-2" viewBox="0 0 255 255" xml:space="preserve"><polygon class="fill-current" points="0,0 127.5,127.5 255,0"/></svg>
                            </div>

                            {{-- The Bar --}}
                            <div style="height: {{ $val > 100 ? 100 : ($val < 5 ? 5 : $val) }}%" 
                                 class="w-full max-w-[50px] rounded-t-2xl relative transition-all duration-700 cubic-bezier(0.4, 0, 0.2, 1)
                                 {{ $val >= 100 ? 'bg-gradient-to-t from-emerald-500 to-emerald-400' : 'bg-gradient-to-t from-indigo-600 to-indigo-400' }}
                                 opacity-90 hover:opacity-100 group-hover/bar:scale-y-[1.03] group-hover/bar:shadow-[0_0_20px_-5px_rgba(99,102,241,0.5)]">
                                 <div class="absolute top-0 left-0 right-0 h-1/2 bg-gradient-to-b from-white/30 to-transparent rounded-t-2xl"></div>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 mt-3 text-center transition-colors group-hover/bar:text-indigo-600 uppercase tracking-wider">{{ substr($chart_labels[$index], 0, 3) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- HIGHLIGHT CARDS (Right Column) --}}
            <div class="lg:col-span-4 flex flex-col h-full gap-5">
                {{-- Card Header --}}
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-lg font-bold text-slate-800">Sorotan Kinerja</h3>
                    <button wire:click="openHighlightModal" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                        Lihat Semua
                    </button>
                </div>

                {{-- Scrollable List --}}
                <div class="flex-1 overflow-y-auto custom-scroll pr-2 space-y-4 max-h-[400px]">
                    @foreach($highlights as $item)
                    {{-- Updated Logic to handle 'unreported' tab opening --}}
                    <div wire:click="openHighlightModal('{{ $item['label'] == 'Top Performer' ? 'performer' : ($item['label'] == 'Belum Lapor' ? 'unreported' : ($item['label'] == 'Perlu Perhatian' ? 'isu' : 'dokumen')) }}')" 
                         class="group relative bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden">
                        
                        <div class="absolute inset-0 bg-gradient-to-r {{ $item['label'] == 'Perlu Perhatian' ? 'from-rose-50/50' : ($item['label'] == 'Top Performer' ? 'from-amber-50/50' : ($item['label'] == 'Belum Lapor' ? 'from-red-50/50' : 'from-blue-50/50')) }} to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110 shadow-sm
                                {{ $item['label'] == 'Perlu Perhatian' ? 'bg-rose-100 text-rose-600' : 
                                  ($item['label'] == 'Belum Lapor' ? 'bg-red-100 text-red-600' :
                                  ($item['label'] == 'Top Performer' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600')) }}">
                                @if($item['icon'] == 'star') <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @elseif($item['icon'] == 'warning') <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @else <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm group-hover:text-indigo-900 transition-colors">{{ $item['label'] }}</h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- BOTTOM SECTION (ACTIVITY LOG ONLY) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-entry delay-200">
            
            {{-- ACTIVITY LOG --}}
            <div class="lg:col-span-12 glass-card rounded-[2.5rem] p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Log Aktivitas SKPD</h3>
                </div>

                {{-- PERBAIKAN: Scroll Wrapper untuk Activity --}}
                <div class="max-h-[400px] overflow-y-auto custom-scroll pr-2">
                    <div class="space-y-0 relative before:absolute before:inset-0 before:ml-5 before:w-0.5 before:-translate-x-px before:bg-gradient-to-b before:from-slate-200 before:via-slate-100 before:to-transparent">
                        @foreach($activities as $act)
                        <div class="relative flex gap-6 pb-6 group last:pb-0">
                            <div class="absolute left-0 mt-1.5 ml-2.5 -translate-x-1/2 translate-y-0.5 w-5 h-5 rounded-full border-4 border-white bg-slate-200 group-hover:bg-indigo-500 group-hover:scale-110 transition-all z-10 shadow-sm"></div>
                            
                            <div class="flex-1 bg-slate-50/50 hover:bg-white p-4 rounded-2xl border border-transparent hover:border-indigo-100 hover:shadow-sm transition-all duration-300">
                                <div class="flex justify-between items-start gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{!! $act['aktivitas'] !!}</p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-200/50 px-2 py-0.5 rounded">{{ $act['user'] }}</span>
                                            <span class="text-xs text-slate-400 font-medium">{{ $act['waktu'] }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ str_replace('bg-', 'bg-opacity-10 text-', $act['status_color']) }} {{ $act['status_color'] }}">
                                        {{ $act['status'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
        </div>

    </div>

    {{-- MODAL (PREMIUM BLURRED) --}}
    @if($isOpenHighlight)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Backdrop --}}
            <div wire:click="closeHighlightModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity animate-entry" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div class="relative inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-slate-200 animate-entry delay-100">
                
                {{-- Modal Header --}}
                <div class="bg-white px-8 py-6 border-b border-slate-100 flex justify-between items-center sticky top-0 z-10">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Detail Kinerja Organisasi</h3>
                        <p class="text-sm text-slate-400">Laporan mendalam monitoring indikator</p>
                    </div>
                    <button wire:click="closeHighlightModal" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-500 flex items-center justify-center transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="bg-slate-50/50 px-8 border-b border-slate-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        @foreach([
                            'performer' => ['🏆 Top Performer', 'indigo'],
                            'unreported' => ['⛔ Belum Lapor', 'rose'],
                            'isu' => ['⚠️ Isu Kritis', 'amber'], 
                            'dokumen' => ['📄 Dokumen PK', 'emerald']
                        ] as $key => $val)
                        <button wire:click="switchTab('{{ $key }}')" 
                                class="{{ $activeTab === $key ? 'border-'.$val[1].'-500 text-'.$val[1].'-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }} 
                                       whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-all flex items-center gap-2">
                           {{ $val[0] }}
                        </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Modal Content --}}
                <div class="p-8 bg-slate-50/30">
                    @if($activeTab === 'performer')
                         @if(count($detailPerformers) > 0)
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                <div class="max-h-[400px] overflow-y-auto custom-scroll">
                                    <table class="min-w-full divide-y divide-slate-100">
                                        <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50">Rank</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50">Unit Kerja</th>
                                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50">Indikator</th>
                                                <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50">Skor</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-slate-100">
                                            @foreach($detailPerformers as $idx => $item)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $idx==0 ? 'text-amber-500' : 'text-slate-500' }}">#{{ $idx+1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $item['jabatan'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center">{{ $item['jumlah_indikator'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <span class="px-2 py-1 text-xs font-bold rounded bg-indigo-50 text-indigo-600 border border-indigo-100">{{ $item['score'] }}%</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-10 text-slate-400">Belum ada data performer.</div>
                        @endif

                    @elseif($activeTab === 'unreported')
                        {{-- Unreported Content --}}
                        @if(count($detailUnreported) > 0)
                        <div class="overflow-hidden rounded-xl border border-rose-200 bg-rose-50/30 shadow-sm">
                             <div class="max-h-[500px] overflow-y-auto custom-scroll">
                                 <table class="min-w-full divide-y divide-rose-100">
                                    <thead class="bg-rose-100 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-rose-700 uppercase tracking-wider">Unit Kerja / Jabatan</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-rose-700 uppercase tracking-wider">Nama Pegawai</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-rose-700 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-rose-50">
                                        @foreach($detailUnreported as $item)
                                        <tr class="hover:bg-rose-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-slate-800">{{ $item['jabatan'] }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-slate-600">{{ $item['pegawai'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-600 border border-rose-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-xs text-rose-500 font-medium italic">* Data diambil berdasarkan Jadwal Pengukuran yang sedang aktif.</p>
                        </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-12 text-emerald-500 bg-emerald-50/50 rounded-2xl border-2 border-dashed border-emerald-100">
                                <svg class="w-12 h-12 opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-bold text-lg">Luar Biasa!</span>
                                <span class="text-sm text-emerald-600/70">Semua pegawai telah melaporkan kinerja.</span>
                            </div>
                        @endif

                    @elseif($activeTab === 'isu')
                        <div class="max-h-[400px] overflow-y-auto custom-scroll pr-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @forelse($detailIsuKritis as $isu)
                                <div class="bg-white p-4 rounded-xl border border-amber-100 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-[10px] font-bold bg-amber-50 text-amber-500 px-2 py-0.5 rounded uppercase">{{ $isu['jabatan'] }}</span>
                                        </div>
                                        <h4 class="font-bold text-sm text-slate-800 line-clamp-2">{{ $isu['indikator'] }}</h4>
                                    </div>
                                    <div class="mt-4 flex items-end justify-between border-t border-slate-50 pt-3">
                                        <div class="text-xs text-slate-500 space-y-1">
                                            <div>Target: <span class="font-bold text-slate-700">{{ $isu['target'] }}</span></div>
                                            <div>Realisasi: <span class="font-bold text-slate-700">{{ $isu['realisasi'] }}</span></div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-black text-amber-500 leading-none">{{ $isu['capaian'] }}%</div>
                                            <div class="text-[10px] text-amber-300 font-bold uppercase mt-1">Capaian</div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-span-2 flex flex-col items-center justify-center py-12 text-emerald-500 bg-emerald-50/50 rounded-2xl border-2 border-dashed border-emerald-100">
                                    <svg class="w-12 h-12 opacity-50 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="font-bold">Semua Aman!</span>
                                    <span class="text-xs text-emerald-600/70">Tidak ada indikator kritis.</span>
                                </div>
                                @endforelse
                            </div>
                        </div>

                    @else
                        {{-- Dokumen Content --}}
                        @if(count($detailDokumen) > 0)
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                             <div class="max-h-[400px] overflow-y-auto custom-scroll">
                                 <table class="min-w-full divide-y divide-slate-100">
                                    <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase bg-slate-50">Unit / Pegawai</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase bg-slate-50">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase bg-slate-50">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        @foreach($detailDokumen as $doc)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-slate-800">{{ $doc['jabatan'] }}</div>
                                                <div class="text-xs text-slate-500">{{ $doc['pegawai'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $doc['status'] == 'Final' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                    {{ $doc['status'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-500">{{ $doc['tanggal'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                            <div class="text-center py-10 text-slate-400">Data dokumen kosong.</div>
                        @endif
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse">
                    <button wire:click="closeHighlightModal" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2 bg-slate-800 text-base font-medium text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 sm:ml-3 sm:w-auto sm:text-sm transition-all hover:scale-105">
                        Tutup Panel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>