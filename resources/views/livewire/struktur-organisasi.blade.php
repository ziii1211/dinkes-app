<div>
    {{-- NOTIFIKASI SUKSES --}}
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm relative mx-auto max-w-7xl mt-4 animate-fade-in-down" role="alert">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="font-bold">Berhasil!</p>
            </div>
            <p class="ml-8">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Konfigurasi Judul Halaman & Breadcrumb --}}
    <x-slot:title>Struktur Organisasi</x-slot>

    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Master Data</span>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Struktur Organisasi</span>
        </div>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="space-y-8 relative z-10 mt-6 md:mt-8">

        {{-- INFORMASI UNIT KERJA --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center bg-white">
                <svg class="w-5 h-5 text-gray-700 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 2H15V7H9V2H5C3.9 2 3 2.9 3 4V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V4C21 2.9 20.1 2 19 2ZM11 18H7V16H11V18ZM11 14H7V12H11V14ZM11 10H7V8H11V10ZM17 18H13V16H17V18ZM17 14H13V12H17V14ZM17 10H13V8H17V10Z" />
                </svg>
                <h3 class="font-bold text-gray-800 text-lg">Informasi Unit Kerja</h3>
            </div>
            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-4 text-sm md:text-base">
                    <div class="md:col-span-2 text-gray-600 font-medium">Nama SKPD</div>
                    <div class="md:col-span-10 text-gray-800 font-semibold">: DINAS KESEHATAN</div>
                    
                    <div class="md:col-span-2 text-gray-600 font-medium">Kode SKPD</div>
                    <div class="md:col-span-10 text-gray-800 font-semibold break-all">: 1.02.0.00.0.00.01.0000</div>
                    
                    <div class="md:col-span-2 text-gray-600 font-medium">Status</div>
                    <div class="md:col-span-10 text-gray-800 font-semibold">: SKPD</div>
                </div>
            </div>
        </div>

        {{-- DAFTAR JABATAN --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white gap-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2 transform rotate-45 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                    </svg>
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Jabatan</h3>
                </div>
                {{-- HANYA ADMIN YANG BISA TAMBAH --}}
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="createJabatan" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </button>
                @endif
            </div>

            <div class="p-0 md:p-6">
                {{-- TABEL SCROLLABLE --}}
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto relative rounded-none md:rounded-lg border-t md:border border-gray-100">
                    <table class="w-full text-left border-collapse whitespace-nowrap md:whitespace-normal">
                        <thead class="sticky top-0 z-10 bg-gray-50 shadow-sm">
                            <tr class="border-b border-gray-200">
                                <th class="p-3 text-sm font-bold text-gray-700 w-16 text-center min-w-[50px]">#</th>
                                <th class="p-3 text-sm font-bold text-gray-700 min-w-[250px]">Nama Jabatan</th>
                                {{-- KOLOM MENU HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <th class="p-3 text-sm font-bold text-gray-700 w-32 text-center min-w-[120px]">Menu</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @if ($jabatans->count() > 0)
                            @foreach ($jabatans as $index => $jabatan)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="p-4 text-sm text-gray-600 text-center font-medium">{{ $index + 1 }}</td>
                                <td class="p-4 text-sm text-gray-800 font-semibold whitespace-nowrap">
                                    <div class="flex items-center">
                                        {{-- Tampilan Tabel Tetap Menggunakan Panah agar Visual Pohon Jelas --}}
                                        @if ($jabatan->level == 1) <span class="text-gray-400 mr-3 ml-4 text-lg font-bold">↳</span>
                                        @elseif ($jabatan->level >= 2) <span class="text-gray-400 mr-3 ml-8 text-lg font-bold">↳ ↳</span>
                                        @endif
                                        {{ $jabatan->nama }}
                                    </div>
                                </td>
                                {{-- AKSI EDIT/DELETE HANYA ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="editJabatan({{ $jabatan->id }})" class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded shadow-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="deleteJabatan({{ $jabatan->id }})" wire:confirm="Yakin hapus jabatan?" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded shadow-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('admin') ? 3 : 2 }}" class="p-8 text-center text-gray-500 italic bg-gray-50">Data jabatan kosong.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- DAFTAR PEGAWAI --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white gap-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                    </svg>
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Pegawai</h3>
                </div>
                {{-- HANYA ADMIN YANG BISA TAMBAH PEGAWAI --}}
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="createPegawai" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </button>
                @endif
            </div>

            <div class="p-6">
                <div class="flex justify-end items-center mb-6">
                    <div class="flex items-center w-full sm:w-auto">
                        <span class="text-sm text-gray-600 mr-2 hidden sm:inline">Search:</span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama / NIP..." class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-gray-50 w-full sm:w-64 transition-colors">
                    </div>
                </div>

                {{-- TABEL PEGAWAI SCROLLABLE --}}
                <div class="overflow-x-auto relative rounded-lg border border-gray-100 min-h-[300px]">
                    <table class="w-full text-left border-collapse whitespace-nowrap md:whitespace-normal">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="p-3 text-sm font-bold text-gray-700 w-12 text-center min-w-[50px]">#</th>
                                <th class="p-3 text-sm font-bold text-gray-700 w-20 text-center min-w-[80px]">Foto</th>
                                <th class="p-3 text-sm font-bold text-gray-700 min-w-[200px]">Nama</th>
                                <th class="p-3 text-sm font-bold text-gray-700 min-w-[150px]">NIP</th>
                                <th class="p-3 text-sm font-bold text-gray-700 text-center min-w-[120px]">Status</th>
                                {{-- KOLOM MENU HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <th class="p-3 text-sm font-bold text-gray-700 w-32 text-center min-w-[120px]">Menu</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @if ($pegawais->count() > 0)
                            @foreach ($pegawais as $index => $pegawai)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="p-4 text-sm text-gray-600 text-center font-medium">{{ $pegawais->firstItem() + $index }}</td>
                                <td class="p-4 text-center">
                                    <img src="{{ $pegawai->foto ? asset('storage/'.$pegawai->foto) : 'https://ui-avatars.com/api/?name='.urlencode($pegawai->nama).'&background=0D8ABC&color=fff&size=128' }}" class="w-10 h-10 rounded-full object-cover mx-auto border border-gray-200 shadow-sm" loading="lazy">
                                </td>
                                <td class="p-4 whitespace-normal">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-800 font-medium">{{ $pegawai->nama }}</span>
                                        <span class="text-xs text-gray-500 mt-0.5">{{ $pegawai->jabatan ? $pegawai->jabatan->nama : '-' }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-sm text-gray-600">{{ $pegawai->nip }}</td>
                                <td class="p-4 text-center">
                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                                        $dotClass = 'bg-gray-500';

                                        if ($pegawai->status == 'Definitif') {
                                            $statusClass = 'bg-green-100 text-green-800 border-green-200';
                                            $dotClass = 'bg-green-500';
                                        } elseif ($pegawai->status == 'Plt') {
                                            $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                            $dotClass = 'bg-yellow-500';
                                        } elseif ($pegawai->status == 'Pj' || $pegawai->status == 'Pjs') {
                                            $statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                            $dotClass = 'bg-blue-500';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusClass }}">
                                        <span class="w-2 h-2 rounded-full mr-1.5 {{ $dotClass }}"></span>
                                        {{ $pegawai->status }}
                                    </span>
                                </td>
                                {{-- AKSI EDIT/DELETE HANYA ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="editPegawai({{ $pegawai->id }})" class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded shadow-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="deletePegawai({{ $pegawai->id }})" wire:confirm="Yakin hapus pegawai?" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded shadow-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('admin') ? 6 : 5 }}" class="p-8 text-center text-gray-500 italic bg-gray-50">Belum ada data pegawai.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- NAVIGASI PAGINATION --}}
                <div class="mt-4">
                    {{ $pegawais->links() }} 
                </div>
            </div>
        </div>

        {{-- MODAL HANYA RENDER JIKA ADMIN --}}
        @if(auth()->user()->hasRole('admin'))
        @if($modalJabatanOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100 flex-shrink-0 bg-white">
                    <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Jabatan' : 'Tambah Jabatan' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 md:p-8 space-y-6 overflow-y-auto">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Jabatan</label>
                        <input type="text" wire:model="jab_nama" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                        @error('jab_nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan Atasan</label>
                        <select wire:model="jab_parent_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-blue-500 outline-none bg-white font-sans">
                            <option value="">-- Pilih Jabatan Atasan (Unit Kerja Induk) --</option>
                            @foreach($jabatans as $jab)
                                @if($jab->id != $jab_id)
                                <option value="{{ $jab->id }}">
                                    @if($jab->level == 0)
                                        {{ strtoupper($jab->nama) }}
                                    @else
                                        {{ str_repeat('-', $jab->level) }} {{ $jab->nama }}
                                    @endif
                                </option>
                                @endif
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">*Kosongkan jika ini adalah Jabatan Tertinggi (Kepala Dinas)</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 flex-shrink-0">
                    <button wire:click="closeModal" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">Batal</button>
                    <button wire:click="storeJabatan" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
                </div>
            </div>
        </div>
        @endif

        @if($modalPegawaiOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100 flex-shrink-0 bg-white">
                    <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 md:p-8 space-y-4 overflow-y-auto">
                    <div class="flex items-center gap-4">
                        @if ($peg_foto)
                        <img src="{{ $peg_foto->temporaryUrl() }}" class="w-16 h-16 rounded-full object-cover border">
                        @elseif ($peg_foto_lama)
                        <img src="{{ asset('storage/'.$peg_foto_lama) }}" class="w-16 h-16 rounded-full object-cover border">
                        @else
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        @endif
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Pegawai</label>
                            <input type="file" wire:model="peg_foto" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                            @error('peg_foto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="peg_nama" placeholder="Gelar dan Nama Lengkap" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none">
                        @error('peg_nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input type="text" wire:model="peg_nip" placeholder="Nomor Induk Pegawai" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none">
                        @error('peg_nip') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <select wire:model="peg_jabatan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none bg-white font-sans">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatans as $jab)
                                <option value="{{ $jab->id }}">
                                    @if($jab->level == 0)
                                        {{ strtoupper($jab->nama) }}
                                    @else
                                        {{ str_repeat('-', $jab->level) }} {{ $jab->nama }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="peg_status" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 outline-none bg-white">
                                <option value="Definitif">Definitif</option>
                                <option value="Plt">Plt</option>
                                <option value="Pj">Pj</option>
                                <option value="Pjs">Pjs</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 flex-shrink-0">
                    <button wire:click="closeModal" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">Batal</button>
                    <button wire:click="storePegawai" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</div>