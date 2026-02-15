<div class="space-y-6"

    x-data="{ 
        // MENGGUNAKAN ENTANGLE AGAR SINKRON DENGAN LIVEWIRE/DATABASE
        totalAnggaran: @entangle('totalAnggaran'), 
        totalRealisasi: @entangle('totalRealisasi'),
        totalPersentase: 0, // TAMBAHAN BARU UNTUK PERSENTASE
        
        // Fungsi untuk membersihkan format Rupiah jadi float
        parseRupiah(value) {
            if (!value) return 0;
            if (typeof value === 'number') return value;
            return parseFloat(value.replace(/[^0-9]/g, '')) || 0;
        },

        // Fungsi format angka jadi Rupiah
        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        },

        // Update Total saat input berubah
        updateTotals() {
            let totalAng = 0;
            let totalReal = 0;

            // Cari semua input dengan class spesifik
            document.querySelectorAll('.input-pagu').forEach(el => {
                totalAng += this.parseRupiah(el.value);
            });

            document.querySelectorAll('.input-realisasi').forEach(el => {
                totalReal += this.parseRupiah(el.value);
            });

            this.totalAnggaran = totalAng;
            this.totalRealisasi = totalReal;

            // RUMUS PERSENTASE (Realisasi / Anggaran x 100)
            if (totalAng > 0) {
                this.totalPersentase = (totalReal / totalAng) * 100;
                // Pastikan mentok di 100% jika realisasi melebihi anggaran (opsional)
                if(this.totalPersentase > 100) this.totalPersentase = 100; 
            } else {
                this.totalPersentase = 0;
            }
        }
     }"
    {{-- MENGGUNAKAN NEXTTICK AGAR DIHITUNG SETELAH DOM SIAP --}}
    x-init="$nextTick(() => { updateTotals() })"
    @input.debounce.500ms="updateTotals()">
    <x-slot:title>
        Laporan e-monev
        </x-slot>
     <x-slot:breadcrumb>
            <div class="overflow-x-auto whitespace-nowrap pb-2">
                <a href="/" class="hover:text-blue-100 transition-colors">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="font-medium text-white">Laporan e-monev</span>
            </div>
            </x-slot>
    {{-- HELPER PHP UNTUK RUMUS DI VIEW --}}
    @php
        // Helper Parsing Angka (PENTING UNTUK KONSISTENSI)
        $parseNum = function($val) {
            if(is_int($val) || is_float($val)) return (float)$val;
            
            $strVal = (string)($val ?? '0');
            $strVal = str_replace('.', '', $strVal); // Hapus ribuan
            $strVal = str_replace(',', '.', $strVal); // Ubah koma jadi titik
            $clean = preg_replace('/[^0-9\.]/', '', $strVal);
            
            return (float) ($clean ?: 0);
        };

        $hitungPersen = function($pembilang, $penyebut) use ($parseNum) {
            $a = $parseNum($pembilang);
            $b = $parseNum($penyebut);
            $hasil = ($b > 0) ? ($a / $b) * 100 : 0;
            return min($hasil, 100);
        };
        
        // Helper format angka bersih (100.00 -> 100)
        $formatClean = function($val) {
            return (float)number_format($val, 2);
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

        <div class="flex gap-4 md:gap-6 items-center bg-gray-50 px-5 py-3 rounded-xl border border-gray-200 shadow-inner">
            <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Total Anggaran</p>
                <p class="text-lg font-bold text-gray-800">Rp <span x-text="formatRupiah(totalAnggaran)">0</span></p>
            </div>
            <div class="w-px h-10 bg-gray-300"></div>
            <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Total Realisasi</p>
                <p class="text-lg font-bold text-green-600">Rp <span x-text="formatRupiah(totalRealisasi)">0</span></p>
            </div>
            <div class="w-px h-10 bg-gray-300"></div>
            <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Total Persentase%</p>
                <p class="text-lg font-bold text-blue-600"><span x-text="totalPersentase.toFixed(1)">0</span>%</p>
            </div>
        </div>
    </div>

    {{-- MESSAGE --}}
    @if (session()->has('message'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm animate-fade-in-down">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ session('message') }}
    </div>
    @endif

    {{-- TOOLBAR --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <h3 class="text-lg font-bold text-gray-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Rincian Matrik
            </h3>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="openPrintModal" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95" title="Cetak Laporan PDF">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak PDF
            </button>

            {{-- HANYA ADMIN YANG BISA SINKRON & TAMBAH DATA --}}
            @if(auth()->user()->role == 'admin')
            <button wire:click="syncData" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed" title="Tarik data baru dari Master Data">
                <svg wire:loading.remove wire:target="syncData" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <svg wire:loading wire:target="syncData" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sinkron
            </button>

            <button wire:click="openProgramModal" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah
            </button>
            @endif

            {{-- TOMBOL SIMPAN (VERIFIKATOR TIDAK PERLU TOMBOL SIMPAN, TAPI BISA DIBIARKAN JIKA INGIN) --}}
            @if(auth()->user()->role != 'verifikator')
            <button wire:click="saveAll" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading.remove wire:target="saveAll" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                <svg wire:loading wire:target="saveAll" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Simpan
            </button>
            @endif
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

                        {{-- KOLOM BARU: SISA ANGGARAN --}}
                        <th rowspan="2" class="px-2 py-3 w-32 text-center border-r border-slate-200 align-middle">Sisa<br>Anggaran</th>

                        <th rowspan="2" class="px-2 py-3 w-24 text-center align-middle">Aksi</th>
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
                $isAdmin = auth()->user()->role == 'admin';

                // --- LOGIKA BARU: HITUNG RATA-RATA PERSENTASE CAPAIAN FISIK PROGRAM (SYNC DENGAN PDF) ---
                $totalPersenFisikSub_Prog = 0;
                $jumlahSub_Prog = 0;

                // Loop tembus ke dalam kegiatan -> sub kegiatan
                foreach($group['kegiatans'] as $kegLoop) {
                    foreach($kegLoop['details'] as $detailLoop) {
                        // GUNAKAN $parseNum UNTUK MEMASTIKAN ANGKA DARI INPUT TERBACA BENAR (MISAL 2,5)
                        $targetSub = $parseNum($inputs[$detailLoop->id]['target'] ?? 0);
                        $realisasiSub = $parseNum($inputs[$detailLoop->id]['realisasi_fisik'] ?? 0);
                        
                        // Hitung Persentase per Sub Kegiatan
                        $persenSub = ($targetSub > 0) ? ($realisasiSub / $targetSub) * 100 : 0;
                        $persenSub = min($persenSub, 100); // Batasi maks 100%

                        $totalPersenFisikSub_Prog += $persenSub;
                        $jumlahSub_Prog++;
                    }
                }
                // Rumus Rata-rata Persentase Program
                $rataFisikProg = $jumlahSub_Prog > 0 ? $totalPersenFisikSub_Prog / $jumlahSub_Prog : 0;
                // -----------------------------------------------------------------------------------

                // INPUT PROGRAM
                $progTarget = $programInputs[$program->id]['target'] ?? 0;
                $progPagu = $programInputs[$program->id]['pagu_anggaran'] ?? 0;
                $progRealisasi = $programInputs[$program->id]['pagu_realisasi'] ?? 0;

                // RUMUS PROGRAM
                $persenKeuProg = $hitungPersen($progRealisasi, $progPagu);
                $persenFisikProg = $rataFisikProg; // MENGGUNAKAN RATA-RATA PERSENTASE

                // HITUNG SISA PROGRAM
                $sisaProg = $parseNum($progPagu) - $parseNum($progRealisasi);
                @endphp

                <tbody wire:key="prog-{{ $program->id }}" x-data="{ expanded: true }" class="border-b border-gray-100 group">

                    {{-- === BARIS PROGRAM === --}}
                    <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                        <td class="px-2 py-3 font-bold text-gray-800 border-r border-gray-200 font-mono text-center align-top">
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
                        <td class="p-1.5 border-r border-gray-200 align-middle text-center">
                            <span class="text-gray-400 font-bold text-[14px]">-</span>
                        </td>

                        {{-- Pagu Anggaran Program --}}
                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('programInputs.{{ $program->id }}.pagu_anggaran', '{{ $progPagu }}')">
                            <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire"
                                class="w-full text-[11px] text-right font-bold text-blue-800 bg-gray-100 border border-gray-300 rounded shadow-sm px-2 py-1.5 disabled:text-gray-500 cursor-not-allowed"
                                placeholder="0"
                                disabled title="Dihitung otomatis dari Sub Kegiatan">
                        </td>

                        {{-- Realisasi Keuangan Program --}}
                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('programInputs.{{ $program->id }}.pagu_realisasi', '{{ $progRealisasi }}')">
                            <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire"
                                class="w-full text-[11px] text-right font-bold text-green-800 bg-gray-100 border border-gray-300 rounded shadow-sm px-2 py-1.5 disabled:text-gray-500 cursor-not-allowed"
                                placeholder="0"
                                disabled title="Dihitung otomatis dari Sub Kegiatan">
                        </td>

                        {{-- Realisasi Fisik Program (KEMBALI KE STRIP) --}}
                        <td class="p-1.5 border-r border-gray-200 align-middle text-center">
                            <span class="text-gray-400 font-bold text-[14px]">-</span>
                        </td>

                        {{-- % Capaian Keu --}}
                        <td class="text-center text-[10px] font-bold text-gray-600 border-r border-gray-200 align-middle">{{ number_format($persenKeuProg, 0) }}%</td>

                        {{-- % Capaian Fisik (RATA-RATA PERSENTASE) --}}
                        <td class="text-center text-[10px] font-bold text-gray-600 border-r border-gray-200 align-middle">
                            <span class="text-gray-800">{{ $formatClean($persenFisikProg) }}%</span>
                        </td>

                        {{-- SISA ANGGARAN PROGRAM --}}
                        <td class="px-2 py-2 text-right font-bold text-gray-800 border-r border-gray-200 align-middle text-[11px]">
                            Rp {{ number_format($sisaProg, 0, ',', '.') }}
                        </td>

                        {{-- AKSI --}}
                        <td class="p-2 text-center align-middle whitespace-nowrap">
                            @if($isAdmin)
                            <button wire:click="deleteProgram({{ $program->id }})" wire:confirm="Yakin hapus Program beserta seluruh Kegiatan dan Sub Kegiatannya?" class="text-red-400 hover:text-red-600 p-1" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>

                    {{-- === LOOP KEGIATAN === --}}
                    @foreach($group['kegiatans'] as $kegData)
                    @php
                    $kegiatan = $kegData['kegiatan'];
                    $details = $kegData['details'];

                    // --- LOGIKA BARU: HITUNG RATA-RATA PERSENTASE FISIK KEGIATAN (SYNC DENGAN PDF) ---
                    $totalPersenFisikSub_Keg = 0;
                    $jumlahSub_Keg = 0;

                    foreach($details as $detailLoop) {
                        // GUNAKAN $parseNum DISINI JUGA
                        $targetSub = $parseNum($inputs[$detailLoop->id]['target'] ?? 0);
                        $realisasiSub = $parseNum($inputs[$detailLoop->id]['realisasi_fisik'] ?? 0);
                        
                        // Hitung Persentase per Sub Kegiatan
                        $persenSub = ($targetSub > 0) ? ($realisasiSub / $targetSub) * 100 : 0;
                        $persenSub = min($persenSub, 100);

                        $totalPersenFisikSub_Keg += $persenSub;
                        $jumlahSub_Keg++;
                    }
                    // Rumus Rata-rata Kegiatan
                    $rataFisikKeg = $jumlahSub_Keg > 0 ? $totalPersenFisikSub_Keg / $jumlahSub_Keg : 0;
                    // -----------------------------------------------------------------------------------

                    $kegTarget = $kegiatanInputs[$kegiatan->id]['target'] ?? 0;
                    $kegPagu = $kegiatanInputs[$kegiatan->id]['pagu_anggaran'] ?? 0;
                    $kegRealisasi = $kegiatanInputs[$kegiatan->id]['pagu_realisasi'] ?? 0;

                    $persenKeuKeg = $hitungPersen($kegRealisasi, $kegPagu);
                    $persenFisikKeg = $rataFisikKeg; // MENGGUNAKAN RATA-RATA PERSENTASE

                    // HITUNG SISA KEGIATAN
                    $sisaKeg = $parseNum($kegPagu) - $parseNum($kegRealisasi);
                    @endphp

                    <tr x-show="expanded" x-transition class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-2 py-2 font-semibold text-gray-600 border-r border-gray-200 font-mono text-center align-top text-[11px]">{{ $kegiatan->kode }}</td>
                        <td class="px-4 py-2 border-r border-gray-200 align-top text-left">
                            <div class="flex items-start gap-2">
                                <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 mt-0.5">KEG</span>
                                <span class="font-semibold text-gray-700 text-xs">{{ $kegiatan->nama ?? $kegiatan->nama_kegiatan }}</span>
                            </div>
                        </td>
                        <td colspan="2" class="border-r border-gray-200"></td>

                        {{-- Target Kegiatan --}}
                        <td class="p-1.5 border-r border-gray-200 align-middle text-center">
                            <span class="text-gray-400 font-bold text-[14px]">-</span>
                        </td>

                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('kegiatanInputs.{{ $kegiatan->id }}.pagu_anggaran', '{{ $kegPagu }}')">
                            <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" 
                                class="w-full text-[11px] text-right font-semibold text-gray-700 bg-gray-100 border border-gray-300 rounded shadow-sm px-2 py-1.5 disabled:text-gray-500 cursor-not-allowed" 
                                placeholder="0" 
                                disabled title="Dihitung otomatis dari Sub Kegiatan">
                        </td>

                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('kegiatanInputs.{{ $kegiatan->id }}.pagu_realisasi', '{{ $kegRealisasi }}')">
                            <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" 
                                class="w-full text-[11px] text-right font-semibold text-gray-700 bg-gray-100 border border-gray-300 rounded shadow-sm px-2 py-1.5 disabled:text-gray-500 cursor-not-allowed" 
                                placeholder="0" 
                                disabled title="Dihitung otomatis dari Sub Kegiatan">
                        </td>

                        {{-- Realisasi Fisik Kegiatan (KEMBALI KE STRIP) --}}
                        <td class="p-1.5 border-r border-gray-200 align-middle text-center">
                            <span class="text-gray-400 font-bold text-[14px]">-</span>
                        </td>

                        {{-- % Capaian Keu --}}
                        <td class="text-center text-[10px] font-medium text-gray-600 border-r border-gray-200 align-middle">{{ number_format($persenKeuKeg, 0) }}%</td>

                        {{-- % Capaian Fisik (RATA-RATA PERSENTASE) --}}
                        <td class="text-center text-[10px] font-medium text-gray-600 border-r border-gray-200 align-middle">
                            <span class="text-gray-700">{{ $formatClean($persenFisikKeg) }}%</span>
                        </td>

                        {{-- SISA ANGGARAN KEGIATAN --}}
                        <td class="px-2 py-2 text-right font-semibold text-gray-700 border-r border-gray-200 align-middle text-[11px]">
                            Rp {{ number_format($sisaKeg, 0, ',', '.') }}
                        </td>

                        <td class="p-2 text-center align-middle whitespace-nowrap">
                            @if($isAdmin)
                            <button wire:click="deleteKegiatan({{ $kegiatan->id }})" wire:confirm="Yakin hapus Kegiatan beserta Sub Kegiatannya?" class="text-red-400 hover:text-red-600 p-1" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>

                    {{-- === BARIS SUB KEGIATAN === --}}
                    @foreach($details as $detail)
                    @php
                    $subTarget = $inputs[$detail->id]['target'] ?? 0;
                    $subPagu = $inputs[$detail->id]['pagu_anggaran'] ?? 0;
                    $subRealisasiKeu = $inputs[$detail->id]['pagu_realisasi'] ?? 0;
                    $subRealisasiFisik = $inputs[$detail->id]['realisasi_fisik'] ?? 0;

                    $persenKeuSub = $hitungPersen($subRealisasiKeu, $subPagu);
                    $persenFisikSub = $hitungPersen($subRealisasiFisik, $subTarget);

                    // HITUNG SISA SUB KEGIATAN
                    $sisaSub = $parseNum($subPagu) - $parseNum($subRealisasiKeu);

                    // --- LOGIKA PERMISSION USER DI-UPDATE DISINI ---
                    $currentUser = auth()->user();
                    $isPj = false;
                    $userJabatanId = $currentUser->jabatan_id ?? $currentUser->pegawai?->jabatan_id;
                    $subJabatanId = $detail->subKegiatan?->jabatan_id;

                    if ($userJabatanId && $subJabatanId && $userJabatanId == $subJabatanId) {
                        $isPj = true;
                    }
                    
                    // Cek Role
                    $isAdmin = $currentUser->role == 'admin';
                    $isVerifikator = $currentUser->role == 'verifikator'; 

                    // LOGIKA AKSES INPUT (Verifikator TIDAK BOLEH edit angka)
                    // Jika Verifikator, maka $canEditRealisasi jadi false
                    $canEditRealisasi = ($isAdmin || $isPj) && !$isVerifikator;

                    // LOGIKA AKSES TOMBOL CENTANG (Hanya Admin dan Verifikator yang boleh)
                    $canVerify = $isAdmin || $isVerifikator;
                    @endphp

                    <tr x-show="expanded" x-transition class="hover:bg-gray-50 transition-colors group border-b border-gray-100 bg-white">
                        <td class="px-2 py-2 text-center font-mono text-[10px] text-gray-500 border-r border-gray-200 align-top pt-3">{{ $detail->kode }}</td>
                        <td class="px-4 py-2 border-r border-gray-200 pl-8 align-top pt-3 text-left">
                            <div class="flex items-start gap-2">
                                <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-700 border border-purple-200 mt-0.5">SUB</span>
                                <div class="text-gray-600 leading-snug text-xs font-medium">
                                    {{ $detail->subKegiatan?->nama ?? 'Sub Kegiatan' }}
                                    @if($isPj && !$isAdmin)
                                    <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-green-100 text-green-700 border border-green-200">Tanggung Jawab Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top text-[10px] text-gray-600 text-left">{!! nl2br(e($detail->sub_output ?? '-')) !!}</td>
                        <td class="px-2 py-2 border-r border-gray-200 align-top text-center text-[10px] text-gray-600">{!! nl2br(e($detail->satuan_unit ?? '-')) !!}</td>

                        {{-- TARGET SUB KEGIATAN (DISABLED) --}}
                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('inputs.{{ $detail->id }}.target', '{{ $subTarget }}')">
                            <input type="text" 
                                x-model="displayValue" 
                                class="w-full text-[11px] text-center font-bold text-gray-500 bg-gray-100 border border-gray-300 rounded shadow-sm px-1 py-1.5 cursor-not-allowed" 
                                placeholder="" 
                                disabled 
                                title="Target terkunci (Read Only)">
                        </td>

                        {{-- PAGU ANGGARAN SUB KEGIATAN (DISABLED) --}}
                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('inputs.{{ $detail->id }}.pagu_anggaran', '{{ $subPagu }}')">
                            <input type="text" 
                                x-model="displayValue" 
                                class="input-pagu w-full text-[11px] text-right font-bold text-gray-500 bg-gray-100 border border-gray-300 rounded shadow-sm px-2 py-1.5 cursor-not-allowed" 
                                placeholder="Rp 0" 
                                disabled 
                                title="Pagu Anggaran terkunci (Read Only)">
                        </td>

                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="rupiahInput('inputs.{{ $detail->id }}.pagu_realisasi', '{{ $subRealisasiKeu }}')">
                            <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="input-realisasi w-full text-[11px] text-right font-bold text-green-700 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed" placeholder="Rp 0" @if(!$canEditRealisasi) disabled @endif>
                        </td>

                        <td class="p-1.5 border-r border-gray-200 align-top" x-data="numberInput('inputs.{{ $detail->id }}.realisasi_fisik', '{{ $subRealisasiFisik }}')">
                            <input type="text" x-model="displayValue" @input="updateWire" @blur="updateWire" class="w-full text-[11px] text-right font-medium text-gray-800 bg-white border border-gray-300 rounded shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 px-2 py-1.5 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed" placeholder="" @if(!$canEditRealisasi) disabled @endif>
                        </td>

                        <td class="px-2 py-2 text-center text-[10px] text-gray-600 border-r border-gray-200 align-middle">{{ number_format($persenKeuSub, 0) }}%</td>
                        <td class="px-2 py-2 text-center text-[10px] text-gray-600 border-r border-gray-200 align-middle">{{ number_format($persenFisikSub, 0) }}%</td>

                        {{-- SISA ANGGARAN SUB KEGIATAN --}}
                        <td class="px-2 py-2 text-right text-[10px] font-medium text-gray-600 border-r border-gray-200 align-middle">
                            Rp {{ number_format($sisaSub, 0, ',', '.') }}
                        </td>

                        <td class="p-1 text-center align-middle whitespace-nowrap">
                            @php
                            $isVerifiedSub = \App\Models\DetailLaporanKonsolidasi::where('id', $detail->id)->value('is_verified');
                            @endphp
                            
                            <div class="flex items-center justify-center gap-1">
                                
                                {{-- KONDISI 1: JIKA USER ADALAH VERIFIKATOR ATAU ADMIN --}}
                                @if($isAdmin || $isVerifikator)
                                    <button wire:click="toggleVerification({{ $detail->id }}, 'sub_kegiatan')" 
                                        class="transition-all active:scale-95 focus:outline-none" 
                                        title="{{ $isVerifiedSub ? 'Batalkan Verifikasi' : 'Verifikasi Data' }}">
                                        
                                        @if($isVerifiedSub)
                                            {{-- Icon Terverifikasi (Hijau dengan Centang) --}}
                                            <div class="bg-green-100 text-green-600 border border-green-200 p-1.5 rounded-md shadow-sm hover:bg-green-200 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @else
                                            {{-- Icon Belum Verifikasi (Putih/Abu) --}}
                                            <div class="bg-white text-gray-300 border border-gray-200 p-1.5 rounded-md shadow-sm hover:bg-blue-50 hover:text-blue-500 hover:border-blue-300 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </button>
                                
                                {{-- KONDISI 2: JIKA USER ADALAH PEGAWAI (USER BIASA) --}}
                                @else
                                    @if($isVerifiedSub)
                                        {{-- Badge Status: SUDAH DIVERIFIKASI --}}
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Terverifikasi
                                        </span>
                                    @else
                                        {{-- Badge Status: BELUM DIVERIFIKASI --}}
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Belum Verif
                                        </span>
                                    @endif
                                @endif

                                {{-- TOMBOL HAPUS (HANYA ADMIN) --}}
                                @if($isAdmin)
                                <button wire:click="deleteSubKegiatan({{ $detail->id }})" wire:confirm="Hapus sub kegiatan ini?" class="ml-1 text-gray-300 hover:text-red-500 p-1 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
                @empty
                <tbody>
                    <tr>
                        <td colspan="12" class="text-center py-10 text-gray-400">Belum ada data</td>
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

    {{-- Script Alpine: UPDATE BESAR DI SINI AGAR SINKRON DENGAN LIVEWIRE SAAT SAVE --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('rupiahInput', (modelName, initialValue) => ({
                displayValue: '',
                modelName: modelName,
                init() {
                    // Set nilai awal saat pertama render
                    this.formatInitial(initialValue);
                    
                    // SINKRONISASI: Dengarkan perubahan langsung dari backend Livewire (misal setelah klik Simpan)
                    if (this.$wire) {
                        // Untuk Livewire v3
                        if (typeof this.$wire.$watch === 'function') {
                            this.$wire.$watch(this.modelName, (value) => {
                                let currentRaw = this.displayValue.toString().replace(/[^0-9]/g, '');
                                let newRaw = value ? value.toString().replace(/[^0-9]/g, '') : '';
                                // Update form field jika nilainya benar-benar berubah dari backend
                                if (currentRaw !== newRaw) {
                                    this.formatInitial(value);
                                }
                            });
                        } 
                        // Fallback jika menggunakan Livewire v2
                        else {
                            this.$watch('$wire.' + this.modelName, (value) => {
                                let currentRaw = this.displayValue.toString().replace(/[^0-9]/g, '');
                                let newRaw = value ? value.toString().replace(/[^0-9]/g, '') : '';
                                if (currentRaw !== newRaw) {
                                    this.formatInitial(value);
                                }
                            });
                        }
                    }
                },
                updateWire(e) {
                    this.formatCurrency(e);
                    this.$wire.set(this.modelName, this.displayValue);
                },
                formatCurrency(e) {
                    let inputVal = e.target.value;
                    let numberString = inputVal.replace(/[^,\d]/g, '').toString();
                    let split = numberString.split(',');
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    this.displayValue = rupiah ? 'Rp ' + rupiah : '';
                },
                formatInitial(val) {
                    if (!val) {
                        this.displayValue = '';
                        return;
                    }
                    let stringVal = val.toString().replace(/[^0-9]/g, '');
                    if (!stringVal || stringVal == '0') {
                        this.displayValue = '';
                        return;
                    }
                    let sisa = stringVal.length % 3;
                    let rupiah = stringVal.substr(0, sisa);
                    let ribuan = stringVal.substr(sisa).match(/\d{3}/gi);
                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    this.displayValue = 'Rp ' + rupiah;
                }
            }));

            Alpine.data('numberInput', (modelName, initialValue) => ({
                displayValue: '',
                modelName: modelName,
                init() {
                    // Set nilai awal
                    this.setInitial(initialValue);
                    
                    // SINKRONISASI: Dengarkan perubahan langsung dari backend Livewire
                    if (this.$wire) {
                        if (typeof this.$wire.$watch === 'function') {
                            this.$wire.$watch(this.modelName, (value) => {
                                if (this.displayValue != value) {
                                    this.setInitial(value);
                                }
                            });
                        } else {
                            this.$watch('$wire.' + this.modelName, (value) => {
                                if (this.displayValue != value) {
                                    this.setInitial(value);
                                }
                            });
                        }
                    }
                },
                setInitial(val) {
                    if (!val || val == 0 || val == '0') {
                        this.displayValue = '';
                    } else {
                        this.displayValue = val;
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

    {{-- MODAL CETAK PDF --}}
    @if($isOpenPrintModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" wire:click="closePrintModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-100 p-2 rounded-full">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Cetak Laporan</h3>
                    </div>
                    <p class="text-sm text-gray-500 mt-2 ml-11">Pilih filter jabatan untuk hasil cetak, atau biarkan kosong untuk mencetak semua.</p>
                </div>

                <div class="p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Berdasarkan Jabatan / Bidang</label>
                    <div class="relative">
                        <select wire:model="selectedJabatanPrint" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm py-2.5 px-3">
                            <option value="">-- Semua Jabatan (Keseluruhan) --</option>
                            @foreach($jabatans as $jab)
                            <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button wire:click="printLaporan"
                        class="inline-flex justify-center items-center rounded-lg shadow-sm px-4 py-2 bg-red-600 text-sm font-bold text-white hover:bg-red-700 focus:outline-none transition-all active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Cetak PDF
                    </button>
                    <button wire:click="closePrintModal"
                        class="inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>