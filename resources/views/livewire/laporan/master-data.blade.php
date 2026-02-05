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

            {{-- Header --}}
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Laporan Master Data</h3>
                <button wire:click="createProgram" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex justify-center items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Program
                </button>
            </div>

            {{-- Table Wrapper --}}
            <div class="p-4 sm:p-6 pb-10">
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px]">
                    <table class="w-full text-left border-collapse whitespace-nowrap sm:whitespace-normal">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 text-sm font-bold border-b border-gray-200">
                                <th class="p-4 border-r border-gray-200 align-middle">Program / Kegiatan / Sub Kegiatan</th>
                                <th class="p-4 align-middle text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-600">
                            @forelse($programs as $program)
                                
                                {{-- === BARIS PROGRAM === --}}
                                <tr class="bg-blue-50/40 border-b border-gray-100 group hover:bg-blue-50 transition-colors">
                                    <td class="p-4 border-r border-gray-100 align-top">
                                        <div class="flex items-start gap-3">
                                            <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 mt-1">PROGRAM</span>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-800 text-base">{{ $program->kode }}</span>
                                                <span class="text-gray-700 font-medium leading-relaxed">{{ $program->nama }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center align-middle relative">
                                        {{-- Dropdown Program --}}
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm transition-colors">
                                                Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                            <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 animate-fade-in-down">
                                                <div class="py-1">
                                                    <button wire:click="createKegiatan({{ $program->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-green-600 hover:bg-green-50">
                                                        <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                        Tambah Kegiatan
                                                    </button>
                                                </div>
                                                <div class="py-1">
                                                    <button wire:click="editProgram({{ $program->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50">
                                                        <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        Edit Program
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $program->id }}, 'program')" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                                        <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus Program
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- === LOOP KEGIATAN === --}}
                                @foreach($program->kegiatans as $kegiatan)
                                <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
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
                                    <td class="p-4 text-center align-middle relative">
                                        {{-- Dropdown Kegiatan --}}
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-600 hover:bg-gray-100 focus:outline-none shadow-sm transition-colors">
                                                Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                            <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 animate-fade-in-down">
                                                <div class="py-1">
                                                    {{-- Tambah Sub Kegiatan (HIJAU) --}}
                                                    <button wire:click="createSubKegiatan({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-green-600 hover:bg-green-50">
                                                        <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                        Tambah Sub Kegiatan
                                                    </button>
                                                </div>
                                                <div class="py-1">
                                                    <button wire:click="editKegiatan({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50">
                                                        <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        Edit Kegiatan
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $kegiatan->id }}, 'kegiatan')" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                                        <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus Kegiatan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                    {{-- === LOOP SUB KEGIATAN === --}}
                                    @foreach($kegiatan->subKegiatans as $sub)
                                    <tr class="bg-gray-50/50 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="p-4 border-r border-gray-100 align-top">
                                            {{-- Indentasi Level 2 --}}
                                            <div class="flex items-start gap-3 pl-12 sm:pl-20 border-l-2 border-dashed border-gray-200 ml-4">
                                                <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200 mt-1 uppercase">SUB</span>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-gray-700 text-sm">{{ $sub->kode }}</span>
                                                    <span class="text-gray-500 text-sm leading-relaxed">{{ $sub->nama }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center align-middle relative">
                                            {{-- Dropdown Sub Kegiatan --}}
                                            <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                                <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1 bg-white text-[11px] font-medium text-gray-500 hover:bg-gray-100 focus:outline-none shadow-sm transition-colors">
                                                    Menu <svg class="-mr-1 ml-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                </button>
                                                <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 animate-fade-in-down">
                                                    <div class="py-1">
                                                        {{-- Indikator Kinerja (HIJAU) --}}
                                                        <button wire:click="openIndikator({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-green-600 hover:bg-green-50">
                                                            <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                            Indikator Kinerja
                                                        </button>
                                                    </div>
                                                    <div class="py-1">
                                                        <button wire:click="editSubKegiatan({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50">
                                                            <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            Edit Sub Kegiatan
                                                        </button>
                                                        <button wire:click="confirmDelete({{ $sub->id }}, 'sub_kegiatan')" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                                            <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus Sub Kegiatan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                @endforeach

                            @empty
                                <tr>
                                    <td colspan="2" class="p-10 text-center text-gray-400 italic bg-white">
                                        Data belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- === BAGIAN PAGINATION (DITAMBAHKAN) === --}}
                <div class="mt-4 px-2">
                    {{ $programs->links() }}
                </div>
                {{-- ======================================= --}}

            </div>
        </div>
    </div>

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
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode 
                        @if($formType == 'program') Program
                        @elseif($formType == 'kegiatan') Kegiatan
                        @else Sub Kegiatan
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="kode" placeholder="Contoh: 1.02.01" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    @error('kode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                         Nama 
                        @if($formType == 'program') Program
                        @elseif($formType == 'kegiatan') Kegiatan
                        @else Sub Kegiatan
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="nama" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all"></textarea>
                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="store" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition-colors">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 2: INDIKATOR KINERJA (BARU) --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Indikator Kinerja Sub Kegiatan</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $selectedSubKegiatan->nama ?? '-' }}</p>
                </div>
                <button wire:click="closeIndikatorModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Form Tambah/Edit Indikator --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-8">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Sub Output <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="subOutput" placeholder="Masukkan Sub Output / Tolok Ukur" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Satuan Unit <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="satuan" placeholder="Contoh: Dokumen" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <button wire:click="saveIndikator" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm font-medium transition-colors h-[38px]">
                            {{ $indikatorId ? 'Update' : 'Tambah' }}
                        </button>
                    </div>
                </div>
                @error('subOutput') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                @error('satuan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Tabel Indikator --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-xs font-bold uppercase border-b border-gray-200">
                            <th class="p-3 border-r border-gray-200 w-10 text-center">No</th>
                            <th class="p-3 border-r border-gray-200">Sub Output</th>
                            <th class="p-3 border-r border-gray-200 w-32 text-center">Satuan Unit</th>
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
                                <td class="p-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button wire:click="editIndikator({{ $indikator->id }})" class="text-blue-500 hover:text-blue-700" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button wire:click="deleteIndikator({{ $indikator->id }})" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="p-6 text-center text-gray-400 italic">Belum ada indikator kinerja.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>