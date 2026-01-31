<div>
    {{-- CSS & JS FLATPICKR (KHUSUS HALAMAN INI) --}}
    @assets
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
        <style>
            /* Custom Style Flatpickr agar senada dengan Tailwind */
            .flatpickr-calendar { border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: none; }
            .flatpickr-month { border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; }
        </style>
    @endassets

    <x-slot:title>
        Laporan Konsolidasi
    </x-slot>

    <x-slot:breadcrumb>
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-blue-100 transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Laporan Konsolidasi</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        
        {{-- CARD WRAPPER --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            
            {{-- A. Header --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    Daftar Laporan 
                    

                <button wire:click="create" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-all shadow-md focus:ring-4 focus:ring-blue-100 transform active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Laporan Baru</span>
                </button>
            </div>

            {{-- Flash Message --}}
            @if (session()->has('message'))
                <div class="px-6 pt-4">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3 animate-fade-in-down">
                        <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- B. Filter & Search --}}
            <div class="p-6 bg-white grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                {{-- Show Entries --}}
                <div class="md:col-span-3 flex items-center gap-3">
                    <span class="text-sm text-gray-500 font-medium">Show</span>
                    <select wire:model.live="perPage" class="border-gray-200 bg-gray-50 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-3 shadow-sm cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                {{-- Filter Tahun --}}
                <div class="md:col-span-3">
                    <select wire:model.live="filterTahun" class="w-full border-gray-200 bg-gray-50 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-3 shadow-sm cursor-pointer text-gray-600">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div class="md:col-span-6 relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           class="pl-10 w-full border-gray-200 bg-gray-50 rounded-lg text-sm py-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm placeholder-gray-400 focus:bg-white" 
                           placeholder="Cari judul laporan...">
                    
                    {{-- Loading Spinner --}}
                    <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- C. Table --}}
            <div class="overflow-x-auto border-t border-gray-100 min-h-[400px]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 text-xs uppercase text-gray-500 font-bold tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">#</th>
                            <th class="px-6 py-4 min-w-[300px]">Detail Laporan</th>
                            <th class="px-6 py-4 min-w-[200px]">Periode Laporan</th>
                            <th class="px-6 py-4 text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    
                    {{-- DATA --}}
                    <tbody wire:loading.class="hidden" class="text-sm text-gray-600 divide-y divide-gray-100 bg-white">
                        @forelse($laporans as $index => $laporan)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            
                            {{-- No --}}
                            <td class="px-6 py-5 text-center font-medium text-gray-400">
                                {{ $laporans->firstItem() + $index }}
                            </td>

                            {{-- Judul --}}
                            <td class="px-6 py-5">
                                <div class="flex items-start gap-4">
                                    <div class="p-2.5 bg-blue-100/50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-base group-hover:text-blue-700 transition-colors line-clamp-2">
                                            {{ $laporan->judul }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-400">
                                            <span class="flex items-center gap-1 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Update: {{ $laporan->updated_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Periode (Gabungan) --}}
                            <td class="px-6 py-5">
                                <div class="inline-flex items-center bg-white border border-gray-200 rounded-lg p-1.5 shadow-sm group-hover:border-blue-200 transition-colors">
                                    <div class="bg-blue-50 text-blue-600 p-1.5 rounded-md mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col pr-2">
                                        <span class="text-[10px] uppercase font-bold text-gray-400 leading-none mb-0.5">Periode</span>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-sm font-bold text-gray-800">{{ $laporan->bulan }}</span>
                                            <span class="text-xs font-mono font-medium text-gray-500 bg-gray-100 px-1.5 rounded">{{ $laporan->tahun }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    {{-- Input Data --}}
                                    <a href="{{ route('laporan-konsolidasi.input', $laporan->id) }}" 
                                       class="flex items-center gap-2 px-3 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg border border-emerald-200 hover:border-emerald-600 transition-all duration-200 text-xs font-bold uppercase tracking-wider shadow-sm group/btn">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Isi Data
                                    </a>
                                    
                                    {{-- Edit --}}
                                    <button wire:click="edit({{ $laporan->id }})" class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors border border-transparent hover:border-yellow-200" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>

                                    {{-- Delete --}}
                                    <button wire:click="delete({{ $laporan->id }})" 
                                            wire:confirm="Yakin ingin menghapus laporan ini?"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-200" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center animate-fade-in-down">
                                    <div class="bg-gray-50 rounded-full p-6 mb-4 border border-gray-100 shadow-inner">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <h3 class="text-gray-900 font-bold text-lg">Tidak ada laporan ditemukan</h3>
                                    <p class="text-gray-500 mt-2 max-w-sm">Belum ada data yang cocok dengan filter atau pencarian Anda. Silakan buat laporan baru.</p>
                                    <button wire:click="create" class="mt-6 text-blue-600 hover:text-blue-800 font-semibold text-sm hover:underline">
                                        + Buat Laporan Baru Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    {{-- SKELETON LOADING --}}
                    <tbody wire:loading class="w-full bg-white divide-y divide-gray-100">
                        @for ($i = 0; $i < 5; $i++)
                        <tr class="animate-pulse">
                            <td class="px-6 py-5 text-center"><div class="h-4 bg-gray-200 rounded w-4 mx-auto"></div></td>
                            <td class="px-6 py-5">
                                <div class="flex gap-4">
                                    <div class="h-12 w-12 bg-gray-200 rounded-xl"></div>
                                    <div class="flex-1 space-y-2 py-1">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5"><div class="h-10 bg-gray-200 rounded-lg w-32"></div></td>
                            <td class="px-6 py-5 text-center"><div class="h-8 bg-gray-200 rounded-md w-full"></div></td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                {{ $laporans->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL POP UP (With AlpineJS + Flatpickr) --}}
    @if($isOpen)
    <div class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 relative">
                
                {{-- Modal Header --}}
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Perbarui Laporan' : 'Buat Laporan Baru' }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Lengkapi informasi dasar laporan.</p>
                    </div>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 p-1 rounded-full">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-6 space-y-6">
                    
                    {{-- Input Judul --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Laporan</label>
                        <input type="text" wire:model="judul" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-4 transition-colors placeholder-gray-400" placeholder="Contoh: Laporan Kinerja Triwulan I">
                        @error('judul') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    {{-- DATEPICKER (Flatpickr) --}}
                    <div wire:ignore>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Periode Laporan (Bulan & Tahun)</label>
                        <div 
                            x-data="{ value: @entangle('periode') }" 
                            x-init="
                                flatpickr($refs.picker, {
                                    plugins: [
                                        new monthSelectPlugin({
                                            shorthand: true, // Tampilkan singkatan bulan (Jan, Feb)
                                            dateFormat: 'Y-m', // Format output value
                                            altFormat: 'F Y', // Format tampilan (Januari 2025)
                                            theme: 'light' // Tema
                                        })
                                    ],
                                    defaultDate: value,
                                    onChange: function(selectedDates, dateStr, instance) {
                                        @this.set('periode', dateStr);
                                    }
                                })
                            "
                            class="relative"
                        >
                            <input 
                                x-ref="picker" 
                                type="text" 
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-4 pl-10 cursor-pointer bg-white" 
                                placeholder="Pilih Bulan..."
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Klik kolom di atas untuk memilih bulan dan tahun sekaligus.</p>
                    </div>
                    @error('periode') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror

                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100 rounded-b-2xl">
                    <button wire:click="save" class="inline-flex justify-center items-center rounded-lg shadow-sm px-5 py-2.5 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform active:scale-95">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Laporan' }}
                    </button>
                    <button wire:click="closeModal" class="inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>