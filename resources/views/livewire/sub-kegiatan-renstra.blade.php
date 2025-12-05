<div>
    <!-- HEADER HALAMAN -->
    <div class="relative -mt-20 mb-8 z-20">
        <h2 class="text-3xl font-bold text-white tracking-wide">Sub Kegiatan</h2>
        <p class="text-blue-100 text-sm mt-1">Master Data / Matrik Renstra / Program, Kegiatan & Sub Kegiatan / Kegiatan / Sub Kegiatan</p>
    </div>

    <!-- INFO BOX -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 relative z-10">
        <!-- Perangkat Daerah -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-sm font-bold text-gray-800 mb-1">Perangkat Daerah</h4>
            <p class="text-gray-600 text-sm">1.02.0.00.0.00.01.0000 DINAS KESEHATAN</p>
        </div>
        <!-- Program -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Program</h4>
            <p class="text-gray-600 text-sm uppercase font-medium">{{ $program->kode }} {{ $program->nama }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 relative z-10">
        <!-- Outcome -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Outcome</h4>
            <div class="text-gray-600 text-sm">
                @foreach($program->outcomes as $out) 
                    <div class="mb-1 flex items-start"><span class="mr-2">•</span><span>{{ $out->outcome }}</span></div> 
                @endforeach
            </div>
        </div>
        <!-- Kegiatan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Kegiatan</h4>
            <p class="text-gray-600 text-sm uppercase font-medium">{{ $kegiatan->kode }} {{ $kegiatan->nama }}</p>
        </div>
        <!-- Output -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Output</h4>
            <p class="text-gray-600 text-sm">{{ $kegiatan->output ?? '-' }}</p>
        </div>
    </div>

    <!-- TABEL SUB KEGIATAN -->
    <div class="space-y-8 relative z-10">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Sub Kegiatan</h3>
                
                <div class="flex gap-2">
                    <!-- Tombol Kembali -->
                    <a href="{{ route('matrik.kegiatan', ['id' => $program->id]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                    <!-- Tombol Tambah -->
                    <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Sub Kegiatan
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px]">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <!-- Header Baris 1: Judul Kolom Utama -->
                            <tr class="bg-white text-gray-700 font-bold border-b border-gray-200">
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle w-64">Sub Kegiatan</th>
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle w-48">Output</th>
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle w-48">Indikator</th>
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle w-24">Satuan</th>
                                <th colspan="12" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50">Periode</th>
                                <th rowspan="3" class="p-4 text-center align-middle w-24">Aksi</th>
                            </tr>
                            <!-- Header Baris 2: Tahun -->
                            <tr class="bg-white text-gray-700 font-bold border-b border-gray-200">
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50">2025</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center">2026</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50">2027</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center">2028</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50">2029</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center">2030</th>
                            </tr>
                            <!-- Header Baris 3: Target & Pagu -->
                            <tr class="bg-white text-gray-700 font-bold border-b border-gray-200 text-[10px]">
                                <th class="p-2 border-r text-center bg-gray-50">Target</th><th class="p-2 border-r text-center bg-gray-50">Pagu</th>
                                <th class="p-2 border-r text-center">Target</th><th class="p-2 border-r text-center">Pagu</th>
                                <th class="p-2 border-r text-center bg-gray-50">Target</th><th class="p-2 border-r text-center bg-gray-50">Pagu</th>
                                <th class="p-2 border-r text-center">Target</th><th class="p-2 border-r text-center">Pagu</th>
                                <th class="p-2 border-r text-center bg-gray-50">Target</th><th class="p-2 border-r text-center bg-gray-50">Pagu</th>
                                <th class="p-2 border-r text-center">Target</th><th class="p-2 border-r text-center">Pagu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-gray-600">
                            @forelse($sub_kegiatans as $sub)
                                @php
                                    $rowCount = $sub->indikators->count();
                                    $rowSpan = $rowCount > 0 ? $rowCount : 1;
                                @endphp

                                @if($rowCount > 0)
                                    <!-- Jika ada Indikator, looping indikatornya -->
                                    @foreach($sub->indikators as $index => $ind)
                                    <tr class="hover:bg-gray-50 border-b border-gray-100">
                                        <!-- Kolom Sub Kegiatan & Output (Merge Rows) -->
                                        @if($index === 0)
                                            <td rowspan="{{ $rowSpan }}" class="p-4 border-r align-top font-bold text-gray-800 bg-white">
                                                <div>{{ $sub->kode }}</div>
                                                <div class="mt-1">{{ $sub->nama }}</div>
                                                @if($sub->jabatan)
                                                    <div class="mt-2 inline-flex items-center px-2 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-[10px]">
                                                        PJ: {{ $sub->jabatan->nama }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td rowspan="{{ $rowSpan }}" class="p-4 border-r align-top bg-white">{{ $sub->output ?? '-' }}</td>
                                        @endif

                                        <!-- Kolom Indikator & Satuan (Per Baris) -->
                                        <td class="p-4 border-r text-gray-700 font-medium">{{ $ind->keterangan }}</td>
                                        <td class="p-4 border-r text-center">{{ $ind->satuan }}</td>
                                        
                                        <!-- Data Target & Pagu (DENGAN PERBAIKAN RP) -->
                                        <td class="p-2 border-r text-center bg-gray-50">{{ $ind->target_2025 ?? '-' }}</td> 
                                        <td class="p-2 border-r text-right text-[10px] bg-gray-50 whitespace-nowrap">{{ $ind->pagu_2025 ? 'Rp ' . number_format($ind->pagu_2025, 0, ',', '.') : '-' }}</td>
                                        
                                        <td class="p-2 border-r text-center">{{ $ind->target_2026 ?? '-' }}</td> 
                                        <td class="p-2 border-r text-right text-[10px] whitespace-nowrap">{{ $ind->pagu_2026 ? 'Rp ' . number_format($ind->pagu_2026, 0, ',', '.') : '-' }}</td>
                                        
                                        <td class="p-2 border-r text-center bg-gray-50">{{ $ind->target_2027 ?? '-' }}</td> 
                                        <td class="p-2 border-r text-right text-[10px] bg-gray-50 whitespace-nowrap">{{ $ind->pagu_2027 ? 'Rp ' . number_format($ind->pagu_2027, 0, ',', '.') : '-' }}</td>
                                        
                                        <td class="p-2 border-r text-center">{{ $ind->target_2028 ?? '-' }}</td> 
                                        <td class="p-2 border-r text-right text-[10px] whitespace-nowrap">{{ $ind->pagu_2028 ? 'Rp ' . number_format($ind->pagu_2028, 0, ',', '.') : '-' }}</td>
                                        
                                        <td class="p-2 border-r text-center bg-gray-50">{{ $ind->target_2029 ?? '-' }}</td> 
                                        <td class="p-2 border-r text-right text-[10px] bg-gray-50 whitespace-nowrap">{{ $ind->pagu_2029 ? 'Rp ' . number_format($ind->pagu_2029, 0, ',', '.') : '-' }}</td>
                                        
                                        <td class="p-2 border-r text-center">{{ $ind->target_2030 ?? '-' }}</td> 
                                        <td class="p-2 border-r text-right text-[10px] whitespace-nowrap">{{ $ind->pagu_2030 ? 'Rp ' . number_format($ind->pagu_2030, 0, ',', '.') : '-' }}</td>
                                        
                                        <!-- Kolom Aksi (Menu Dropdown Disederhanakan) -->
                                        <td class="p-4 text-center align-middle">
                                            <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                                <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm">
                                                    Menu <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                </button>
                                                <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 text-left">
                                                    <div class="py-1">
                                                        <!-- 1. Penanggung Jawab -->
                                                        <button wire:click="pilihPenanggungJawab({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors">
                                                            <svg class="mr-3 h-4 w-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                            Penanggung Jawab
                                                        </button>
                                                        
                                                        <!-- 2. Atur Target -->
                                                        <button wire:click="aturTarget({{ $ind->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-purple-600 hover:bg-purple-50 transition-colors">
                                                            <svg class="mr-3 h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                            Atur Target
                                                        </button>
                                                        
                                                        <div class="border-t border-gray-100 my-1"></div>

                                                        <!-- 3. Hapus Sub Kegiatan -->
                                                        <button wire:click="delete({{ $sub->id }})" wire:confirm="Hapus Sub Kegiatan Beserta Seluruh Indikatornya?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                            <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus Sub Kegiatan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <!-- Jika Tidak Ada Indikator -->
                                    <tr class="bg-white border-b border-gray-100">
                                        <td class="p-4 border-r align-top font-bold text-gray-800">
                                            <div>{{ $sub->kode }}</div>
                                            <div class="mt-1">{{ $sub->nama }}</div>
                                            @if($sub->jabatan)
                                                <div class="mt-2 inline-flex items-center px-2 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-[10px]">
                                                    PJ: {{ $sub->jabatan->nama }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4 border-r align-top">{{ $sub->output ?? '-' }}</td>
                                        <td colspan="14" class="p-4 border-r text-center text-gray-300 italic">Belum ada indikator</td>
                                        
                                        <!-- Kolom Aksi (Tanpa Atur Target karena tidak ada indikator) -->
                                        <td class="p-4 text-center align-middle">
                                            <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                                <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm">
                                                    Menu <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                </button>
                                                <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 text-left">
                                                    <div class="py-1">
                                                        <button wire:click="pilihPenanggungJawab({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors">
                                                            <svg class="mr-3 h-4 w-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                            Penanggung Jawab
                                                        </button>
                                                        
                                                        <div class="border-t border-gray-100 my-1"></div>

                                                        <button wire:click="delete({{ $sub->id }})" wire:confirm="Hapus?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                            <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Hapus Sub Kegiatan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td colspan="17" class="p-10 text-center text-gray-400 italic">Belum ada data Sub Kegiatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: SUB KEGIATAN -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit' : 'Tambah' }} Sub Kegiatan</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Sub Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="kode" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Contoh: 1.02.02.1.01.01">
                    @error('kode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sub Kegiatan <span class="text-red-500">*</span></label>
                    <textarea wire:model="nama" rows="3" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none resize-none" placeholder="Nama Sub Kegiatan"></textarea>
                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Output</label>
                    <textarea wire:model="output" rows="2" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none resize-none" placeholder="Output (Opsional)"></textarea>
                </div>

                <!-- Bagian Indikator (Hanya Tampil saat Tambah Baru) -->
                @if(!$isEditMode)
                <div class="pt-4 mt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Indikator Awal (Opsional)</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Indikator Kinerja</label>
                            <textarea wire:model="ind_keterangan" rows="2" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none resize-none" placeholder="Contoh: Jumlah Dokumen..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                            <select wire:model="ind_satuan" class="w-full border rounded px-3 py-2 text-sm bg-white focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="">Pilih Satuan</option>
                                <option>Dokumen</option>
                                <option>Kegiatan</option>
                                <option>Persen</option>
                                <option>Orang</option>
                                <option>Laporan</option>
                                <option>Kab/Kota</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded font-medium hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="store" wire:loading.attr="disabled" class="px-5 py-2.5 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 transition-colors flex items-center shadow-sm">
                    <span wire:loading.remove wire:target="store">Simpan</span>
                    <span wire:loading wire:target="store">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL 2: INDIKATOR -->
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-xl font-bold mb-4">{{ $isEditMode ? 'Edit' : 'Tambah' }} Indikator</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium">Keterangan</label>
                    <textarea wire:model="ind_keterangan" class="w-full border rounded px-3 py-2"></textarea>
                    @error('ind_keterangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Satuan</label>
                    <select wire:model="ind_satuan" class="w-full border rounded px-3 py-2 bg-white">
                        <option value="">Pilih Satuan</option>
                        <option>Dokumen</option>
                        <option>Kegiatan</option>
                        <option>Persen</option>
                        <option>Orang</option>
                        <option>Laporan</option>
                        <option>Kab/Kota</option>
                    </select>
                    @error('ind_satuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition-colors">Batal</button>
                <button wire:click="storeIndikator" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center">
                    <span wire:loading.remove wire:target="storeIndikator">Simpan</span>
                    <span wire:loading wire:target="storeIndikator">...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL 3: TARGET & PAGU (TAMPILAN DIPERBARUI) -->
    @if($isOpenTarget)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 p-0 overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Form Target Indikator</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Content Scrollable -->
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                <div class="space-y-6">
                    @foreach([2025, 2026, 2027, 2028, 2029, 2030] as $y)
                        <div class="grid grid-cols-1 gap-y-4 border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                            <!-- Target Row -->
                            <div class="grid grid-cols-12 gap-4 items-center">
                                <label class="col-span-3 text-sm font-medium text-gray-700">Target {{ $y }}</label>
                                <div class="col-span-9">
                                    <div class="flex rounded-md shadow-sm">
                                        <input type="text" wire:model="target_{{ $y }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0">
                                        <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            {{ $target_satuan ?? 'Satuan' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagu Row -->
                            <div class="grid grid-cols-12 gap-4 items-start">
                                <label class="col-span-3 text-sm font-medium text-gray-700 mt-2">Pagu {{ $y }}</label>
                                <div class="col-span-9">
                                    <div class="flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            Rp.
                                        </span>
                                        <input type="text" wire:model="pagu_{{ $y }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-md border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">Ketik angka saja; akan diformat otomatis.</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Batal
                </button>
                <button wire:click="simpanTarget" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 border border-transparent text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center shadow-sm">
                    <span wire:loading.remove wire:target="simpanTarget">Simpan</span>
                    <span wire:loading wire:target="simpanTarget">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <!-- MODAL 4: PENANGGUNG JAWAB -->
    @if($isOpenPJ)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-xl font-bold mb-4">Penanggung Jawab</h3>
            <div class="mb-4 text-sm italic bg-gray-50 p-3 rounded border text-gray-600">"{{ $pj_sub_kegiatan_text }}"</div>
            <div>
                <label class="text-sm font-medium block mb-2">Jabatan</label>
                <select wire:model="pj_jabatan_id" class="w-full border rounded px-3 py-2 bg-white focus:ring-blue-500 outline-none">
                    <option value="">Pilih Jabatan</option>
                    @foreach($jabatans as $j)<option value="{{$j->id}}">{{$j->nama}}</option>@endforeach
                </select>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition-colors">Batal</button>
                <button wire:click="simpanPenanggungJawab" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center">
                    <span wire:loading.remove wire:target="simpanPenanggungJawab">Simpan</span>
                    <span wire:loading wire:target="simpanPenanggungJawab">...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>