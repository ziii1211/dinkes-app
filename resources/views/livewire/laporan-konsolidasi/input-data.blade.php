<div class="space-y-6">
    
    {{-- HEADER & INFO TOTAL (TETAP SAMA) --}}
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
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-20 text-center border-r border-slate-200">Kode</th>
                        <th class="px-4 py-3 min-w-[250px] border-r border-slate-200">Uraian</th>
                        <th class="px-4 py-3 w-64 border-r border-slate-200">Sub Output</th>
                        <th class="px-4 py-3 w-32 border-r border-slate-200 text-center">Satuan</th>
                        <th class="px-4 py-3 w-44 text-right border-r border-slate-200 bg-blue-50/20">Pagu Anggaran</th>
                        <th class="px-4 py-3 w-44 text-right border-r border-slate-200 bg-green-50/20">Realisasi</th>
                        <th class="px-4 py-3 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                
                {{-- LOOP DATA --}}
                @forelse($reportData as $progId => $group)
                    @php 
                        $program = $group['program'];
                    @endphp

                    {{-- TBODY PER PROGRAM --}}
                    <tbody wire:key="prog-{{ $program->id }}" x-data="{ expanded: true }" class="border-b border-gray-100 group transition-colors">
                        
                        {{-- === BARIS PROGRAM === --}}
                        <tr class="bg-blue-50/40 hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 font-bold text-gray-800 border-r border-blue-100 font-mono text-center align-top">
                                <button @click="expanded = !expanded" class="mr-2 text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-90': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                {{ $program->kode }}
                            </td>
                            <td class="px-4 py-3 border-r border-blue-100 align-top">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 mt-0.5">PROGRAM</span>
                                    <span class="font-bold text-gray-800">{{ $program->nama ?? $program->nama_program }}</span>
                                </div>
                            </td>
                            <td colspan="2" class="bg-gray-50/30 border-r border-gray-100"></td>

                            {{-- PROGRAM INPUTS --}}
                            <td class="p-2 border-r border-gray-100 align-top bg-blue-50/30" x-data="rupiahInput('programInputs.{{ $program->id }}.pagu_anggaran', '{{ $programInputs[$program->id]['pagu_anggaran'] ?? 0 }}')">
                                <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-xs text-right font-bold text-blue-800 bg-transparent border-b border-blue-200 focus:border-blue-600 focus:ring-0 px-2 py-2" placeholder="Rp 0">
                            </td>
                            <td class="p-2 border-r border-gray-100 align-top bg-green-50/30" x-data="rupiahInput('programInputs.{{ $program->id }}.pagu_realisasi', '{{ $programInputs[$program->id]['pagu_realisasi'] ?? 0 }}')">
                                <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-xs text-right font-bold text-green-800 bg-transparent border-b border-green-200 focus:border-green-600 focus:ring-0 px-2 py-2" placeholder="Rp 0">
                            </td>

                            {{-- AKSI PROGRAM --}}
                            <td class="p-2 text-center align-middle relative">
                                <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                    <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-2 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                        Menu <svg class="-mr-1 ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </button>
                                    <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100">
                                        <div class="py-1">
                                            <button wire:click="createKegiatan({{ $program->id }})" @click="open = false" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-green-50 hover:text-green-700 w-full text-left">
                                                <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                Tambah Kegiatan
                                            </button>
                                        </div>
                                        <div class="py-1">
                                            <button wire:click="editProgram({{ $program->id }})" @click="open = false" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-700 w-full text-left">
                                                <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit Program
                                            </button>
                                            <button wire:click="deleteProgram({{ $program->id }})" wire:confirm="Yakin hapus program ini? Semua kegiatan didalamnya akan ikut terhapus." @click="open = false" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-red-50 hover:text-red-700 w-full text-left">
                                                <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus Program
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- === LOOP KEGIATAN & SUB === --}}
                        @foreach($group['kegiatans'] as $kegData)
                            @php 
                                $kegiatan = $kegData['kegiatan'];
                                $details  = $kegData['details'];
                            @endphp

                            {{-- BARIS KEGIATAN --}}
                            <tr x-show="expanded" x-transition class="bg-white border-b border-gray-100 hover:bg-amber-50 transition-colors">
                                <td class="px-4 py-2 font-semibold text-gray-600 border-r border-gray-100 font-mono text-center align-top pl-8">
                                    {{ $kegiatan->kode }}
                                </td>
                                <td class="px-4 py-2 border-r border-gray-100 align-top pl-8">
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200 mt-0.5">KEGIATAN</span>
                                        <span class="font-semibold text-gray-700">{{ $kegiatan->nama ?? $kegiatan->nama_kegiatan }}</span>
                                    </div>
                                </td>
                                <td colspan="2" class="border-r border-gray-100 bg-gray-50/10"></td>

                                {{-- KEGIATAN INPUTS --}}
                                <td class="p-2 border-r border-gray-100 align-top bg-amber-50/20" x-data="rupiahInput('kegiatanInputs.{{ $kegiatan->id }}.pagu_anggaran', '{{ $kegiatanInputs[$kegiatan->id]['pagu_anggaran'] ?? 0 }}')">
                                    <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-xs text-right font-semibold text-gray-700 bg-transparent border-b border-gray-300 focus:border-amber-500 focus:ring-0 px-2 py-2" placeholder="Rp 0">
                                </td>
                                <td class="p-2 border-r border-gray-100 align-top bg-green-50/10" x-data="rupiahInput('kegiatanInputs.{{ $kegiatan->id }}.pagu_realisasi', '{{ $kegiatanInputs[$kegiatan->id]['pagu_realisasi'] ?? 0 }}')">
                                    <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-xs text-right font-semibold text-gray-700 bg-transparent border-b border-gray-300 focus:border-green-500 focus:ring-0 px-2 py-2" placeholder="Rp 0">
                                </td>

                                {{-- AKSI KEGIATAN --}}
                                <td class="p-2 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-200 shadow-sm px-2 py-1 bg-white text-[11px] font-medium text-gray-600 hover:bg-gray-50 focus:outline-none">
                                            Menu <svg class="-mr-1 ml-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100">
                                            {{-- PERUBAHAN: Tombol Tambah Sub Kegiatan DIHAPUS --}}
                                            <div class="py-1">
                                                <button wire:click="editKegiatan({{ $kegiatan->id }})" @click="open = false" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-700 w-full text-left">
                                                    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Edit Kegiatan
                                                </button>
                                                <button wire:click="deleteKegiatan({{ $kegiatan->id }})" wire:confirm="Yakin hapus Kegiatan ini? Sub kegiatan didalamnya akan ikut terhapus." @click="open = false" class="group flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-red-50 hover:text-red-700 w-full text-left">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus Kegiatan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- BARIS SUB KEGIATAN --}}
                            @foreach($details as $detail)
                                <tr x-show="expanded" x-transition class="hover:bg-yellow-50 transition-colors group border-b border-gray-100 bg-gray-50/30">
                                    {{-- Kode --}}
                                    <td class="px-4 py-2.5 text-center font-mono text-xs text-gray-500 border-r border-gray-100 align-top pt-4">
                                        {{ $detail->kode }}
                                    </td>
                                    
                                    {{-- Nama Sub Kegiatan --}}
                                    <td class="px-4 py-2.5 border-r border-gray-100 pl-16 align-top pt-4">
                                        <div class="flex items-start gap-3">
                                            <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200 mt-0.5 uppercase">SUB</span>
                                            <div class="text-gray-600 leading-snug text-sm font-medium">
                                                {{ $detail->subKegiatan?->nama ?? 'Sub Kegiatan Tidak Ditemukan' }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- KOLOM SUB OUTPUT (BERISI INDIKATOR KETERANGAN - READ ONLY) --}}
                                    <td class="p-2 border-r border-gray-100 align-top">
                                        @if($detail->subKegiatan && $detail->subKegiatan->indikators->isNotEmpty())
                                            <div class="space-y-2">
                                                @foreach($detail->subKegiatan->indikators as $indikator)
                                                    <div class="text-xs text-gray-600 bg-white p-2 rounded border border-gray-200 shadow-sm leading-tight min-h-[38px] flex items-center">
                                                        {{ $indikator->keterangan }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-400 italic p-2 text-center">-</div>
                                        @endif
                                    </td>
                                    
                                    {{-- KOLOM SATUAN (BERISI INDIKATOR SATUAN - READ ONLY) --}}
                                    <td class="p-2 border-r border-gray-100 align-top">
                                        @if($detail->subKegiatan && $detail->subKegiatan->indikators->isNotEmpty())
                                            <div class="space-y-2">
                                                @foreach($detail->subKegiatan->indikators as $indikator)
                                                    <div class="text-xs text-gray-600 bg-white p-2 rounded border border-gray-200 shadow-sm text-center leading-tight min-h-[38px] flex items-center justify-center">
                                                        {{ $indikator->satuan }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-400 italic p-2 text-center">-</div>
                                        @endif
                                    </td>
                                    
                                    {{-- Input Anggaran --}}
                                    <td class="p-2 border-r border-gray-100 align-top bg-blue-50/5" x-data="rupiahInput('inputs.{{ $detail->id }}.pagu_anggaran', '{{ $inputs[$detail->id]['pagu_anggaran'] ?? 0 }}')">
                                        <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-xs text-right font-medium text-gray-800 border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-md px-2 py-2" placeholder="Rp 0">
                                    </td>
                                    
                                    {{-- Input Realisasi --}}
                                    <td class="p-2 border-r border-gray-100 align-top bg-green-50/5" x-data="rupiahInput('inputs.{{ $detail->id }}.pagu_realisasi', '{{ $inputs[$detail->id]['pagu_realisasi'] ?? 0 }}')">
                                        <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-xs text-right font-bold text-green-700 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 rounded-md px-2 py-2" placeholder="Rp 0">
                                    </td>
                                    
                                    {{-- AKSI SUB KEGIATAN --}}
                                    <td class="p-2 text-center align-middle relative">
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                                    <button wire:click="deleteSubKegiatan({{ $detail->id }})" wire:confirm="Yakin hapus data ini?" @click="open = false" class="group flex items-center px-4 py-2 text-xs text-gray-600 hover:bg-red-50 hover:text-red-700 w-full text-left">
                                                        <svg class="mr-2 h-3.5 w-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400 bg-gray-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                    </div>
                                    <span class="font-medium text-gray-600">Belum ada program ditambahkan.</span>
                                    <span class="text-sm text-gray-400 mt-1">Silakan klik tombol "Tambah Program" di atas.</span>
                                </div>
                            </td>
                        </tr>
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

    {{-- SCRIPT & MODAL (TETAP SAMA) --}}
    <script>
        document.addEventListener('alpine:init', () => {
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
        });
    </script>

    {{-- MODAL TAMBAH PROGRAM (TETAP SAMA) --}}
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

    {{-- MODAL TAMBAH KEGIATAN (TETAP SAMA) --}}
    @if($isOpenKegiatan)
    <div class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" wire:click="closeKegiatanModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Kegiatan</h3>
                    <p class="text-sm text-gray-500 mt-1">Pilih kegiatan untuk ditambahkan ke program ini.</p>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kegiatan</label>
                    <select wire:model="selectedKegiatanId" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                        <option value="">-- Pilih Kegiatan --</option>
                        @foreach($kegiatanOptions as $keg)
                            <option value="{{ $keg->id }}">{{ $keg->kode }} - {{ $keg->nama ?? $keg->nama_kegiatan }}</option>
                        @endforeach
                    </select>
                    @error('selectedKegiatanId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button wire:click="addKegiatan" class="inline-flex justify-center items-center rounded-lg shadow-sm px-4 py-2 bg-green-600 text-sm font-bold text-white hover:bg-green-700 focus:outline-none transition-all">Tambahkan</button>
                    <button wire:click="closeKegiatanModal" class="inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL EDIT SUB KEGIATAN (BARU) --}}
    @if($isOpenEditSub)
    <div class="fixed inset-0 z-[101] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" wire:click="closeEditSubModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Edit Sub Kegiatan</h3>
                    <p class="text-sm text-gray-500 mt-1">Ubah data sub kegiatan.</p>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Dropdown Ganti Sub Kegiatan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sub Kegiatan</label>
                        <select wire:model="editSubKegiatanId" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                            <option value="">-- Pilih Sub Kegiatan --</option>
                            @foreach($subKegiatanOptions as $subOption)
                                <option value="{{ $subOption->id }}">{{ $subOption->kode }} - {{ $subOption->nama }}</option>
                            @endforeach
                        </select>
                        @error('editSubKegiatanId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Note: Input Sub Output dan Satuan dihapus dari modal karena sudah otomatis dari indikator --}}
                    <div class="bg-blue-50 text-blue-700 p-3 rounded text-xs border border-blue-100">
                        <span class="font-bold">Info:</span> Sub Output dan Satuan diambil otomatis dari Indikator Kinerja Master Data.
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button wire:click="updateSubKegiatan" class="inline-flex justify-center items-center rounded-lg shadow-sm px-4 py-2 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none transition-all">Simpan Perubahan</button>
                    <button wire:click="closeEditSubModal" class="inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>