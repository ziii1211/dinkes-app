<div>
    {{-- Konfigurasi Judul Halaman & Breadcrumb --}}
    <x-slot:title>Program & Kegiatan</x-slot>

    <x-slot:breadcrumb>
        {{-- Wrapper agar breadcrumb bisa discroll jika terlalu panjang di mobile --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Matrik Renstra</span>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Program/Kegiatan/Sub</span>
        </div>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="space-y-8 relative z-10 mt-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header --}}
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Program / Kegiatan / Sub Kegiatan</h3>

                {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="create" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex justify-center items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Program
                </button>
                @endif
            </div>

            {{-- Table Wrapper --}}
            <div class="p-4 sm:p-6 pb-10">
                {{-- overflow-x-auto memastikan tabel bisa discroll ke kanan pada mobile --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px] pb-40">
                    <table class="w-full text-left border-collapse whitespace-nowrap sm:whitespace-normal">
                        <thead>
                            <tr class="bg-white text-gray-700 text-sm font-bold border-b border-gray-200">
                                <th rowspan="2" class="p-4 border-r border-gray-200 align-middle min-w-[250px] sm:w-96">Program / Outcome</th>
                                <th colspan="6" class="p-4 border-b border-r border-gray-200 text-center align-middle">Periode</th>
                                {{-- KOLOM AKSI HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <th rowspan="2" class="p-4 text-center align-middle w-32">Aksi</th>
                                @endif
                            </tr>
                            <tr class="bg-white text-gray-800 text-sm font-bold border-b border-gray-200">
                                <th class="p-4 border-r text-center min-w-[80px]">2025</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2026</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2027</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2028</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2029</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2030</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-600">
                            @forelse($programs as $program)
                            {{-- BARIS PROGRAM --}}
                            <tr class="bg-white border-b border-gray-100 group">
                                <td class="p-6 border-r border-gray-100 align-top whitespace-normal">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 h-6 whitespace-nowrap">
                                                Program
                                            </span>
                                            <span class="text-gray-800 font-bold leading-relaxed uppercase">
                                                {{ $program->kode }} {{ $program->nama }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="6" class="p-4 border-r text-center text-gray-300 align-middle">&mdash;</td>

                                {{-- TOMBOL MENU PROGRAM --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm">
                                            Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                            <div class="py-1">
                                                <button wire:click="addOutcome({{ $program->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-green-600 hover:bg-green-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    Tambah Outcome
                                                </button>
                                                <button wire:click="edit({{ $program->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-indigo-600 hover:bg-indigo-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Edit Program
                                                </button>
                                                <button wire:click="delete({{ $program->id }})" wire:confirm="Hapus Program?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus Program
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>

                            {{-- LOOP OUTCOME DI DALAM PROGRAM --}}
                            @foreach($program->outcomes as $outcome)
                            <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="p-6 border-r border-gray-100 align-top pl-6 sm:pl-12 whitespace-normal">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800 border border-green-200 h-6 whitespace-nowrap">
                                                Outcome
                                            </span>
                                            <span class="text-gray-700 text-sm leading-relaxed">
                                                {{ $outcome->outcome }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td colspan="6" class="p-4 border-r text-center text-gray-300 align-middle">&mdash;</td>

                                {{-- TOMBOL MENU OUTCOME --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-transparent px-3 py-1.5 bg-blue-50 text-sm font-medium text-blue-700 hover:bg-blue-100 focus:outline-none transition-colors">
                                            Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                            <div class="py-1">
                                                {{-- UPDATE DI SINI: MENAMBAHKAN outcome_id --}}
                                                <a href="{{ route('matrik.kegiatan', ['id' => $program->id, 'outcome_id' => $outcome->id]) }}" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    Kegiatan
                                                </a>
                                                <button wire:click="removeOutcome({{ $outcome->id }})" wire:confirm="Lepas outcome ini dari program?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus Outcome
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach

                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('admin') ? 8 : 7 }}" class="p-10 text-center text-gray-400 italic bg-gray-50">
                                    Data Program belum tersedia.
                                    @if(auth()->user()->hasRole('admin'))
                                    Silakan klik tombol <strong>+ Tambah Program</strong>.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HANYA UNTUK ADMIN --}}
    @if(auth()->user()->hasRole('admin'))
    
    {{-- MODAL TAMBAH/EDIT PROGRAM --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Program' : 'Tambah Program' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Program <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="kode" placeholder="Contoh: 1.02.01" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    @error('kode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Program <span class="text-red-500">*</span></label>
                    <textarea wire:model="nama" rows="3" placeholder="Nama Program" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none transition-all"></textarea>
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

    {{-- MODAL PILIH OUTCOME --}}
    @if($isOpenOutcome)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Pilih Outcome Program</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Data Outcome <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select wire:model="outcome_id_to_add" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none appearance-none bg-white cursor-pointer shadow-sm">
                            <option value="">-- Pilih Outcome dari Data Master --</option>
                            @foreach($outcomes_list as $outcome)
                            <option value="{{ $outcome->id }}">
                                @if($outcome->program_id)
                                [Sudah Terpakai] -
                                @endif
                                {{ Str::limit($outcome->outcome, 100) }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" /></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Menampilkan seluruh data Outcome yang diinput di halaman Outcome.
                    </p>
                    @error('outcome_id_to_add') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="storeOutcome" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition-colors">Simpan</button>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>