<div>

    <div class="space-y-8 relative z-10">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Outcome</h3>
                {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Outcome
                </button>
                @endif
            </div>

            <div class="p-6">
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-700 text-sm font-bold border-b border-gray-200">
                                <th rowspan="2" class="p-4 border-r border-gray-200 align-middle w-96">Outcome / Indikator Outcome</th>
                                <th colspan="6" class="p-4 border-b border-r border-gray-200 text-center align-middle">Periode</th>
                                {{-- HEADER AKSI HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <th rowspan="2" class="p-4 text-center align-middle w-32">Aksi</th>
                                @endif
                            </tr>
                            <tr class="bg-white text-gray-800 text-sm font-bold border-b border-gray-200">
                                <th class="p-4 border-r text-center w-24">2025</th>
                                <th class="p-4 border-r text-center w-24">2026</th>
                                <th class="p-4 border-r text-center w-24">2027</th>
                                <th class="p-4 border-r text-center w-24">2028</th>
                                <th class="p-4 border-r text-center w-24">2029</th>
                                <th class="p-4 border-r text-center w-24">2030</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-600">
                            @forelse($outcomes as $outcome)
                                <tr class="bg-white border-b border-gray-100">
                                    <td class="p-6 border-r border-gray-100 align-top">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex gap-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800 border border-green-200 h-6 whitespace-nowrap">
                                                    Outcome
                                                </span>
                                                <span class="text-gray-800 font-medium leading-relaxed">
                                                    {{ $outcome->outcome }}
                                                </span>
                                            </div>
                                            
                                            @php
                                                // Default (Kosong/Belum ada PJ) -> Abu-abu
                                                $pjBadgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                                $pjText = 'Belum ada PJ';

                                                if ($outcome->jabatan) {
                                                    $jabatanName = $outcome->jabatan->nama;
                                                    $pjText = 'PJ: ' . $jabatanName;
                                                    
                                                    // Cek Pegawai
                                                    $pegawai = $outcome->jabatan->pegawai;
                                                    
                                                    if ($pegawai) {
                                                        $status = strtolower($pegawai->status); // definitif, plt, plh
                                                        
                                                        if (str_contains($status, 'definitif')) {
                                                            // DEFINITIF -> HIJAU
                                                            $pjBadgeClass = 'bg-green-100 text-green-700 border-green-200';
                                                        } elseif (str_contains($status, 'plt') || str_contains($status, 'plh')) {
                                                            // PLT / PLH -> KUNING
                                                            $pjBadgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                                        } else {
                                                            // Status Lainnya -> Biru
                                                            $pjBadgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                                        }
                                                    } else {
                                                        // Ada Jabatan tapi Kosong -> Merah Muda/Warning
                                                         $pjBadgeClass = 'bg-red-50 text-red-600 border-red-100';
                                                    }
                                                }
                                            @endphp

                                            @if($outcome->jabatan)
                                                <div class="mt-1 ml-14 inline-flex items-center px-2 py-0.5 rounded text-xs border w-fit {{ $pjBadgeClass }}">
                                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    {{ $pjText }}
                                                </div>
                                            @endif
                                            </div>
                                    </td>
                                    <td colspan="6" class="p-4 border-r text-center text-gray-300 align-middle">&mdash;</td>
                                    
                                    {{-- TOMBOL MENU OUTCOME HANYA UNTUK ADMIN --}}
                                    @if(auth()->user()->hasRole('admin'))
                                    <td class="p-4 text-center align-middle relative">
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm">
                                                Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                            <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <button wire:click="pilihPenanggungJawab({{ $outcome->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors"><svg class="mr-3 h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>Penanggung Jawab</button>
                                                    <button wire:click="tambahIndikator({{ $outcome->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50"><svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Tambah Indikator</button>
                                                    <button wire:click="edit({{ $outcome->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-indigo-600 hover:bg-indigo-50"><svg class="mr-3 h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>Edit Outcome</button>
                                                    <button wire:click="delete({{ $outcome->id }})" wire:confirm="Hapus?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"><svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>

                                @foreach($outcome->indikators as $indikator)
                                <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="p-6 border-r border-gray-100 align-top pl-12"> 
                                        <div class="flex gap-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200 h-6 whitespace-nowrap">
                                                Indikator Outcome
                                            </span>
                                            <span class="text-gray-600 text-sm leading-relaxed">
                                                {{ $indikator->keterangan }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td class="p-4 border-r text-center text-gray-700 text-sm">{{ $indikator->target_2025 ? $indikator->target_2025 . ' ' . $indikator->satuan : '-' }}</td>
                                    <td class="p-4 border-r text-center text-gray-700 text-sm">{{ $indikator->target_2026 ? $indikator->target_2026 . ' ' . $indikator->satuan : '-' }}</td>
                                    <td class="p-4 border-r text-center text-gray-700 text-sm">{{ $indikator->target_2027 ? $indikator->target_2027 . ' ' . $indikator->satuan : '-' }}</td>
                                    <td class="p-4 border-r text-center text-gray-700 text-sm">{{ $indikator->target_2028 ? $indikator->target_2028 . ' ' . $indikator->satuan : '-' }}</td>
                                    <td class="p-4 border-r text-center text-gray-700 text-sm">{{ $indikator->target_2029 ? $indikator->target_2029 . ' ' . $indikator->satuan : '-' }}</td>
                                    <td class="p-4 border-r text-center text-gray-700 text-sm">{{ $indikator->target_2030 ? $indikator->target_2030 . ' ' . $indikator->satuan : '-' }}</td>

                                    {{-- TOMBOL MENU INDIKATOR HANYA UNTUK ADMIN --}}
                                    @if(auth()->user()->hasRole('admin'))
                                    <td class="p-4 text-center align-middle relative">
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm">
                                                Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                            <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <button wire:click="editIndikator({{ $indikator->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50"><svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Edit Indikator</button>
                                                    <button wire:click="aturTarget({{ $indikator->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-purple-600 hover:bg-purple-50"><svg class="mr-3 h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>Atur Target</button>
                                                    <button wire:click="deleteIndikator({{ $indikator->id }})" wire:confirm="Hapus indikator ini?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"><svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Indikator</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            @empty
                                <tr>
                                    {{-- COLSPAN 8 JIKA ADMIN, 7 JIKA BUKAN --}}
                                    <td colspan="{{ auth()->user()->hasRole('admin') ? 8 : 7 }}" class="p-10 text-center text-gray-400 italic bg-gray-50">
                                        Data Outcome belum tersedia. 
                                        @if(auth()->user()->hasRole('admin'))
                                            Silakan klik tombol <strong>+ Tambah Outcome</strong>.
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

    {{-- MODAL HANYA RENDER JIKA ADMIN --}}
    @if(auth()->user()->hasRole('admin'))
        @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Outcome' : 'Form Outcome' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sasaran Renstra <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select wire:model="sasaran_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none appearance-none bg-white text-gray-700 cursor-pointer">
                                <option value="">Pilih Sasaran Renstra</option>
                                @foreach($sasarans as $sasaran)
                                    <option value="{{ $sasaran->id }}">{{ $sasaran->sasaran }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg></div>
                        </div>
                        @error('sasaran_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                        <textarea wire:model="outcome" rows="4" placeholder="Keterangan" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none placeholder-gray-400"></textarea>
                        @error('outcome') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">Batal</button>
                    <button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
                </div>
            </div>
        </div>
        @endif

        @if($isOpenPJ)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Penanggung Jawab</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded border text-sm text-gray-600 italic">"{{ $pj_outcome_text }}"</div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <select wire:model="pj_jabatan_id" class="w-full border rounded px-4 py-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="">Pilih Jabatan</option>
                            @foreach($jabatans as $j) <option value="{{ $j->id }}">{{ $j->nama }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3"><button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded font-medium hover:bg-gray-200">Batal</button><button wire:click="simpanPenanggungJawab" class="px-5 py-2.5 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 shadow-sm">Simpan</button></div>
            </div>
        </div>
        @endif

        @if($isOpenIndikator)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Indikator' : 'Form Indikator Outcome' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                        <textarea wire:model="ind_keterangan" rows="3" placeholder="Keterangan" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                        @error('ind_keterangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Satuan Indikator <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select wire:model.live="ind_satuan" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white appearance-none cursor-pointer">
                                <option value="">-- Pilih Satuan --</option>
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
                                <option value="Laporan">Laporan</option>
                                <option value="Level">Level</option>
                                <option value="Nilai">Nilai</option>
                                <option value="Orang">Orang</option>
                                <option value="Paket">Paket</option>
                                <option value="Per 1000 Penduduk">Per 1000 Penduduk</option>
                                <option value="Permil">Permil</option>
                                <option value="Persen">Persen</option>
                                <option value="Poin">Poin</option>
                                <option value="Rupiah">Rupiah</option>
                                <option value="Unit">Unit</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg></div>
                        </div>
                        @error('ind_satuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Arah <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select wire:model="ind_arah" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white appearance-none cursor-pointer">
                                <option value="">Pilih Arah</option>
                                <option value="Meningkat">Naik</option>
                                <option value="Menurun">Turun</option>
                                <option value="Tetap">Tetap</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500"><svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg></div>
                        </div>
                        @error('ind_arah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 rounded-b-xl">
                    <button wire:click="closeModal" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">Batal</button>
                    <button wire:click="storeIndikator" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
                </div>
            </div>
        </div>
        @endif

        @if($isOpenTarget)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Form Target Indikator</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="space-y-4 p-2 max-h-[60vh] overflow-y-auto">
                    @foreach([2025, 2026, 2027, 2028, 2029, 2030] as $year)
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-3"><label class="text-sm font-medium text-gray-700">Target {{ $year }}</label></div>
                        <div class="col-span-9 relative">
                            <input type="text" wire:model="target_{{ $year }}" class="w-full border rounded px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 bg-gray-50 border-l text-sm text-gray-500">{{ $target_satuan ?? 'Angka' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end gap-3"><button wire:click="closeModal" class="px-4 py-2 bg-gray-100 rounded">Batal</button><button wire:click="simpanTarget" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button></div>
            </div>
        </div>
        @endif
    @endif
</div>