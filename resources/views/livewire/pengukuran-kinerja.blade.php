<div>
    <x-slot:title>Pengukuran Bulanan</x-slot>
    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-blue-100 transition-colors">Main Menu</a>
            <span class="mx-2">/</span><span class="text-blue-200">Pengukuran Kinerja</span>
            <span class="mx-2">/</span><span class="text-white font-medium">Pengukuran Bulanan</span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-100 p-4 md:p-6 space-y-6">

        {{-- NOTIFIKASI SUKSES --}}
        @if (session()->has('message'))
            <div class="max-w-7xl mx-auto bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm relative flex justify-between items-center animate-fade-in-down mb-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="font-bold mr-1">Berhasil!</span>
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
                <button type="button" class="text-green-700 hover:text-green-900" wire:click="$refresh">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-2 gap-2">
            <h2 class="text-xl font-bold text-gray-800 border-l-4 border-blue-600 pl-3 flex flex-wrap items-center gap-2">
                Pengukuran Kinerja Bulanan 
                <span class="text-gray-300 font-light hidden sm:inline">|</span> 
                <span class="text-gray-500 font-normal text-sm md:text-base">{{ $jabatan->nama }}</span>
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
            {{-- INFORMASI TAHUN & JABATAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                {{-- Dropdown Tahun --}}
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50 relative" x-data="{ open: false, search: '' }">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Tahun</label>
                    <button @click="open = !open" @click.outside="open = false" type="button" class="flex items-center justify-between w-full bg-white border border-gray-200 text-gray-800 text-lg font-bold py-1 px-3 rounded hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all">
                        <span>{{ $tahun }}</span>
                        <svg class="w-4 h-4 text-gray-400 ml-2 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" class="absolute left-0 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden" style="display: none;">
                        <div class="p-2 border-b border-gray-100">
                            <input x-model="search" type="text" placeholder="Cari tahun..." class="w-full px-2 py-1 text-sm border border-gray-200 rounded focus:outline-none focus:border-blue-400 text-gray-600">
                        </div>
                        <div class="max-h-48 overflow-y-auto">
                            @foreach($availableYears as $y)
                                <button x-show="'{{ $y }}'.includes(search)" wire:click="setTahun({{ $y }}); open = false" type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50 transition-colors {{ $tahun == $y ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-700' }}">
                                    <span>{{ $y }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Informasi Unit Kerja --}}
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Perangkat Daerah</label>
                    <div class="text-sm font-bold text-gray-800 uppercase truncate">DINAS KESEHATAN</div>
                </div>
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Jabatan</label>
                    <div class="text-sm font-bold text-gray-800 truncate">{{ $jabatan->nama }}</div>
                </div>
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Penanggung Jawab Sekarang</label>
                    <div class="text-sm font-bold text-gray-800 truncate">{{ $pegawai->nama ?? '-' }}</div>
                </div>
            </div>

            {{-- STATISTIK --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8">
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center bg-white shadow-sm">
                    <span class="text-sm text-gray-500 font-medium">Total RHK</span>
                    <span class="text-xl font-bold text-gray-800">{{ $totalRhk }}</span>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center bg-white shadow-sm">
                    <span class="text-sm text-gray-500 font-medium">Total Indikator</span>
                    <span class="text-xl font-bold text-gray-800">{{ $totalIndikator }}</span>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center bg-white shadow-sm">
                    <span class="text-sm text-gray-500 font-medium">Indikator Terisi</span>
                    <div class="text-right">
                        <span class="text-xl font-bold text-gray-800">{{ $filledIndikator }}</span>
                        <span class="text-xs text-gray-400 block">({{ $persenTerisi }}%)</span>
                    </div>
                </div>
            </div>

            {{-- KONTROL BULAN & EXPORT --}}
            <div class="mb-4 flex flex-col xl:flex-row justify-between items-start xl:items-end gap-4 border-b border-gray-100 pb-6">
                <div class="w-full">
                    <label class="text-sm font-bold text-gray-700 block mb-2">Pilih Bulan Pengisian:</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($months as $index => $name)
                            <button wire:click="selectMonth({{ $index }})" class="px-3 md:px-4 py-1.5 text-xs font-medium rounded-full border transition-all shadow-sm {{ $selectedMonth == $index ? 'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-200' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-600' }}">{{ $name }}</button>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 w-full xl:w-auto mt-4 xl:mt-0">
                    {{-- TOMBOL EXPORT EXCEL --}}
                    @if(auth()->check() && auth()->user()->role !== 'pimpinan')
                        <button wire:click="downloadExcel" wire:loading.attr="disabled" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center justify-center gap-2 transition-colors w-full sm:w-auto">
                            <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <svg wire:loading class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Export Excel
                        </button>
                    @endif

                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <button wire:click="openAturJadwal" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center justify-center gap-2 transition-colors w-full sm:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Atur Tenggat
                        </button>
                    @endif
                </div>
            </div>

            {{-- STATUS JADWAL --}}
            @if($isScheduleOpen)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex flex-col sm:flex-row items-start gap-3 animate-fade-in-down mb-6">
                    <div class="p-2 bg-green-100 rounded-full text-green-600 mt-1 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-green-700 uppercase tracking-wide">Jadwal Pengisian Terbuka</h4>
                        <p class="text-lg md:text-xl font-extrabold text-green-800 mt-1">{{ $scheduleMessage }}</p>
                        @if(isset($deadlineDate))
                            <p class="text-xs font-bold text-green-700 mt-2 bg-green-100 w-fit px-2 py-1 rounded-md border border-green-200">Batas Waktu: {{ $deadlineDate }} WITA</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex flex-col sm:flex-row items-start gap-3 animate-fade-in-down mb-6">
                    <div class="p-2 bg-red-100 rounded-full text-red-600 mt-1 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-red-700 uppercase tracking-wide">Pengisian Terkunci</h4>
                        <p class="text-sm text-red-700 mt-1 font-semibold">{{ $scheduleMessage ?? 'Jadwal pengisian untuk bulan ini telah ditutup.' }}</p>
                        @if(auth()->user()->role === 'admin')
                            <p class="text-xs font-bold text-purple-600 mt-2 cursor-pointer hover:underline" wire:click="openAturJadwal">Klik tombol "Atur Tenggat" untuk membuka kembali jadwal.</p>
                        @else
                            <p class="text-xs text-red-500 italic mt-2">Hubungi admin jika Anda memerlukan perpanjangan waktu.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- TABEL RHK (Indikator & Realisasi) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 md:px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Rencana Hasil Kerja (RHK)</h3>
            </div>
            
            {{-- SCROLLABLE TABLE --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 min-h-[300px]">
                <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 min-w-[300px] w-1/3">Rencana Hasil Kerja &rarr; Indikator</th>
                            <th class="px-4 py-4 text-center min-w-[120px] w-32">Target</th>
                            <th class="px-4 py-4 text-center min-w-[120px] w-32">Realisasi</th>
                            <th class="px-4 py-4 text-center min-w-[100px] w-24">Capaian</th>
                            <th class="px-4 py-4 min-w-[200px] w-48">Catatan</th>
                            <th class="px-4 py-4 min-w-[200px] w-48 text-center">Tanggapan</th>
                            <th class="px-4 py-4 text-center min-w-[120px] w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @if($pk)
                            @foreach($pk->sasarans as $sasaran)
                                <tr class="bg-blue-50 border-b border-blue-100">
                                    <td colspan="7" class="px-6 py-3 font-bold text-gray-800 flex items-start gap-2 whitespace-normal">
                                        <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8M3 13l6-6m0 0l6 6m-6-6v12"></path></svg>
                                        {{ $sasaran->sasaran }}
                                    </td>
                                </tr>
                                @foreach($sasaran->indikators as $ind)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 pl-10 align-middle whitespace-normal">
                                            <div class="flex items-start gap-2">
                                                <span class="text-gray-400 mt-0.5">&rarr;</span>
                                                <span class="text-gray-700 font-medium">{{ $ind->nama_indikator }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center font-bold text-gray-800 align-middle">{{ $ind->target_tahunan }} {{ $ind->satuan }}</td>
                                        <td class="px-4 py-4 text-center align-middle text-gray-800">{{ $ind->realisasi_bulan ?? '-' }}</td>
                                        <td class="px-4 py-4 text-center align-middle text-gray-800">{{ $ind->capaian_bulan ?? '-' }}</td>
                                        <td class="px-4 py-4 text-gray-500 align-middle text-xs italic whitespace-normal">{{ $ind->catatan_bulan ?? '-' }}</td>
                                        
                                        {{-- KOLOM TANGGAPAN --}}
                                        <td class="px-4 py-4 align-middle text-center">
                                            <div class="text-xs text-gray-700 mb-2 font-medium {{ $ind->tanggapan_bulan ? 'block' : 'hidden' }} whitespace-normal">{{ $ind->tanggapan_bulan ?? '-' }}</div>
                                            <div class="{{ !$ind->tanggapan_bulan ? 'block' : 'hidden' }} text-gray-400 text-xs mb-2">-</div>
                                            
                                            @if($canComment)
                                                <button wire:click="openTanggapan({{ $ind->id }}, '{{ addslashes($ind->nama_indikator) }}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm flex items-center justify-center gap-1 mx-auto transition-colors">
                                                    {{ $ind->tanggapan_bulan ? 'Edit' : 'Beri Tanggapan' }}
                                                </button>
                                            @endif
                                        </td>

                                        {{-- KOLOM AKSI/REALISASI --}}
                                        <td class="px-4 py-4 text-center align-middle">
                                            @if($canEdit)
                                                <button wire:click="openRealisasi({{ $ind->id }}, '{{ addslashes($ind->nama_indikator) }}', '{{ $ind->target_tahunan }}', '{{ $ind->satuan }}', '{{ $ind->arah ?? '' }}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm flex items-center justify-center gap-1 mx-auto transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> 
                                                    {{ $ind->realisasi_bulan ? 'Edit' : 'Isi Realisasi' }}
                                                </button>
                                            @elseif(!$isScheduleOpen && auth()->user()->role === 'pegawai' && isset($pegawai) && auth()->user()->nip === $pegawai->nip)
                                                <button disabled class="px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold rounded flex items-center justify-center gap-1 mx-auto cursor-not-allowed border border-gray-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Terkunci
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @else
                            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data Perjanjian Kinerja (Final) untuk tahun {{ $tahun }}.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL RENCANA AKSI --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="text-lg font-bold text-gray-800">Rencana Aksi</h3>
                <div class="flex gap-2">
                    @if($canEdit)
                        <button wire:click="openTambahAksi" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg flex items-center gap-2 shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Manual
                        </button>
                    @elseif(!$isScheduleOpen && auth()->user()->role === 'pegawai' && isset($pegawai) && auth()->user()->nip === $pegawai->nip)
                         <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 text-xs font-bold rounded-lg flex items-center gap-2 cursor-not-allowed border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Terkunci
                        </button>
                    @endif
                </div>
            </div>
            
            {{-- SCROLLABLE TABLE --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 min-h-[200px]">
                <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 min-w-[250px] w-1/2">RENCANA AKSI</th>
                            <th class="px-4 py-4 text-center min-w-[100px] w-24">TARGET</th>
                            <th class="px-4 py-4 text-center min-w-[100px] w-24">SATUAN</th>
                            <th class="px-4 py-4 text-center min-w-[100px] w-24">REALISASI</th>
                            <th class="px-4 py-4 text-center min-w-[100px] w-24">CAPAIAN</th>
                            <th class="px-4 py-4 text-center min-w-[120px] w-32">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($rencanaAksis as $aksi)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 align-middle text-gray-800 font-medium leading-relaxed whitespace-normal">{{ $aksi->nama_aksi }}</td>
                                <td class="px-4 py-4 text-center align-middle font-bold text-blue-600 bg-blue-50 rounded-lg">{{ $aksi->target }}</td>
                                <td class="px-4 py-4 text-center align-middle text-gray-600"><span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold">{{ $aksi->satuan }}</span></td>
                                <td class="px-4 py-4 text-center align-middle text-gray-800 font-medium">{{ $aksi->realisasi_bulan ?? '-' }}</td>
                                <td class="px-4 py-4 text-center align-middle">
                                    @if($aksi->capaian_bulan !== null)
                                        <span class="px-2 py-1 rounded text-xs font-bold text-white bg-green-500">{{ $aksi->capaian_bulan }}%</span>
                                    @else - @endif
                                </td>
                                <td class="px-4 py-4 text-center align-middle">
                                    @if($canEdit)
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="openRealisasiAksi({{ $aksi->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm flex items-center justify-center gap-1 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                {{ $aksi->realisasi_bulan ? 'Edit' : 'Isi' }}
                                            </button>
                                            <button wire:click="deleteRencanaAksi({{ $aksi->id }})" wire:confirm="Yakin ingin menghapus rencana aksi ini?" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 border border-red-200 text-xs font-bold rounded shadow-sm flex items-center justify-center transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    @elseif(!$isScheduleOpen && auth()->user()->role === 'pegawai' && isset($pegawai) && auth()->user()->nip === $pegawai->nip)
                                        <button disabled class="px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold rounded flex items-center justify-center gap-1 mx-auto cursor-not-allowed border border-gray-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Terkunci
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data Rencana Aksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL PENJELASAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="text-lg font-bold text-gray-800">Penjelasan per Indikator Kinerja</h3>
                <div class="flex gap-2">
                    @if($canEdit)
                        <button wire:click="openTambahPenjelasan" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg flex items-center gap-2 shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Penjelasan
                        </button>
                    @elseif(!$isScheduleOpen && auth()->user()->role === 'pegawai' && isset($pegawai) && auth()->user()->nip === $pegawai->nip)
                        <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 text-xs font-bold rounded-lg flex items-center gap-2 cursor-not-allowed border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Terkunci
                        </button>
                    @endif
                </div>
            </div>

            {{-- SCROLLABLE TABLE --}}
            <div class="overflow-x-auto rounded-lg border border-gray-100 min-h-[200px]">
                <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center min-w-[50px]">NO</th>
                            <th class="px-4 py-4 w-1/3 min-w-[200px]">UPAYA</th>
                            <th class="px-4 py-4 w-1/3 min-w-[200px]">HAMBATAN</th>
                            <th class="px-4 py-4 w-1/3 min-w-[200px]">RENCANA TINDAK LANJUT</th>
                            <th class="px-4 py-4 w-32 text-center min-w-[100px]">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($penjelasans as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-4 align-middle text-gray-800 leading-relaxed whitespace-normal">{{ $item->upaya ?? '-' }}</td>
                                <td class="px-4 py-4 align-middle text-gray-800 leading-relaxed whitespace-normal">{{ $item->hambatan ?? '-' }}</td>
                                <td class="px-4 py-4 align-middle text-gray-800 leading-relaxed whitespace-normal">{{ $item->tindak_lanjut ?? '-' }}</td>
                                <td class="px-4 py-4 text-center align-middle">
                                    @if($canEdit)
                                        <button wire:click="hapusPenjelasan({{ $item->id }})" wire:confirm="Yakin ingin menghapus poin ini?" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 border border-red-200 text-xs font-bold rounded shadow-sm transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Locked</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data penjelasan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- MODAL-MODAL (Sama seperti sebelumnya, hanya memastikan responsif di mobile dengan max-width) --}}
    
    {{-- MODAL ATUR JADWAL --}}
    @if($isOpenAturJadwal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Atur Jadwal Pengisian</h3>
                <button wire:click="closeAturJadwal" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                    <p class="text-sm text-purple-700 font-medium">Anda sedang mengatur jadwal untuk bulan <span class="font-bold">{{ $months[$selectedMonth] }} {{ $tahun }}</span>.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" wire:model="formJadwalMulai" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Batas Akhir (Tenggat)</label>
                    <input type="date" wire:model="formJadwalSelesai" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeAturJadwal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="simpanJadwal" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow-sm">Simpan Jadwal</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TAMBAH RENCANA AKSI --}}
    @if($isOpenTambahAksi)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Tambah Rencana Aksi</h3>
                <button wire:click="closeTambahAksi" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Rencana Aksi</label>
                    <textarea wire:model="formAksiNama" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Melaksanakan koordinasi..."></textarea>
                    @error('formAksiNama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Target</label>
                        <input type="number" wire:model="formAksiTarget" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="100">
                        @error('formAksiTarget') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan</label>
                        <select wire:model="formAksiSatuan" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
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
                            <option value="Keluarga">Keluarga</option>
                            <option value="Kegiatan">Kegiatan</option>
                            <option value="Laporan">Laporan</option>
                            <option value="layanan">layanan</option>
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
                        @error('formAksiSatuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeTambahAksi" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="storeRencanaAksi" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL REALISASI INDIKATOR --}}
    @if($isOpenRealisasi)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Realisasi IKU</h3>
                <button wire:click="closeRealisasi" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Indikator Kinerja Utama</label>
                    <p class="text-sm font-bold text-gray-800 mb-2">{{ $indikatorNama }}</p>
                    <div class="flex gap-4">
                        <div><span class="text-xs text-gray-500 block">Target</span><span class="text-sm font-bold text-gray-900">{{ $indikatorTarget }}</span></div>
                        <div><span class="text-xs text-gray-500 block">Satuan</span><span class="text-sm font-bold text-gray-900">{{ $indikatorSatuan }}</span></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Realisasi Bulan Ini</label>
                    <input type="number" step="0.01" wire:model="realisasiInput" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('realisasiInput') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @if($showCapaianInput)
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 animate-fade-in-down">
                    <label class="block text-sm font-semibold text-yellow-800 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Capaian Manual
                    </label>
                    <p class="text-xs text-yellow-600 mb-2">Indikator ini memiliki arah <strong>menurun/negatif</strong>. Silakan isi capaian manual jika diperlukan.</p>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.01" wire:model="capaianInput" class="w-full border border-yellow-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-yellow-500 outline-none" placeholder="Contoh: 100">
                        <span class="font-bold text-gray-500">%</span>
                    </div>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                    <textarea wire:model="catatanInput" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeRealisasi" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="simpanRealisasi" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TANGGAPAN --}}
    @if($isOpenTanggapan)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Beri Tanggapan Pimpinan</h3>
                <button wire:click="closeTanggapan" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs font-bold text-blue-500 uppercase">Indikator</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $indikatorNama }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Tanggapan</label>
                    <textarea wire:model="tanggapanInput" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Berikan masukan atau arahan terkait capaian ini..."></textarea>
                    @error('tanggapanInput') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeTanggapan" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="simpanTanggapan" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">Simpan Tanggapan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL REALISASI AKSI --}}
    @if($isOpenRealisasiAksi)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Realisasi Rencana Aksi</h3>
                <button wire:click="closeRealisasiAksi" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Rencana Aksi</label>
                    <p class="text-sm font-bold text-gray-800 mb-2">{{ $aksiNama }}</p>
                    <div class="flex gap-4">
                        <div><span class="text-xs text-gray-500 block">Target</span><span class="text-sm font-bold text-gray-900">{{ $aksiTarget }}</span></div>
                        <div><span class="text-xs text-gray-500 block">Satuan</span><span class="text-sm font-bold text-gray-900">{{ $aksiSatuan }}</span></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Realisasi Bulan Ini</label>
                    <input type="number" step="1" wire:model="realisasiAksiInput" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('realisasiAksiInput') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeRealisasiAksi" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="simpanRealisasiAksi" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TAMBAH PENJELASAN --}}
    @if($isOpenTambahPenjelasan)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Tambah Penjelasan Kinerja</h3>
                <button wire:click="closeTambahPenjelasan" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upaya</label>
                    <textarea wire:model="formUpaya" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Uraikan upaya yang dilakukan..."></textarea>
                    @error('formUpaya') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hambatan</label>
                    <textarea wire:model="formHambatan" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Uraikan hambatan yang ditemui..."></textarea>
                    @error('formHambatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rencana Tindak Lanjut</label>
                    <textarea wire:model="formRtl" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Uraikan rencana tindak lanjut..."></textarea>
                    @error('formRtl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeTambahPenjelasan" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="simpanPenjelasan" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>