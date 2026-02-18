<div>
    {{-- 1. Judul & Breadcrumb --}}
    <x-slot:title>Master Data Laporan</x-slot>

    <x-slot:breadcrumb>
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Laporan</span>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Master Data</span>
        </div>
    </x-slot>

    {{-- 2. Konten Utama --}}
    <div class="space-y-8 relative z-10 mt-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header & Filters --}}
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                
                {{-- JUDUL & FILTER TAHUN --}}
                <div class="flex flex-col gap-2">
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Laporan Master Data</h3>
                    
                    {{-- DROPDOWN TAHUN (BARU) --}}
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 font-medium">Tahun Anggaran:</span>
                        <div class="relative">
                            <select wire:model.live="tahun" class="appearance-none bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-24 p-2 pr-8 cursor-pointer shadow-sm hover:bg-blue-100 transition-colors">
                                @foreach($tahunOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GROUP TOMBOL KANAN --}}
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    
                    {{-- SHOW ENTRIES --}}
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                        <span>Show</span>
                        <select wire:model.live="perPage" class="border-gray-300 border-none bg-transparent text-sm focus:ring-0 font-bold py-0 pl-0 pr-6 cursor-pointer">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    {{-- TOMBOL TAMBAH PROGRAM --}}
                    @if(auth()->user()->role == 'admin')
                    <button wire:click="createProgram" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex justify-center items-center transition-colors shadow-sm h-[38px]">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Program
                    </button>
                    @endif
                </div>
            </div>

            {{-- Table Wrapper --}}
            <div class="p-4 sm:p-6 pb-2">
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px]">
                    <table class="w-full text-left border-collapse whitespace-nowrap sm:whitespace-normal">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 text-sm font-bold border-b border-gray-200">
                                <th class="p-4 border-r border-gray-200 align-middle">Program / Kegiatan / Sub Kegiatan</th>
                                <th class="p-4 align-middle text-center w-40">Pagu & Target</th>
                                <th class="p-4 align-middle text-center w-40">Aksi</th>
                            </tr>
                        </thead>

                        {{-- LOOPING DI LUAR TBODY --}}
                        @forelse($programs as $program)
                        <tbody x-data="{ expanded: true }" class="border-b border-gray-100 group transition-colors">

                            {{-- === BARIS PROGRAM === --}}
                            <tr class="bg-blue-50/40 hover:bg-blue-50 transition-colors">
                                <td class="p-4 border-r border-gray-100 align-top">
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 mt-1">PROGRAM</span>

                                        <div class="flex flex-col cursor-pointer select-none" @click="expanded = !expanded">
                                            <span class="font-bold text-gray-800 text-base">{{ $program->kode }}</span>
                                            <span class="text-gray-700 font-medium leading-relaxed">{{ $program->nama }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom Pagu (Otomatis Jumlah) --}}
                                <td class="p-4 border-r border-gray-100 text-right align-top">
                                    @php
                                    $totalPaguProgram = $program->kegiatans->sum(function($keg) {
                                        return $keg->subKegiatans->sum('pagu');
                                    });
                                    @endphp
                                    {{-- HANYA MENAMPILKAN PAGU --}}
                                    <div class="text-xs font-bold text-blue-700">Rp {{ number_format($totalPaguProgram, 0, ',', '.') }}</div>
                                </td>

                                <td class="p-4 text-center align-middle relative">
                                    {{-- HANYA ADMIN YANG BISA EDIT PROGRAM --}}
                                    @if(auth()->user()->role == 'admin')
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm transition-colors">
                                            Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 animate-fade-in-down">
                                            
                                            <div class="py-1">
                                                <button wire:click="createKegiatan({{ $program->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-green-600 hover:bg-green-50">
                                                    <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Tambah Kegiatan
                                                </button>
                                            </div>
                                            <div class="py-1">
                                                <button wire:click="editProgram({{ $program->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50">
                                                    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit Program
                                                </button>
                                                <button wire:click="confirmDelete({{ $program->id }}, 'program')" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus Program
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400 italic">View Only</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- === LOOP KEGIATAN === --}}
                            @foreach($program->kegiatans as $kegiatan)
                            <tr x-show="expanded" x-cloak x-transition class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-r border-gray-100 align-top">
                                    {{-- Indentasi Level 1 --}}
                                    <div class="flex items-start gap-3 pl-6 sm:pl-10 border-l-2 border-gray-100 ml-2">
                                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200 mt-1">KEGIATAN</span>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-800 text-sm">{{ $kegiatan->kode }}</span>
                                            <span class="text-gray-600 text-sm leading-relaxed">{{ $kegiatan->nama }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 border-r border-gray-100 text-right align-top">
                                    @php
                                    $totalPaguKegiatan = $kegiatan->subKegiatans->sum('pagu');
                                    @endphp
                                    {{-- HANYA MENAMPILKAN PAGU --}}
                                    <div class="text-xs font-bold text-amber-700">Rp {{ number_format($totalPaguKegiatan, 0, ',', '.') }}</div>
                                </td>

                                <td class="p-4 text-center align-middle relative">
                                    {{-- HANYA ADMIN YANG BISA EDIT KEGIATAN --}}
                                    @if(auth()->user()->role == 'admin')
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-600 hover:bg-gray-100 focus:outline-none shadow-sm transition-colors">
                                            Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 animate-fade-in-down">
                                            <div class="py-1">
                                                {{-- Tambah Sub Kegiatan (HIJAU) --}}
                                                <button wire:click="createSubKegiatan({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-green-600 hover:bg-green-50">
                                                    <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    Tambah Sub Kegiatan
                                                </button>
                                            </div>
                                            <div class="py-1">
                                                <button wire:click="editKegiatan({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50">
                                                    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit Kegiatan
                                                </button>
                                                <button wire:click="confirmDelete({{ $kegiatan->id }}, 'kegiatan')" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus Kegiatan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400 italic">View Only</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- === LOOP SUB KEGIATAN === --}}
                            @foreach($kegiatan->subKegiatans as $sub)
                            <tr x-show="expanded" x-cloak x-transition class="bg-gray-50/50 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="p-4 border-r border-gray-100 align-top">
                                    {{-- Indentasi Level 2 --}}
                                    <div class="flex items-start gap-3 pl-12 sm:pl-20 border-l-2 border-dashed border-gray-200 ml-4">
                                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200 mt-1 uppercase">SUB</span>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-700 text-sm">{{ $sub->kode }}</span>
                                            <span class="text-gray-500 text-sm leading-relaxed">{{ $sub->nama }}</span>

                                            {{-- KETERANGAN PENANGGUNG JAWAB --}}
                                            @if($sub->jabatan)
                                            <div class="flex items-center gap-1.5 mt-2 text-purple-700 bg-purple-100/50 px-2 py-1 rounded-md w-fit border border-purple-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span class="text-[11px] font-semibold">PJ: {{ $sub->jabatan->nama }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom Pagu & Target --}}
                                <td class="p-4 border-r border-gray-100 text-right align-top">
                                    <div class="text-xs font-bold text-purple-700">Rp {{ number_format($sub->pagu, 0, ',', '.') }}</div>
                                </td>

                                <td class="p-4 text-center align-middle relative">
                                    {{-- HANYA ADMIN YANG BISA EDIT SUB KEGIATAN --}}
                                    @if(auth()->user()->role == 'admin')
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1 bg-white text-[11px] font-medium text-gray-500 hover:bg-gray-100 focus:outline-none shadow-sm transition-colors">
                                            Menu <svg class="-mr-1 ml-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 animate-fade-in-down">
                                            <div class="py-1">
                                                {{-- Indikator Kinerja (HIJAU) --}}
                                                <button wire:click="openIndikator({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-green-600 hover:bg-green-50">
                                                    <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                    Indikator Kinerja
                                                </button>
                                                {{-- Penanggung Jawab (UNGU) --}}
                                                <button wire:click="openPenanggungJawab({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-purple-600 hover:bg-purple-50">
                                                    <svg class="mr-3 h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                    Penanggung Jawab
                                                </button>
                                            </div>
                                            <div class="py-1">
                                                <button wire:click="editSubKegiatan({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50">
                                                    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit Sub Kegiatan
                                                </button>
                                                <button wire:click="confirmDelete({{ $sub->id }}, 'sub_kegiatan')" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus Sub Kegiatan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400 italic">View Only</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                            @endforeach

                        </tbody>
                        @empty
                        <tbody>
                            <tr>
                                <td colspan="3" class="p-10 text-center text-gray-400 italic bg-white">
                                    Data belum tersedia untuk tahun {{ $tahun }}.
                                </td>
                            </tr>
                        </tbody>
                        @endforelse
                    </table>
                </div>

                {{-- FOOTER PAGINATION --}}
                <div class="mt-4 px-2 pb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    {{-- Keterangan Showing X to Y of Z --}}
                    <div class="text-sm text-gray-600">
                        @if($programs->total() > 0)
                        Showing <span class="font-bold text-gray-800">{{ $programs->firstItem() }}</span>
                        to <span class="font-bold text-gray-800">{{ $programs->lastItem() }}</span>
                        of <span class="font-bold text-gray-800">{{ $programs->total() }}</span> results
                        @else
                        No results found
                        @endif
                    </div>

                    {{-- Tombol Navigasi Pagination --}}
                    <div class="w-full sm:w-auto">
                        {{ $programs->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script Rupiah Helper (TETAP SAMA) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('rupiahInput', (modelName, initialValue) => ({
                displayValue: '',
                modelName: modelName,
                init() {
                    this.formatInitial(initialValue);
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
        });
    </script>

    {{-- 3. Modal Form (Dinamis: Program / Kegiatan / Sub Kegiatan) --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    {{ $isEditMode ? 'Edit' : 'Tambah' }}
                    @if($formType == 'program') Program
                    @elseif($formType == 'kegiatan') Kegiatan
                    @else Sub Kegiatan
                    @endif
                    <span class="text-blue-600">({{ $tahun }})</span> {{-- INFO TAHUN DI MODAL --}}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-5">
                {{-- Input Kode --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="kode" placeholder="Contoh: 1.02.01" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    @error('kode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Input Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                    <textarea wire:model="nama" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all"></textarea>
                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Input Pagu HANYA UNTUK SUB KEGIATAN --}}
                @if($formType == 'sub_kegiatan')
                <div class="mt-4">
                    {{-- Pagu Anggaran (Format Rupiah) --}}
                    <div x-data="rupiahInput('pagu', '{{ $pagu }}')">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pagu Anggaran</label>
                        <input type="text" x-model="displayValue" @input="updateWire" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right font-mono" placeholder="Rp 0">
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="store" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition-colors">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 2: INDIKATOR KINERJA (TETAP SAMA TAPI DENGAN KOLOM TARGET) --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Indikator Kinerja Sub Kegiatan</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $selectedSubKegiatan->nama ?? '-' }}</p>
                </div>
                <button wire:click="closeIndikatorModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Form Tambah/Edit Indikator --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Sub Output <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="subOutput" placeholder="Masukkan Sub Output" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="satuan" placeholder="Contoh: Dokumen" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                    </div>
                    {{-- TARGET (INPUT BARU) --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Target <span class="text-red-500">*</span></label>
                        <input type="number" step="any" wire:model="target" placeholder="0" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 outline-none text-center">
                    </div>
                    <div class="sm:col-span-2">
                        <button wire:click="saveIndikator" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm font-medium transition-colors h-[38px]">
                            {{ $indikatorId ? 'Update' : 'Tambah' }}
                        </button>
                    </div>
                </div>
                @error('subOutput') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                @error('satuan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                @error('target') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Tabel Indikator --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-xs font-bold uppercase border-b border-gray-200">
                            <th class="p-3 border-r border-gray-200 w-10 text-center">No</th>
                            <th class="p-3 border-r border-gray-200">Sub Output</th>
                            <th class="p-3 border-r border-gray-200 w-24 text-center">Satuan</th>
                            <th class="p-3 border-r border-gray-200 w-24 text-center">Target</th>
                            <th class="p-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                        @if($indikators && count($indikators) > 0)
                        @foreach($indikators as $index => $indikator)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-3 border-r border-gray-100 text-center">{{ $index + 1 }}</td>
                            <td class="p-3 border-r border-gray-100">{{ $indikator->keterangan }}</td>
                            <td class="p-3 border-r border-gray-100 text-center">{{ $indikator->satuan }}</td>
                            <td class="p-3 border-r border-gray-100 text-center font-bold">{{ $indikator->target }}</td>
                            <td class="p-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button wire:click="editIndikator({{ $indikator->id }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteIndikator({{ $indikator->id }})" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400 italic">Belum ada indikator kinerja.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 3: PENANGGUNG JAWAB (TETAP SAMA) --}}
    @if($isOpenPenanggungJawab)
    <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Set Penanggung Jawab</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $selectedSubKegiatan->nama ?? '-' }}</p>
                </div>
                <button wire:click="closePenanggungJawabModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jabatan / Struktur Organisasi</label>
                    <div class="relative">
                        <select wire:model="selectedJabatanId" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none appearance-none bg-white">
                            <option value="">-- Pilih Penanggung Jawab --</option>
                            @foreach($jabatans as $jabatan)
                            <option value="{{ $jabatan->id }}">{{ $jabatan->nama }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        *Pejabat yang memegang jabatan ini akan bertanggung jawab menginput realisasi fisik & keuangan untuk sub kegiatan ini.
                    </p>
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closePenanggungJawabModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="savePenanggungJawab" class="w-full sm:w-auto px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-sm transition-colors">Simpan Perubahan</button>
            </div>
        </div>
    </div>
    @endif
</div>