<div>
    <x-slot:title>Detail Perjanjian Kinerja</x-slot>
    
    <x-slot:breadcrumb>
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Perencanaan</span>
        <span class="mx-2">/</span>
        <a href="{{ route('perjanjian.kinerja') }}" class="text-blue-200 hover:text-white" wire:navigate>Perjanjian Kinerja</a>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">{{ $jabatan->nama ?? 'Detail' }}</span>
    </x-slot>

    <!-- NOTIFIKASI SUKSES/ERROR -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 shadow-sm flex items-center animate-fade-in-down">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- PANEL KHUSUS PIMPINAN (ACTION BAR) -->
    <!-- ========================================================= -->
    @if(auth()->user()->role == 'pimpinan')
        
        <!-- JIKA STATUS MASIH DRAFT (Pegawai Belum Mengajukan) -->
        @if($pk->status_verifikasi == 'draft')
        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-r-xl shadow-sm mb-8">
            <div class="flex items-start gap-4">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Mode Monitoring (Draft)</h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Dokumen ini masih dalam status <strong>Draft</strong>. Pegawai pengampu ({{ $pegawai->nama ?? 'Pegawai' }}) belum melakukan "Simpan & Publikasi".<br>
                        Anda belum dapat melakukan verifikasi sampai dokumen diajukan.
                    </p>
                </div>
            </div>
        </div>

        <!-- JIKA STATUS PENDING (SIAP VERIFIKASI) -->
        @elseif($pk->status_verifikasi == 'pending')
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-xl shadow-lg mb-8 animate-fade-in-down">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-yellow-100 text-yellow-600 rounded-full shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Menunggu Verifikasi Anda</h3>
                        <p class="text-gray-600 text-sm mt-1">Dokumen ini telah diajukan oleh pegawai. Silakan periksa target kinerja di bawah, lalu tentukan keputusan.</p>
                    </div>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <button wire:click="openModalVerifikasi('ditolak')" class="flex-1 md:flex-none px-5 py-2.5 bg-white border border-red-300 text-red-600 font-bold rounded-lg hover:bg-red-50 transition-colors flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Tolak / Revisi
                    </button>
                    <button wire:click="openModalVerifikasi('disetujui')" class="flex-1 md:flex-none px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center shadow-md transform hover:scale-105 duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Setujui (Final)
                    </button>
                </div>
            </div>
        </div>
        
        <!-- JIKA SUDAH FINAL -->
        @elseif($pk->status_verifikasi == 'disetujui')
        <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl shadow-sm mb-8">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-green-100 text-green-600 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Dokumen Telah Disetujui</h3>
                    <p class="text-green-700 text-sm mt-1">
                        Diverifikasi pada: <strong>{{ \Carbon\Carbon::parse($pk->tanggal_verifikasi)->translatedFormat('d F Y H:i') }}</strong>
                    </p>
                </div>
            </div>
        </div>
        @endif

    @endif

    <!-- HEADER CARD INFORMASI DOKUMEN -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            
            <!-- Judul & Badge Status -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                <h2 class="text-lg font-bold text-gray-800 uppercase">
                    PK • DINAS KESEHATAN
                </h2>
                
                @if($pk->status_verifikasi == 'draft')
                    <span class="w-fit bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded border border-gray-300 uppercase">DRAFT</span>
                @elseif($pk->status_verifikasi == 'pending')
                    <span class="w-fit bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded border border-yellow-300 uppercase flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>
                        MENUNGGU VERIFIKASI
                    </span>
                @elseif($pk->status_verifikasi == 'disetujui')
                    <span class="w-fit bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded border border-green-300 uppercase flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        FINAL / DISETUJUI
                    </span>
                @elseif($pk->status_verifikasi == 'ditolak')
                    <span class="w-fit bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded border border-red-300 uppercase">DITOLAK / REVISI</span>
                @endif
            </div>

            <!-- Tombol Aksi Header -->
            <div class="flex gap-2 w-full md:w-auto">
                <a href="{{ route('perjanjian.kinerja') }}" wire:navigate class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
                
                <a href="{{ route('perjanjian.kinerja.print', $pk->id) }}" target="_blank" class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-medium rounded-lg transition-colors shadow-sm text-shadow">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Preview Cetak
                </a>
                
                <!-- Hapus hanya untuk Pegawai -->
                @if(in_array($pk->status_verifikasi, ['draft', 'ditolak']) && auth()->user()->role == 'pegawai')
                <button wire:click="deletePk" wire:confirm="Hapus PK ini?" class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    Hapus
                </button>
                @endif
            </div>
        </div>
        
        <!-- CATATAN PIMPINAN (JIKA ADA & DITOLAK) -->
        @if($pk->catatan_pimpinan && $pk->status_verifikasi == 'ditolak')
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-pulse">
            <p class="text-xs font-bold text-red-600 uppercase mb-1 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                Catatan Revisi dari Pimpinan:
            </p>
            <p class="text-gray-800 text-sm font-medium italic">"{{ $pk->catatan_pimpinan }}"</p>
        </div>
        @endif
    </div>

    <!-- 3 KOLOM INFO (PIHAK 1, 2, DETAIL) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Kolom Detail -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-3 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gray-300"></div>
            <div><span class="text-xs text-gray-400 font-medium uppercase">PD:</span><span class="text-sm font-bold text-gray-800 ml-1">DINAS KESEHATAN</span></div>
            <div><span class="text-xs text-gray-400 font-medium uppercase">Jabatan PK:</span><span class="text-sm font-bold text-blue-600 ml-1">{{ $jabatan->nama }}</span></div>
            <div><span class="text-xs text-gray-400 font-medium uppercase">Tahun:</span><span class="text-sm font-bold text-gray-800 ml-1">{{ $pk->tahun }}</span></div>
            <div><span class="text-xs text-gray-400 font-medium uppercase">Keterangan:</span><span class="text-sm font-bold text-gray-800 ml-1">{{ $pk->keterangan }}</span></div>
        </div>
        
        <!-- Kolom Pihak 1 (Pegawai) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
            <div class="flex justify-between items-start">
                <h4 class="font-bold text-gray-800 mb-3">Pihak 1</h4>
                <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-bold uppercase">Pembuat</span>
            </div>
            @if($pegawai)
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 border border-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-800 uppercase leading-tight">{{ $pegawai->nama }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">NIP: {{ $pegawai->nip }}</div>
                    </div>
                </div>
            @else <span class="text-sm text-red-500 italic">Belum ada pejabat.</span> @endif
        </div>

        <!-- Kolom Pihak 2 (Atasan/Pimpinan) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-green-500"></div>
            <div class="flex justify-between items-start">
                <h4 class="font-bold text-gray-800 mb-3">Pihak 2 (Atasan)</h4>
                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold uppercase">Verifikator</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 border border-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    @if($is_kepala_dinas)
                        <div class="text-sm font-bold text-gray-800 uppercase leading-tight">{{ $gubernur_nama }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $gubernur_jabatan }}</div>
                    @elseif($atasan_pegawai)
                        <div class="text-sm font-bold text-gray-800 uppercase leading-tight">{{ $atasan_pegawai->nama }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $atasan_jabatan->nama ?? 'Atasan' }}</div>
                    @else <span class="text-sm text-gray-400 italic">Tidak ada atasan langsung.</span> @endif
                </div>
            </div>
        </div>
    </div>

    <!-- AREA SASARAN & BUTTONS -->
    @if(auth()->user()->role == 'pegawai')
        @if(in_array($pk->status_verifikasi, ['draft', 'ditolak']))
        <div class="flex flex-col sm:flex-row items-center gap-3 mb-6 bg-white p-4 rounded-xl border border-blue-100 shadow-sm">
            <div class="flex-1">
                <h4 class="font-bold text-gray-800">Aksi Pegawai</h4>
                <p class="text-xs text-gray-500">Lengkapi data kinerja utama, anggaran, lalu publikasikan.</p>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <button wire:click="openModalKinerjaUtama" class="flex-1 sm:flex-none bg-white border border-blue-600 text-blue-600 hover:bg-blue-50 text-sm font-bold px-4 py-2 rounded-lg shadow-sm flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tambah Kinerja
                </button>
                <button wire:click="ajukan" wire:confirm="Kirim data ke Pimpinan? Data tidak bisa diedit selama proses verifikasi." class="flex-1 sm:flex-none bg-pink-500 hover:bg-pink-600 text-white text-sm font-bold px-4 py-2 rounded-lg shadow-md flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Simpan & Publikasikan
                </button>
            </div>
        </div>
        @endif

        @if($pk->status_verifikasi == 'pending')
        <div class="mb-6 flex items-center text-yellow-800 bg-yellow-50 px-4 py-3 rounded-lg border border-yellow-200 shadow-sm">
            <svg class="w-5 h-5 mr-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <div class="flex-1">
                <span class="font-bold">Sedang Diverifikasi Pimpinan.</span> Anda tidak dapat mengedit data saat ini.
            </div>
            <button wire:click="batalkan" wire:confirm="Batalkan pengajuan? Status akan kembali menjadi Draft." class="ml-4 text-xs font-bold text-yellow-700 underline hover:text-yellow-900">Batalkan Pengajuan</button>
        </div>
        @endif
    @endif

    <!-- LIST SASARAN -->
    @if($pk->sasarans->count() == 0)
    <div class="bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-300 min-h-[250px] flex flex-col items-center justify-center p-8 text-center mb-8">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        </div>
        <h3 class="text-gray-900 font-bold text-base mb-1">Belum ada Kinerja Utama</h3>
        @if(in_array($pk->status_verifikasi, ['draft', 'ditolak']) && auth()->user()->role == 'pegawai')
        <p class="text-gray-500 text-sm mb-4">Klik tombol <strong>"Tambah Kinerja"</strong> di atas untuk memulai.</p>
        @else
        <p class="text-gray-500 text-sm">Pegawai belum menginput data kinerja utama.</p>
        @endif
    </div>
    @else
    <div class="space-y-6 mb-8">
        <h3 class="font-bold text-gray-800 text-lg flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Detail Kinerja Utama & Indikator
        </h3>
        
        @foreach($pk->sasarans as $index => $sasaran)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow" x-data="{ expanded: true }">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center cursor-pointer" @click="expanded = !expanded">
                <h4 class="text-sm font-bold text-blue-700 uppercase tracking-wide flex items-center">
                    <span class="bg-blue-200 text-blue-800 text-[10px] px-2 py-0.5 rounded mr-3">#{{ $index + 1 }}</span>
                    Kinerja Utama
                </h4>
                <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            
            <div x-show="expanded" class="p-6">
                <div class="flex justify-between items-start gap-4 mb-6">
                    <div class="flex-1">
                        <p class="text-base font-bold text-gray-800 leading-relaxed">{{ $sasaran->sasaran }}</p>
                    </div>
                    @if(in_array($pk->status_verifikasi, ['draft', 'ditolak']) && auth()->user()->role == 'pegawai')
                    <button wire:click="deleteKinerjaUtama({{ $sasaran->id }})" wire:confirm="Hapus Sasaran ini?" class="text-red-500 hover:text-red-700 text-xs font-bold bg-red-50 px-2 py-1 rounded">Hapus Sasaran</button>
                    @endif
                </div>

                <!-- TABEL INDIKATOR -->
                <div class="overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-blue-50 text-blue-800 text-xs uppercase font-bold">
                            <tr>
                                <th class="px-4 py-3 w-5/12">Indikator Kinerja</th>
                                <th class="px-4 py-3 w-2/12 text-center">Satuan</th>
                                <th class="px-4 py-3 w-2/12 text-center">Target {{ $pk->tahun }}</th>
                                <th class="px-4 py-3 w-1/12 text-center">Arah</th>
                                <th class="px-4 py-3 w-2/12 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sasaran->indikators as $ind)
                            <tr wire:key="ind-{{ $ind->id }}" class="hover:bg-gray-50">
                                @if($editingIndikatorId === $ind->id && auth()->user()->role == 'pegawai')
                                    <!-- MODE EDIT -->
                                    <td class="px-4 py-3 text-gray-800 font-medium">{{ $ind->nama_indikator }}</td>
                                    <td class="px-4 py-3 text-center">{{ $ind->satuan }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="text" wire:model="editTargetValue" class="w-24 border border-blue-300 rounded px-2 py-1 text-center text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $ind->arah }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-1">
                                            <button wire:click="saveEdit" class="text-white bg-green-500 hover:bg-green-600 px-2 py-1 rounded text-xs font-bold">Simpan</button>
                                            <button wire:click="cancelEdit" class="text-gray-600 bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded text-xs font-bold">Batal</button>
                                        </div>
                                    </td>
                                @else
                                    <!-- MODE TAMPIL -->
                                    <td class="px-4 py-3 text-gray-800 font-medium">{{ $ind->nama_indikator }}</td>
                                    <td class="px-4 py-3 text-center text-gray-500 bg-gray-50">{{ $ind->satuan }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-blue-600 bg-blue-50">
                                        @php $col = 'target_'.$pk->tahun; echo $ind->$col ?? $ind->target; @endphp
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500">{{ $ind->arah }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if(in_array($pk->status_verifikasi, ['draft', 'ditolak']) && auth()->user()->role == 'pegawai')
                                        <div class="flex justify-center gap-2">
                                            <button wire:click="startEdit({{ $ind->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-bold flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit
                                            </button>
                                            <button wire:click="deleteIndikator({{ $ind->id }})" wire:confirm="Hapus?" class="text-red-500 hover:text-red-700 text-xs font-bold flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus
                                            </button>
                                        </div>
                                        @else
                                        <span class="text-xs text-gray-400 italic">Locked</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- SECTION ANGGARAN -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-12">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
            <h3 class="font-bold text-gray-800 text-lg flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Anggaran Program / Kegiatan
            </h3>
            @if(in_array($pk->status_verifikasi, ['draft', 'ditolak']) && auth()->user()->role == 'pegawai')
            <button wire:click="openModalAnggaran" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Anggaran
            </button>
            @endif
        </div>
        <div class="p-6">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Program / Kegiatan / Sub Kegiatan</th>
                        <th class="px-4 py-3 w-48 text-right">Anggaran</th>
                        <th class="px-4 py-3 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pk->anggarans as $index => $ang)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center text-gray-500">{{ $index+1 }}</td>
                        <td class="px-4 py-3 text-gray-800">
                            @if($ang->subKegiatan)
                                <div class="font-bold text-blue-600 text-xs">{{ $ang->subKegiatan->kode }}</div>
                                <div class="font-medium">{{ $ang->subKegiatan->nama }}</div>
                            @else <span class="text-gray-400">-</span> @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 bg-gray-50">Rp {{ number_format($ang->anggaran, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if(in_array($pk->status_verifikasi, ['draft', 'ditolak']) && auth()->user()->role == 'pegawai')
                            <button wire:click="deleteAnggaran({{ $ang->id }})" wire:confirm="Hapus?" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2 py-1 rounded">Hapus</button>
                            @else
                            <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-8 text-center text-gray-400 italic bg-gray-50 rounded-lg">Belum ada anggaran yang diinput.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL VERIFIKASI PIMPINAN -->
    @if($isOpenVerifikasi)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all animate-fade-in-down">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">
                    {{ $verifikasiStatusTemp == 'disetujui' ? 'Verifikasi & Setujui' : 'Tolak & Kembalikan' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 p-3 rounded-lg flex gap-3 items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-blue-800 text-sm">
                        @if($verifikasiStatusTemp == 'disetujui')
                            Anda akan menyetujui Perjanjian Kinerja ini. Status dokumen akan berubah menjadi <strong>FINAL</strong> dan tidak dapat diubah lagi oleh pegawai.
                        @else
                            Anda akan menolak dokumen ini. Dokumen akan dikembalikan ke status <strong>DRAFT</strong> agar pegawai dapat melakukan perbaikan.
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Pimpinan (Opsional)</label>
                    <textarea wire:model="catatan_pimpinan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="Tulis catatan atau arahan untuk pegawai..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                <button wire:click="simpanVerifikasi" class="px-4 py-2 text-white font-bold rounded-lg shadow-sm {{ $verifikasiStatusTemp == 'disetujui' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                    {{ $verifikasiStatusTemp == 'disetujui' ? 'Ya, Setujui Dokumen' : 'Kirim Penolakan' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL TAMBAH KINERJA UTAMA -->
    @if($isOpenKinerjaUtama)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Pilih Kinerja Utama</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sub Kegiatan</label>
                    <select wire:model.live="sub_kegiatan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Sub Kegiatan --</option>
                        @foreach($sub_kegiatans as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->nama }}</option>
                        @endforeach
                    </select>
                </div>
                @if($selected_output)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-xs font-bold text-blue-500 uppercase mb-1">Output (Sasaran Kinerja)</p>
                    <p class="text-sm font-bold text-gray-800">{{ $selected_output }}</p>
                </div>
                @endif
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                <button wire:click="storeKinerjaUtama" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL ANGGARAN -->
    @if($isOpenAnggaran)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tambah Anggaran</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sub Kegiatan</label>
                    <select wire:model="anggaran_sub_kegiatan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih --</option>
                        @foreach($sub_kegiatans as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nilai Anggaran (Rp)</label>
                    <input type="number" wire:model="anggaran_nilai" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="0">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                <button wire:click="storeAnggaran" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

</div>