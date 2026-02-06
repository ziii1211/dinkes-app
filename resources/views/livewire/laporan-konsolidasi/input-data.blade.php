<div class="space-y-6">
    
    {{-- HELPER PHP UNTUK RUMUS DI VIEW --}}
    @php
        $parseNum = function($val) {
            if(is_numeric($val)) return (float)$val;
            return (float) preg_replace('/[^0-9]/', '', $val ?? '0');
        };

        $hitungPersen = function($pembilang, $penyebut) use ($parseNum) {
            $a = $parseNum($pembilang);
            $b = $parseNum($penyebut);
            $hasil = ($b > 0) ? ($a / $b) * 100 : 0;
            
            // LOGIKA BARU: Cap maksimal 100%
            return min($hasil, 100);
        };
    @endphp

    {{-- HEADER & INFO TOTAL --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('laporan-konsolidasi.index') }}" class="hover:text-blue-600 transition-colors">&larr; Kembali</a>
                <span>/</span>
                <span>Input Data</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $laporan->judul }}</h2>
            <div class="flex items-center gap-3 mt-1">
                <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">{{ $laporan->bulan }}</span>
                <span class="px-2.5 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs font-bold border border-gray-200">{{ $laporan->tahun }}</span>
            </div>
        </div>

        <div class="flex gap-6 items-center bg-gray-50 px-5 py-3 rounded-xl border border-gray-200 shadow-inner">
            <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Total Anggaran</p>
                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
            </div>
            <div class="w-px h-10 bg-gray-300"></div>
            <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Total Realisasi</p>
                <p class="text-lg font-bold text-green-600">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- MESSAGE --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- TOOLBAR --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <h3 class="text-lg font-bold text-gray-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Rincian Matrik
            </h3>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="syncData" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed" title="Tarik data baru dari Master Data">
                <svg wire:loading.remove wire:target="syncData" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <svg wire:loading wire:target="syncData" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Sinkron
            </button>

            <button wire:click="openProgramModal" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah
            </button>

            <button wire:click="saveAll" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <svg wire:loading class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Simpan
            </button>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden relative flex flex-col">
        <div class="overflow-x-auto min-h-[400px]"> 
            <table class="w-full text-sm border-collapse">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[11px] border-b border-slate-200">
                    <tr>
                        <th rowspan="2" class="px-2 py-3 w-16 text-center border-r border-slate-200 align-middle">Kode</th>
                        <th rowspan="2" class="px-4 py-3 min-w-[250px] text-center border-r border-slate-200 align-middle">Program / Kegiatan / Sub Kegiatan</th>
                        <th rowspan="2" class="px-2 py-3 w-48 text-center border-r border-slate-200 align-middle">Indikator</th>
                        <th rowspan="2" class="px-2 py-3 w-16 text-center border-r border-slate-200 align-middle">Satuan</th>
                        <th rowspan="2" class="px-2 py-3 w-20 text-center border-r border-slate-200 align-middle">Target</th>
                        <th rowspan="2" class="px-2 py-3 w-36 text-center border-r border-slate-200 align-middle">Pagu<br>Anggaran</th>
                        
                        <th colspan="2" class="px-2 py-1 text-center border-r border-b border-slate-200 align-middle">Realisasi</th>
                        <th colspan="2" class="px-2 py-1 text-center border-r border-b border-slate-200 align-middle">% Capaian</th>
                        
                        <th rowspan="2" class="px-2 py-3 w-16 text-center align-middle">Aksi</th>
                    </tr>
                    <tr>
                        <th class="px-2 py-2 w-32 text-center border-r border-slate-200">Keuangan (Rp)</th>
                        <th class="px-2 py-2 w-20 text-center border-r border-slate-200">Fisik</th>
                        <th class="px-2 py-2 w-16 text-center border-r border-slate-200">Keu</th>
                        <th class="px-2 py-2 w-16 text-center border-r border-slate-200">Fisik</th>
                    </tr>
                </thead>
                
                {{-- LOOP DATA --}}
                @forelse($reportData as $progId => $group)
                    @php 
                        $program = $group['program'];
                        
                        // INPUT PROGRAM
                        $progTarget = $programInputs[$program->id]['target'] ?? 0;
                        $progFisik = $programInputs[$program->id]['realisasi_fisik'] ?? 0;
                        $progPagu = $programInputs[$program->id]['pagu_anggaran'] ?? 0;
                        $progRealisasi = $programInputs[$program->id]['pagu_realisasi'] ?? 0;
                        
                        // RUMUS PROGRAM
                        $persenKeuProg = $hitungPersen($progRealisasi, $progPagu);
                        $persenFisikProg = $hitungPersen($progFisik, $progTarget);
                    @endphp

                    <tbody wire:key="prog-{{ $program->id }}" x-data="{ expanded: true }" class="border-b border-gray-100 group">
                        
                        {{-- === BARIS PROGRAM === --}}
                        <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                            <td class="px-2 py-3 font-bold text-gray-800 border-r border-gray-200 font-mono text-center align-top">
                                <button @click="expanded = !expanded" class="mr-1 text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-90': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                {{ $program->kode }}
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200 align-top text-left">
                                <div class="flex items-start gap-2">
                                    <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 mt-0.5">PROG</span>
                                    <span class="font-bold text-gray-800 text-xs uppercase">{{ $program->nama ?? $program->nama_program }}</span>
                                </div>
                            </td>
                            <td colspan="2" class="border-r border-gray-200"></td>

                            {{-- Target Program --}}
                            <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('programInputs.{{ $program->id }}.target', '{{ $progTarget }}')">
                                <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-center font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 px-1 py-1.5" placeholder="">
                            </td>

                            {{-- Pagu Anggaran Program --}}
                            <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('programInputs.{{ $program->id }}.pagu_anggaran', '{{ $progPagu }}')">
                                <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-bold text-blue-800 bg-white border border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 px-2 py-1.5" placeholder="0">
                            </td>
                            
                            {{-- Realisasi Keuangan Program --}}
                            <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('programInputs.{{ $program->id }}.pagu_realisasi', '{{ $progRealisasi }}')">
                                <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-bold text-green-800 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5" placeholder="0">
                            </td>
                            
                            {{-- Realisasi Fisik Program --}}
                            <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('programInputs.{{ $program->id }}.realisasi_fisik', '{{ $progFisik }}')">
                                <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5" placeholder="">
                            </td>

                            {{-- % Capaian Keuangan Program --}}
                            <td class="text-center text-[10px] font-bold text-gray-600 border-r border-gray-200 align-middle">
                                {{ number_format($persenKeuProg, 0) }}%
                            </td>
                            {{-- % Capaian Fisik Program --}}
                            <td class="text-center text-[10px] font-bold text-gray-600 border-r border-gray-200 align-middle">
                                {{ number_format($persenFisikProg, 0) }}%
                            </td>

                            {{-- AKSI PROGRAM --}}
                            <td class="p-2 text-center align-middle">
                                <button wire:click="deleteProgram({{ $program->id }})" wire:confirm="Yakin hapus Program beserta seluruh Kegiatan dan Sub Kegiatannya?" class="text-red-400 hover:text-red-600 p-1" title="Hapus Program">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>

                        {{-- === LOOP KEGIATAN === --}}
                        @foreach($group['kegiatans'] as $kegData)
                            @php 
                                $kegiatan = $kegData['kegiatan'];
                                $details  = $kegData['details'];
                                
                                // INPUT KEGIATAN
                                $kegTarget = $kegiatanInputs[$kegiatan->id]['target'] ?? 0;
                                $kegFisik = $kegiatanInputs[$kegiatan->id]['realisasi_fisik'] ?? 0;
                                $kegPagu = $kegiatanInputs[$kegiatan->id]['pagu_anggaran'] ?? 0;
                                $kegRealisasi = $kegiatanInputs[$kegiatan->id]['pagu_realisasi'] ?? 0;
                                
                                // RUMUS KEGIATAN
                                $persenKeuKeg = $hitungPersen($kegRealisasi, $kegPagu);
                                $persenFisikKeg = $hitungPersen($kegFisik, $kegTarget);
                            @endphp

                            <tr x-show="expanded" x-transition class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-2 py-2 font-semibold text-gray-600 border-r border-gray-200 font-mono text-center align-top text-[11px]">
                                    {{ $kegiatan->kode }}
                                </td>
                                <td class="px-4 py-2 border-r border-gray-200 align-top text-left">
                                    <div class="flex items-start gap-2">
                                        <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 mt-0.5">KEG</span>
                                        <span class="font-semibold text-gray-700 text-xs">{{ $kegiatan->nama ?? $kegiatan->nama_kegiatan }}</span>
                                    </div>
                                </td>
                                <td colspan="2" class="border-r border-gray-200"></td>

                                {{-- Target Kegiatan --}}
                                <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('kegiatanInputs.{{ $kegiatan->id }}.target', '{{ $kegTarget }}')">
                                    <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-center font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 px-1 py-1.5" placeholder="">
                                </td>

                                {{-- Pagu Anggaran Kegiatan --}}
                                <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('kegiatanInputs.{{ $kegiatan->id }}.pagu_anggaran', '{{ $kegPagu }}')">
                                    <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-semibold text-gray-700 bg-white border border-gray-300 rounded shadow-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 px-2 py-1.5" placeholder="0">
                                </td>
                                
                                {{-- Realisasi Keuangan Kegiatan --}}
                                <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('kegiatanInputs.{{ $kegiatan->id }}.pagu_realisasi', '{{ $kegRealisasi }}')">
                                    <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-semibold text-gray-700 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5" placeholder="0">
                                </td>
                                
                                {{-- Realisasi Fisik Kegiatan --}}
                                <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('kegiatanInputs.{{ $kegiatan->id }}.realisasi_fisik', '{{ $kegFisik }}')">
                                    <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5" placeholder="">
                                </td>
                                
                                {{-- % Capaian Keuangan Kegiatan --}}
                                <td class="text-center text-[10px] font-medium text-gray-600 border-r border-gray-200 align-middle">
                                    {{ number_format($persenKeuKeg, 0) }}%
                                </td>
                                {{-- % Capaian Fisik Kegiatan --}}
                                <td class="text-center text-[10px] font-medium text-gray-600 border-r border-gray-200 align-middle">
                                    {{ number_format($persenFisikKeg, 0) }}%
                                </td>

                                {{-- AKSI KEGIATAN --}}
                                <td class="p-2 text-center align-middle">
                                    <button wire:click="deleteKegiatan({{ $kegiatan->id }})" wire:confirm="Yakin hapus Kegiatan beserta Sub Kegiatannya?" class="text-red-400 hover:text-red-600 p-1" title="Hapus Kegiatan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>

                            {{-- === BARIS SUB KEGIATAN === --}}
                            @foreach($details as $detail)
                                @php
                                    $subTarget = $inputs[$detail->id]['target'] ?? 0;
                                    $subPagu = $inputs[$detail->id]['pagu_anggaran'] ?? 0;
                                    $subRealisasiKeu = $inputs[$detail->id]['pagu_realisasi'] ?? 0;
                                    $subRealisasiFisik = $inputs[$detail->id]['realisasi_fisik'] ?? 0;

                                    // Hitung Persen Sub Kegiatan
                                    $persenKeuSub = $hitungPersen($subRealisasiKeu, $subPagu);
                                    $persenFisikSub = $hitungPersen($subRealisasiFisik, $subTarget);
                                @endphp

                                <tr x-show="expanded" x-transition class="hover:bg-gray-50 transition-colors group border-b border-gray-100 bg-white">
                                    <td class="px-2 py-2 text-center font-mono text-[10px] text-gray-500 border-r border-gray-200 align-top pt-3">
                                        {{ $detail->kode }}
                                    </td>
                                    <td class="px-4 py-2 border-r border-gray-200 pl-8 align-top pt-3 text-left">
                                        <div class="flex items-start gap-2">
                                            <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-700 border border-purple-200 mt-0.5">SUB</span>
                                            <div class="text-gray-600 leading-snug text-xs font-medium">
                                                {{ $detail->subKegiatan?->nama ?? 'Sub Kegiatan' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 border-r border-gray-200 align-top text-[10px] text-gray-600 text-left">
                                        {!! nl2br(e($detail->sub_output ?? '-')) !!}
                                    </td>
                                    <td class="px-2 py-2 border-r border-gray-200 align-top text-center text-[10px] text-gray-600">
                                        {!! nl2br(e($detail->satuan_unit ?? '-')) !!}
                                    </td>
                                    
                                    {{-- Target (INPUT) --}}
                                    <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('inputs.{{ $detail->id }}.target', '{{ $subTarget }}')">
                                        <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-center font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 px-1 py-1.5" placeholder="">
                                    </td>

                                    {{-- Pagu Anggaran --}}
                                    <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('inputs.{{ $detail->id }}.pagu_anggaran', '{{ $subPagu }}')">
                                        <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 px-2 py-1.5" placeholder="Rp 0">
                                    </td>
                                    
                                    {{-- Realisasi Keuangan --}}
                                    <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('inputs.{{ $detail->id }}.pagu_realisasi', '{{ $subRealisasiKeu }}')">
                                        <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-bold text-green-700 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5" placeholder="Rp 0">
                                    </td>

                                    {{-- Realisasi Fisik (INPUT) --}}
                                    <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('inputs.{{ $detail->id }}.realisasi_fisik', '{{ $subRealisasiFisik }}')">
                                        <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5" placeholder="">
                                    </td>

                                    {{-- % Capaian Keuangan --}}
                                    <td class="px-2 py-2 text-center text-[10px] text-gray-600 border-r border-gray-200 align-middle">
                                        {{ number_format($persenKeuSub, 0) }}%
                                    </td>

                                    {{-- % Capaian Fisik --}}
                                    <td class="px-2 py-2 text-center text-[10px] text-gray-600 border-r border-gray-200 align-middle">
                                        {{ number_format($persenFisikSub, 0) }}%
                                    </td>
                                    
                                    {{-- AKSI SUB KEGIATAN --}}
                                    <td class="p-1 text-center align-middle">
                                        <button wire:click="deleteSubKegiatan({{ $detail->id }})" wire:confirm="Hapus sub kegiatan ini?" class="text-red-400 hover:text-red-600 p-1" title="Hapus Sub Kegiatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                @empty
                    <tbody>
                        <tr><td colspan="11" class="text-center py-10 text-gray-400">Belum ada data</td></tr>
                    </tbody>
                @endforelse
            </table>
        </div>
        
        {{-- PAGINATION --}}
        <div class="bg-gray-50 border-t border-gray-200 px-4 py-3 sm:px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-700">
                @if($reportData->total() > 0)
                    Showing <span class="font-medium">{{ $reportData->firstItem() }}</span> to <span class="font-medium">{{ $reportData->lastItem() }}</span> of <span class="font-medium">{{ $reportData->total() }}</span> results
                @else
                    Showing 0 results
                @endif
            </div>
            <div>{{ $reportData->links('livewire::tailwind') }}</div>
        </div>
    </div>
    
    {{-- Script Alpine --}}
    <script>
        document.addEventListener('alpine:init', () => {
            // COMPONENT: Rupiah Input (Uang)
            Alpine.data('rupiahInput', (modelName, initialValue) => ({
                displayValue: '', modelName: modelName,
                init() { this.formatInitial(initialValue); },
                updateWire(e) { this.formatCurrency(e); this.$wire.set(this.modelName, this.displayValue); },
                formatCurrency(e) {
                    let inputVal = e.target.value;
                    let numberString = inputVal.replace(/[^,\d]/g, '').toString();
                    let split = numberString.split(',');
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                    if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
                    this.displayValue = rupiah ? 'Rp ' + rupiah : '';
                },
                formatInitial(val) {
                    if(!val) { this.displayValue = ''; return; }
                    let stringVal = val.toString().replace(/[^0-9]/g, '');
                    if(!stringVal || stringVal == '0') { this.displayValue = ''; return; }
                    let sisa = stringVal.length % 3;
                    let rupiah = stringVal.substr(0, sisa);
                    let ribuan = stringVal.substr(sisa).match(/\d{3}/gi);
                    if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
                    this.displayValue = 'Rp ' + rupiah;
                }
            }));

            // COMPONENT: Number Input (Target & Fisik)
            Alpine.data('numberInput', (modelName, initialValue) => ({
                displayValue: '', 
                modelName: modelName,
                init() {
                    // Jika 0 atau null, tampilkan kosong
                    if (!initialValue || initialValue == 0 || initialValue == '0') {
                        this.displayValue = '';
                    } else {
                        this.displayValue = initialValue;
                    }
                },
                updateWire(e) {
                    this.$wire.set(this.modelName, this.displayValue);
                }
            }));
        });
    </script>

    {{-- MODAL TAMBAH PROGRAM --}}
    @if($isOpenProgram)
    <div class="fixed inset-0 z-[99] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" wire:click="closeProgramModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Pilih Program</h3>
                    <p class="text-sm text-gray-500 mt-1">Data akan ditambahkan secara otomatis.</p>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Program Renstra</label>
                    <select wire:model="selectedProgramId" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                        <option value="">-- Pilih Program --</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->kode }} - {{ $prog->nama ?? $prog->nama_program }}</option>
                        @endforeach
                    </select>
                    @error('selectedProgramId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button wire:click="addProgram" class="inline-flex justify-center items-center rounded-lg shadow-sm px-4 py-2 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none transition-all">Tambahkan</button>
                    <button wire:click="closeProgramModal" class="inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>