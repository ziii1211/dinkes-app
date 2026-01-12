<div>
    <div class="bg-white shadow-xl rounded-xl sm:rounded-[2.5rem] p-4 sm:p-8 md:p-12 border border-gray-100 relative z-10 space-y-6 sm:space-y-10">

        {{-- HEADER DAN INFO KARTU --}}
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
            <div class="w-full xl:w-auto">
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                        Perjanjian Kinerja (PK) • DINAS KESEHATAN
                    </h2>
                    
                    @if($pk->status_verifikasi == 'draft')
                        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1.5 rounded rounded-md border border-gray-200 uppercase tracking-wider">Draft</span>
                    @else
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded rounded-md uppercase tracking-wider shadow-sm">Terpublikasi</span>
                    @endif
                </div>

                {{-- ALERT SUKSES/GAGAL --}}
                @if (session()->has('message'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm" role="alert">
                        <p class="font-bold">Berhasil</p>
                        <p>{{ session('message') }}</p>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                        <p class="font-bold">Gagal</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                {{-- BREADCRUMB SCROLLABLE --}}
                <div class="overflow-x-auto whitespace-nowrap pb-2 w-full">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-2 text-sm text-gray-500 font-medium">
                            <li><a href="/" class="hover:text-blue-600 transition-colors">Dashboard</a></li>
                            <li class="text-gray-300">/</li>
                            <li><a href="{{ route('perjanjian.kinerja') }}" class="hover:text-blue-600 transition-colors">Perjanjian Kinerja</a></li>
                            <li class="text-gray-300">/</li>
                            <li class="text-gray-800 truncate max-w-[200px]">{{ $jabatan->nama ?? 'Detail' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 w-full xl:w-auto">
                <a href="{{ route('perjanjian.kinerja') }}" class="flex-1 xl:flex-none justify-center inline-flex items-center px-5 py-2.5 bg-[#6f42c1] hover:bg-[#5a32a3] text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
                
                <a href="{{ route('perjanjian.kinerja.print', $pk->id) }}" target="_blank" class="flex-1 xl:flex-none justify-center inline-flex items-center px-5 py-2.5 bg-[#ffc107] hover:bg-[#e0a800] text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Preview
                </a>
                
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="deletePk" wire:confirm="Hapus seluruh dokumen PK ini?" class="flex-1 xl:flex-none justify-center inline-flex items-center px-5 py-2.5 bg-[#f93154] hover:bg-[#d92644] text-white text-sm font-bold rounded-lg shadow-sm transition-all hover:shadow-md whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
                @endif
            </div>
        </div>

        {{-- INFO BOX --}}
        <div class="bg-gray-50/50 border border-gray-200 rounded-xl p-4 sm:p-6 xl:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-6 xl:gap-8">
                {{-- Detail Kiri --}}
                <div class="xl:col-span-6 space-y-3">
                    <div class="flex flex-col sm:grid sm:grid-cols-[120px_1fr] gap-1 sm:gap-2 text-sm">
                        <span class="text-gray-400 font-medium">PD:</span>
                        <span class="text-gray-900 font-bold">1.02.0.00.0.00.01.0000 — DINAS KESEHATAN</span>
                    </div>
                    <div class="flex flex-col sm:grid sm:grid-cols-[120px_1fr] gap-1 sm:gap-2 text-sm">
                        <span class="text-gray-400 font-medium">Jabatan PK:</span>
                        <span class="text-gray-900 font-bold">{{ $jabatan->nama ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:grid sm:grid-cols-[120px_1fr] gap-1 sm:gap-2 text-sm">
                        <span class="text-gray-400 font-medium">Tahun:</span>
                        <span class="text-gray-900 font-bold">{{ $pk->tahun }}</span>
                    </div>
                    <div class="flex flex-col sm:grid sm:grid-cols-[120px_1fr] gap-1 sm:gap-2 text-sm">
                        <span class="text-gray-400 font-medium">Keterangan:</span>
                        <span class="text-gray-900 font-bold">{{ $pk->keterangan ?? '-' }}</span>
                    </div>
                </div>

                {{-- Pihak 1 --}}
                <div class="xl:col-span-3 bg-white rounded-lg p-5 border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-800 mb-3">Pihak 1</h4>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500">{{ $jabatan->nama ?? 'Jabatan' }}</p>
                        <p class="text-sm font-bold text-gray-800 uppercase">{{ $pegawai->nama ?? '-' }}</p>
                        <p class="text-xs text-gray-400">NIP: {{ $pegawai->nip ?? '-' }}</p>
                        <p class="text-xs text-gray-400 mt-2">Status: Definitif</p>
                    </div>
                </div>

                {{-- Pihak 2 --}}
                <div class="xl:col-span-3 bg-white rounded-lg p-5 border border-gray-100 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-800 mb-3">Pihak 2</h4>
                    <div class="space-y-1">
                        @if($is_kepala_dinas)
                            <p class="text-xs text-gray-500">{{ $gubernur_jabatan }}</p>
                            <p class="text-sm font-bold text-gray-800 uppercase">{{ $gubernur_nama }}</p>
                        @elseif($atasan_pegawai)
                            <p class="text-xs text-gray-500">{{ $atasan_jabatan->nama ?? 'Atasan' }}</p>
                            <p class="text-sm font-bold text-gray-800 uppercase">{{ $atasan_pegawai->nama }}</p>
                        @else
                            <p class="text-sm italic text-gray-400 py-2">Belum diset</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">Status: Definitif</p>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->hasRole('admin'))
        <div class="flex flex-wrap items-center gap-4 py-2">
            <button wire:click="openModalKinerjaUtama" class="w-full sm:w-auto justify-center px-5 py-2.5 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-bold rounded rounded-md shadow-sm flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kinerja Utama
            </button>
            
            <button wire:click="ajukan" wire:confirm="Simpan dan Publikasikan data ini?" class="w-full sm:w-auto justify-center px-5 py-2.5 bg-[#f93154] hover:bg-[#d92644] text-white text-sm font-bold rounded rounded-md shadow-sm flex items-center transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Simpan & Publikasikan
            </button>
        </div>
        @endif

        {{-- DAFTAR KINERJA UTAMA --}}
        <div class="space-y-6 pt-4">
            @forelse($pk->sasarans as $index => $sasaran)
            <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: true }" wire:key="sasaran-{{ $sasaran->id }}">
                <div class="px-6 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/30 cursor-pointer" @click="open = !open">
                    <h4 class="text-sm font-bold text-blue-600">Kinerja Utama #{{ $index + 1 }}</h4>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5 transform transition-transform" :class="{'rotate-180': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>

                <div x-show="open" class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
                        <div class="flex-1">
                            <p class="text-xs text-gray-400 font-bold mb-1">Kinerja Utama <span class="text-red-500">*</span></p>
                            <p class="text-gray-900 font-bold text-base">{{ $sasaran->sasaran }}</p>
                        </div>
                        @if(auth()->user()->hasRole('admin'))
                        <button wire:click="deleteKinerjaUtama({{ $sasaran->id }})" wire:confirm="Hapus Kinerja Utama?" class="bg-[#f93154] hover:bg-[#d92644] text-white text-xs font-bold px-3 py-2 rounded shadow-sm flex items-center shrink-0 self-end sm:self-start">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                        @endif
                    </div>

                    {{-- TABEL INDIKATOR SCROLLABLE --}}
                    <div class="overflow-x-auto border border-gray-200 rounded">
                        <table class="w-full text-left text-sm whitespace-nowrap sm:whitespace-normal">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 font-bold text-gray-700 min-w-[250px] w-5/12">Indikator</th>
                                    <th class="px-4 py-3 font-bold text-gray-700 min-w-[100px] w-2/12 border-l border-gray-200 text-center">Satuan</th>
                                    <th class="px-4 py-3 font-bold text-gray-700 min-w-[100px] w-2/12 border-l border-gray-200 text-center">Target</th>
                                    <th class="px-4 py-3 font-bold text-gray-700 min-w-[80px] w-1/12 border-l border-gray-200 text-center">Arah</th>
                                    <th class="px-4 py-3 font-bold text-gray-700 min-w-[120px] w-2/12 border-l border-gray-200 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($sasaran->indikators as $ind)
                                <tr class="hover:bg-gray-50" wire:key="indikator-{{ $ind->id }}">
                                    <td class="px-4 py-3 text-gray-700 whitespace-normal">{{ $ind->nama_indikator }}</td>
                                    <td class="px-4 py-3 text-center border-l border-gray-100">{{ $ind->satuan }}</td>
                                    <td class="px-4 py-3 text-center border-l border-gray-100 font-bold text-gray-900">
                                        @php $col = 'target_'.$pk->tahun; echo $ind->$col ?? $ind->target; @endphp
                                    </td>
                                    <td class="px-4 py-3 text-center border-l border-gray-100">{{ $ind->arah }}</td>
                                    <td class="px-4 py-3 text-center border-l border-gray-100">
                                        @if(auth()->user()->hasRole('admin'))
                                        <div class="flex justify-center gap-2">
                                            <button wire:click="editTarget({{ $ind->id }})" class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded hover:bg-yellow-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <button wire:click="deleteIndikator({{ $ind->id }})" wire:confirm="Hapus?" class="inline-flex items-center px-2 py-1 bg-[#ffecec] text-[#dc3545] text-xs font-bold rounded hover:bg-red-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white border border-dashed border-gray-300 rounded p-8 text-center text-gray-500">
                Belum ada Kinerja Utama.
            </div>
            @endforelse
        </div>

        {{-- ANGGARAN --}}
        <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden mt-8">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-900 text-lg">Anggaran</h3>
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="openModalAnggaran" class="bg-[#0d6efd] hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah
                </button>
                @endif
            </div>
            
            <div class="p-0">
                 {{-- TABEL ANGGARAN SCROLLABLE --}}
                 <div class="overflow-x-auto">
                     <table class="w-full text-left text-sm whitespace-nowrap sm:whitespace-normal">
                        <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 w-16 text-center min-w-[50px]">No</th>
                                <th class="px-6 py-3 min-w-[300px]">Program / Kegiatan / Sub Kegiatan</th>
                                <th class="px-6 py-3 text-right w-48 min-w-[150px]">Anggaran</th>
                                <th class="px-6 py-3 w-24 text-center min-w-[100px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($pk->anggarans as $index => $ang)
                            <tr class="hover:bg-gray-50" wire:key="anggaran-{{ $ang->id }}">
                                <td class="px-6 py-4 text-center text-gray-500">{{ $index+1 }}</td>
                                <td class="px-6 py-4 whitespace-normal">
                                    @if($ang->subKegiatan)
                                        <div class="text-gray-900">
                                            <span class="mr-2 font-mono text-xs bg-gray-100 px-1 py-0.5 rounded">{{ $ang->subKegiatan->kode }}</span>
                                            {{ $ang->subKegiatan->nama }}
                                        </div>
                                    @else 
                                        <div class="text-gray-900">
                                            {{ $ang->nama_program_kegiatan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-gray-900 font-medium">
                                    Rp {{ number_format($ang->anggaran, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                     @if(auth()->user()->hasRole('admin'))
                                    <button wire:click="deleteAnggaran({{ $ang->id }})" wire:confirm="Hapus Anggaran?" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2 py-1 rounded">
                                        Hapus
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                             <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400">
                                    Belum ada anggaran.
                                </td>
                            </tr>
                            @endforelse

                            @if($pk->anggarans->count() > 0)
                            <tr class="bg-gray-50 font-bold text-gray-900 border-t-2 border-gray-100">
                                <td colspan="2" class="px-6 py-4 text-right">Total</td>
                                <td class="px-6 py-4 text-right">Rp {{ number_format($pk->anggarans->sum('anggaran'), 0, ',', '.') }}</td>
                                <td class="px-6 py-4"></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div> 

    {{-- MODAL KINERJA UTAMA --}}
    @if($isOpenKinerjaUtama)
    <div class="fixed inset-0 z-[99] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Tambah Kinerja Utama</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kinerja Utamamu</label>
                    {{-- PERBAIKAN: Gunakan key array ['id'] dan ['label'] dari Controller --}}
                    <select wire:model.live="sumber_kinerja_id" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">-- Pilih Kinerja --</option>
                        @foreach($list_sumber as $item)
                            <option value="{{ $item['id'] }}">
                                {{ $item['label'] }}
                            </option>
                        @endforeach
                    </select>

                    @if(empty($list_sumber) || $list_sumber->isEmpty())
                        @if($is_kepala_dinas)
                            <p class="text-xs text-red-500 mt-2 italic">*Tidak ada data Sasaran Renstra.</p>
                        @else
                            <p class="text-xs text-red-500 mt-2 italic">
                                *Tidak ada Outcome atau Output Kegiatan yang ditugaskan ke jabatan ini. 
                                <br>Silakan atur Penanggung Jawab di menu Renstra.
                            </p>
                        @endif
                    @endif
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded text-sm font-bold text-gray-600">Batal</button>
                <button wire:click="storeKinerjaUtama" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded hover:bg-blue-700">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL ANGGARAN --}}
    @if($isOpenAnggaran)
    <div class="fixed inset-0 z-[99] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Tambah Anggaran</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Program, Kegiatan & Sub Kegiatan</label>
                    <select wire:model="anggaran_pilihan_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih --</option>
                        
                        <optgroup label="PROGRAM">
                            @foreach($programs_dropdown as $prog)
                                <option value="program:{{ $prog->id }}">{{ $prog->kode }} {{ $prog->nama }}</option>
                            @endforeach
                        </optgroup>

                        <optgroup label="KEGIATAN">
                            @foreach($kegiatans_dropdown as $keg)
                                <option value="kegiatan:{{ $keg->id }}">{{ $keg->kode }} {{ $keg->nama }}</option>
                            @endforeach
                        </optgroup>

                        <optgroup label="SUB KEGIATAN">
                            @foreach($sub_kegiatans_dropdown as $sub)
                                <option value="sub:{{ $sub->id }}">{{ $sub->kode }} {{ $sub->nama }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nilai Anggaran (Rp)</label>
                    <input type="number" wire:model="anggaran_nilai" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="0">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded text-sm font-bold text-gray-600">Batal</button>
                <button wire:click="storeAnggaran" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded hover:bg-blue-700">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL EDIT TARGET --}}
    @if($isOpenEditTarget)
    <div class="fixed inset-0 z-[99] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Edit Target ({{ $pk->tahun }})</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Target Tahun {{ $pk->tahun }}</label>
                    <input type="text" wire:model="edit_target_nilai" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Isi target baru...">
                    @error('edit_target_nilai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded text-sm font-bold text-gray-600">Batal</button>
                <button wire:click="updateTarget" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </div>
    </div>
    @endif

</div>