<div>
    {{-- Konfigurasi Judul Halaman & Breadcrumb --}}
    <x-slot:title>Tujuan Renstra</x-slot>

    <x-slot:breadcrumb>
        {{-- Wrapper agar breadcrumb bisa discroll jika terlalu panjang di mobile --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Matrik Renstra</span>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Tujuan</span>
        </div>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="space-y-8 relative z-10 mt-8">

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header --}}
            {{-- Menggunakan flex-col pada mobile agar judul dan tombol tersusun vertikal --}}
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Tujuan Renstra</h3>

                {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="create" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex justify-center items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Tujuan
                </button>
                @endif
            </div>

            {{-- Table Wrapper --}}
            <div class="p-4 sm:p-6">
                {{-- overflow-x-auto memastikan tabel bisa discroll ke kanan pada mobile --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px]">
                    <table class="w-full text-left border-collapse whitespace-nowrap sm:whitespace-normal">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 text-sm font-bold uppercase tracking-wider border-b border-gray-200">
                                <th rowspan="2" class="p-4 border-r border-gray-200 text-center align-middle min-w-[200px] sm:w-64">Sasaran RPJMD</th>
                                <th rowspan="2" class="p-4 border-r border-gray-200 text-center align-middle min-w-[200px] sm:w-64">Tujuan Renstra</th>
                                <th rowspan="2" class="p-4 border-r border-gray-200 text-center align-middle min-w-[150px] sm:w-48">Indikator</th>
                                <th colspan="6" class="p-2 border-b border-r border-gray-200 text-center align-middle">Target</th>
                                {{-- HEADER AKSI HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <th rowspan="2" class="p-4 text-center align-middle w-32">Aksi</th>
                                @endif
                            </tr>
                            <tr class="bg-gray-50 text-gray-700 text-xs font-bold uppercase tracking-wider border-b border-gray-200">
                                <th class="p-2 border-r border-gray-200 text-center min-w-[60px]">2025</th>
                                <th class="p-2 border-r border-gray-200 text-center min-w-[60px]">2026</th>
                                <th class="p-2 border-r border-gray-200 text-center min-w-[60px]">2027</th>
                                <th class="p-2 border-r border-gray-200 text-center min-w-[60px]">2028</th>
                                <th class="p-2 border-r border-gray-200 text-center min-w-[60px]">2029</th>
                                <th class="p-2 border-r border-gray-200 text-center min-w-[60px]">2030</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-600">
                            @php
                            $groupedTujuans = $tujuans->groupBy('sasaran_rpjmd');
                            @endphp

                            @forelse($groupedTujuans as $sasaran => $items)
                            <tr class="bg-gray-50">
                                <td class="p-4 border-r border-gray-100 font-bold text-gray-800 align-top whitespace-normal">
                                    {{ $sasaran }}
                                </td>
                                <td class="p-4 border-r border-gray-100 text-center text-gray-300 align-middle">&mdash;</td>
                                <td class="p-4 border-r border-gray-100 text-center text-gray-300 align-middle">&mdash;</td>
                                <td colspan="6" class="p-2 border-r text-center text-gray-300">&mdash;</td>
                                {{-- KOLOM KOSONG AKSI HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center"></td>
                                @endif
                            </tr>

                            @foreach($items as $tujuan)
                            <tr class="hover:bg-blue-50 transition-colors group">
                                <td class="p-4 border-r border-gray-100"></td>
                                <td class="p-4 border-r border-gray-100 align-top whitespace-normal">
                                    <div class="text-gray-700 font-medium">{{ $tujuan->tujuan }}</div>
                                    @if($tujuan->jabatan)
                                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 shadow-sm">
                                        <svg class="mr-1.5 h-3 w-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        PJ: {{ $tujuan->jabatan->nama }}
                                    </div>
                                    @endif
                                </td>
                                <td class="p-4 border-r text-center text-gray-400 align-middle">&mdash;</td>

                                <td colspan="6" class="p-2 border-r text-center text-gray-300">&mdash;</td>

                                {{-- TOMBOL MENU TUJUAN HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-transparent px-3 py-1.5 bg-blue-100 text-sm font-medium text-blue-700 hover:bg-blue-200 focus:outline-none transition-colors">
                                            Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        {{-- Dropdown Menu --}}
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                            <div class="py-1">
                                                <button wire:click="pilihPenanggungJawab({{ $tujuan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors"><svg class="mr-3 h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>Penanggung Jawab</button>
                                                <button wire:click="tambahIndikator({{ $tujuan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50 transition-colors"><svg class="mr-3 h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>Tambah Indikator</button>
                                            </div>
                                            <div class="py-1">
                                                <button wire:click="edit({{ $tujuan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-indigo-600 hover:bg-indigo-50 transition-colors"><svg class="mr-3 h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>Edit Tujuan</button>
                                                <button wire:click="delete({{ $tujuan->id }})" wire:confirm="Yakin hapus?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors"><svg class="mr-3 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>Hapus Tujuan</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>

                            @foreach($tujuan->indikators as $indikator)
                            <tr class="hover:bg-gray-50 transition-colors bg-white border-l-4 border-blue-500">
                                <td class="p-4 border-r border-gray-100"></td>
                                <td class="p-4 border-r border-gray-100"></td>

                                <td class="p-4 border-r border-gray-100 text-sm text-gray-700 align-top whitespace-normal">
                                    <div class="font-medium">{{ $indikator->keterangan }}</div>
                                </td>

                                <td class="p-2 border-r text-center text-gray-600 text-xs font-medium">{{ $indikator->target_2025 ? $indikator->target_2025 . ' ' . $indikator->satuan : '-' }}</td>
                                <td class="p-2 border-r text-center text-gray-600 text-xs font-medium">{{ $indikator->target_2026 ? $indikator->target_2026 . ' ' . $indikator->satuan : '-' }}</td>
                                <td class="p-2 border-r text-center text-gray-600 text-xs font-medium">{{ $indikator->target_2027 ? $indikator->target_2027 . ' ' . $indikator->satuan : '-' }}</td>
                                <td class="p-2 border-r text-center text-gray-600 text-xs font-medium">{{ $indikator->target_2028 ? $indikator->target_2028 . ' ' . $indikator->satuan : '-' }}</td>
                                <td class="p-2 border-r text-center text-gray-600 text-xs font-medium">{{ $indikator->target_2029 ? $indikator->target_2029 . ' ' . $indikator->satuan : '-' }}</td>
                                <td class="p-2 border-r text-center text-gray-600 text-xs font-medium">{{ $indikator->target_2030 ? $indikator->target_2030 . ' ' . $indikator->satuan : '-' }}</td>

                                {{-- TOMBOL MENU INDIKATOR HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm">
                                            Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                            <div class="py-1">
                                                <button wire:click="editIndikator({{ $indikator->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50"><svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>Edit Indikator</button>
                                                <button wire:click="aturTarget({{ $indikator->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-purple-600 hover:bg-purple-50"><svg class="mr-3 h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>Atur Target</button>
                                                <button wire:click="deleteIndikator({{ $indikator->id }})" wire:confirm="Hapus indikator ini?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"><svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>Hapus Indikator</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach

                            @endforeach
                            @empty
                            <tr>
                                {{-- COLSPAN DISESUAIKAN: 11 JIKA ADMIN, 10 JIKA BUKAN --}}
                                <td colspan="{{ auth()->user()->hasRole('admin') ? 11 : 10 }}" class="p-10 text-center text-gray-400 italic bg-gray-50">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        <p>Data Tujuan Renstra belum tersedia.</p>
                                        {{-- TEXT HINT HANYA UNTUK ADMIN --}}
                                        @if(auth()->user()->hasRole('admin'))
                                        <p class="text-xs mt-1">Silakan klik tombol <strong>+ Tambah Tujuan</strong> di pojok kanan atas.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- SEMUA MODAL DIBUNGKUS HANYA UNTUK ADMIN --}}
    @if(auth()->user()->hasRole('admin'))

    {{-- MODAL TAMBAH/EDIT TUJUAN --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl sm:mx-4 p-6 overflow-y-auto max-h-[90vh]">
            <h3 class="text-xl font-bold mb-4">{{ $isEditMode ? 'Edit Tujuan' : 'Tambah Tujuan' }}</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Sasaran</label>
                    <select wire:model="sasaran_rpjmd" class="w-full border rounded px-3 py-2">
                        <option value="">Pilih Sasaran</option>
                        @foreach($opsi_sasaran as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
                    </select>
                    @error('sasaran_rpjmd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-1">Tujuan</label>
                    <textarea wire:model="tujuan" class="w-full border rounded px-3 py-2" rows="3"></textarea>
                    @error('tujuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button wire:click="closeModal" class="w-full sm:w-auto px-4 py-2 bg-gray-200 rounded">Batal</button>
                <button wire:click="store" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PENANGGUNG JAWAB --}}
    @if($isOpenPJ)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 overflow-y-auto max-h-[90vh]">
            <h3 class="text-xl font-bold mb-4">Penanggung Jawab</h3>
            <div class="space-y-4">
                <div class="bg-gray-50 p-3 rounded text-sm italic">"{{ $pj_tujuan_text }}"</div>
                <div>
                    <label class="block text-sm mb-1">Jabatan</label>
                    <select wire:model="pj_jabatan_id" class="w-full border rounded px-3 py-2">
                        <option value="">Pilih Jabatan</option>
                        @foreach($jabatans as $j) <option value="{{ $j->id }}">{{ $j->nama }}</option> @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button wire:click="closeModal" class="w-full sm:w-auto px-4 py-2 bg-gray-200 rounded">Batal</button>
                <button wire:click="simpanPenanggungJawab" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL INDIKATOR --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 transform transition-all scale-100 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Indikator' : 'Form Indikator Tujuan' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea wire:model="ind_keterangan" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                    <select wire:model="ind_satuan" class="w-full border rounded px-3 py-2">
                        <option value="">Pilih Satuan</option>
                        <option value="Angka">Angka</option>
                        <option value="Barang">Barang</option>
                        <option value="Bulan">Bulan</option>
                        <option value="Data/Bulan">Data/Bulan</option>
                        <option value="Dokumen">Dokumen</option>
                        <option value="Fasyankes">Fasyankes</option>
                        <option value="Indeks">Indeks</option>
                        <option value="Inovasi">Inovasi</option>
                        <option value="Kab/Kota">Kab/Kota</option>
                        <option value="Kegiatan">Kegiatan</option>
                        <option value="Keluarga">Keluarga</option>
                        <option value="Laporan">Laporan</option>
                        <option value="Layanan">Layanan</option>
                        <option value="Level">Level</option>
                        <option value="Nilai">Nilai</option>
                        <option value="Orang">Orang</option>
                        <option value="Paket">Paket</option>
                        <option value="Permil">Permil</option>
                        <option value="Persen">Persen</option>
                        <option value="Poin">Poin</option>
                        <option value="Rupiah">Rupiah</option>
                        <option value="Unit">Unit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arah</label>
                    <select wire:model="ind_arah" class="w-full border rounded px-3 py-2">
                        <option value="">Pilih Arah</option>
                        <option>Naik</option>
                        <option>Turun</option>
                        <option>Tetap</option>
                    </select>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3 bg-gray-50 rounded-b-xl">
                <button wire:click="closeModal" class="w-full sm:w-auto px-6 py-2 bg-gray-100 rounded">Batal</button>
                <button wire:click="storeIndikator" class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TARGET --}}
    @if($isOpenTarget)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl sm:mx-4 transform transition-all scale-100 flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-xl font-bold text-gray-800">Form Target Indikator</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Bagian konten scrollable untuk mobile --}}
            <div class="p-8 space-y-4 overflow-y-auto">
                @foreach([2025, 2026, 2027, 2028, 2029, 2030] as $year)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 items-start sm:items-center">
                    <div class="col-span-1 sm:col-span-3">
                        <label class="text-sm font-medium text-gray-700 block mb-1 sm:mb-0">Target {{ $year }}</label>
                    </div>
                    <div class="col-span-1 sm:col-span-9 relative">
                        <input type="text" wire:model="target_{{ $year }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none bg-gray-50 border-l border-gray-300 rounded-r-lg text-sm text-gray-500">
                            {{ $target_satuan ?? 'Angka' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3 bg-gray-50 rounded-b-xl flex-shrink-0">
                <button wire:click="closeModal" class="w-full sm:w-auto px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="simpanTarget" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>