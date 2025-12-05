<div>
    <x-slot:title>Pengukuran Bulanan</x-slot>
    <x-slot:breadcrumb>
        <a href="/" class="hover:text-blue-100 transition-colors">Main Menu</a>
        <span class="mx-2">/</span><span class="text-blue-200">Pengukuran Kinerja</span>
        <span class="mx-2">/</span><span class="text-white font-medium">Pengukuran Bulanan</span>
    </x-slot>

    <div class="min-h-screen bg-gray-100 p-6 space-y-6">

        @if (session()->has('message'))
            <div class="max-w-7xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative flex justify-between items-center animate-fade-in-down">
                <span class="block sm:inline">{{ session('message') }}</span>
                <button type="button" class="text-green-700" onclick="this.parentElement.remove()"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
        @endif

        <div class="flex justify-between items-end mb-2">
            <h2 class="text-xl font-bold text-gray-800 border-l-4 border-blue-600 pl-3">Pengukuran Kinerja Bulanan <span class="text-gray-300 font-light mx-2">|</span> <span class="text-gray-500 font-normal">{{ $jabatan->nama }}</span></h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Tahun</label>
                    <div class="text-lg font-bold text-gray-800">{{ $tahun }}</div>
                </div>
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Perangkat Daerah</label>
                    <div class="text-sm font-bold text-gray-800 uppercase">DINAS KESEHATAN</div>
                </div>
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Jabatan</label>
                    <div class="text-sm font-bold text-gray-800">{{ $jabatan->nama }}</div>
                </div>
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <label class="text-xs text-gray-400 uppercase font-bold mb-1 block">Penanggung Jawab Sekarang</label>
                    <div class="text-sm font-bold text-gray-800">{{ $pegawai->nama ?? '-' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Total RHK</span>
                    <span class="text-xl font-bold text-gray-800">{{ $totalRhk }}</span>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Total Indikator</span>
                    <span class="text-xl font-bold text-gray-800">{{ $totalIndikator }}</span>
                </div>
                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Indikator Terisi</span>
                    <div class="text-right">
                        <span class="text-xl font-bold text-gray-800">{{ $filledIndikator }}</span>
                        <span class="text-xs text-gray-400 block">({{ $persenTerisi }}% dari indikator)</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-sm font-bold text-gray-700 block mb-2">Pilih Bulan Pengisian:</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($months as $index => $name)
                        <button wire:click="selectMonth({{ $index }})" class="px-4 py-1.5 text-xs font-medium rounded-full border transition-all shadow-sm {{ $selectedMonth == $index ? 'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-200' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-600' }}">{{ $name }}</button>
                    @endforeach
                </div>
            </div>

            @php $currentMonthIndex = (int) date('n'); $isScheduleOpen = ($selectedMonth == $currentMonthIndex); $monthName = $months[$selectedMonth]; @endphp
            @if($isScheduleOpen)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3 animate-fade-in-down">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="text-sm font-bold text-green-700">Jadwal Penginputan Terbuka</h4>
                        <p class="text-xs text-green-600 mt-1">Pengukuran kinerja untuk <span class="font-bold">{{ $monthName }} {{ $tahun }}</span> sedang <span class="font-bold">dibuka</span>.</p>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start gap-3 animate-fade-in-down">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h4 class="text-sm font-bold text-yellow-700">Bukan Jadwal Penginputan</h4>
                        <p class="text-xs text-yellow-600 mt-1">Pengukuran kinerja untuk <span class="font-bold">{{ $monthName }} {{ $tahun }}</span> saat ini <span class="font-bold">tidak dibuka</span>.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Rencana Hasil Kerja (RHK)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 w-1/3">Rencana Hasil Kerja &rarr; Indikator</th>
                            <th class="px-4 py-4 text-center w-32">Target</th>
                            <th class="px-4 py-4 text-center w-32">Realisasi</th>
                            <th class="px-4 py-4 text-center w-24">Capaian</th>
                            <th class="px-4 py-4 w-48">Catatan</th>
                            <th class="px-4 py-4 w-48 text-center">Tanggapan</th>
                            <th class="px-4 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @if($pk)
                            @foreach($pk->sasarans as $sasaran)
                                <tr class="bg-blue-50 border-b border-blue-100"><td colspan="7" class="px-6 py-3 font-bold text-gray-800 flex items-start gap-2"><svg class="w-4 h-4 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8M3 13l6-6m0 0l6 6m-6-6v12"></path></svg>{{ $sasaran->sasaran }}</td></tr>
                                @foreach($sasaran->indikators as $ind)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 pl-10 align-middle"><div class="flex items-center gap-2"><span class="text-gray-400">&rarr;</span><span class="text-gray-700 font-medium">{{ $ind->nama_indikator }}</span></div></td>
                                        <td class="px-4 py-4 text-center font-bold text-gray-800 align-middle">{{ $ind->target_tahunan }} {{ $ind->satuan }}</td>
                                        <td class="px-4 py-4 text-center align-middle text-gray-800">{{ $ind->realisasi_bulan ?? '-' }}</td>
                                        <td class="px-4 py-4 text-center align-middle text-gray-800">{{ $ind->capaian_bulan ?? '-' }}</td>
                                        <td class="px-4 py-4 text-gray-500 align-middle text-xs italic">{{ $ind->catatan_bulan ?? '-' }}</td>
                                        
                                        <td class="px-4 py-4 align-middle text-center">
                                            <div class="text-xs text-gray-700 mb-2 font-medium {{ $ind->tanggapan_bulan ? 'block' : 'hidden' }}">
                                                {{ $ind->tanggapan_bulan ?? '-' }}
                                            </div>
                                            
                                            <div class="{{ !$ind->tanggapan_bulan ? 'block' : 'hidden' }} text-gray-400 text-xs mb-2">-</div>
                                            
                                            {{-- Tombol Edit hanya untuk Pimpinan --}}
                                            @if(auth()->check() && auth()->user()->role === 'pimpinan')
                                                <button wire:click="openTanggapan({{ $ind->id }}, '{{ addslashes($ind->nama_indikator) }}')" 
                                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm flex items-center justify-center gap-1 mx-auto transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                    {{ $ind->tanggapan_bulan ? 'Edit' : 'Beri Tanggapan' }}
                                                </button>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-center align-middle">
                                            @if(auth()->check() && auth()->user()->role !== 'pimpinan')
                                                @if($isScheduleOpen)
                                                    <button wire:click="openRealisasi({{ $ind->id }}, '{{ addslashes($ind->nama_indikator) }}', '{{ $ind->target_tahunan }}', '{{ $ind->satuan }}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm flex items-center justify-center gap-1 mx-auto transition-colors"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> {{ $ind->realisasi_bulan ? 'Edit' : 'Isi Realisasi' }}</button>
                                                @else
                                                    <button disabled class="px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold rounded flex items-center justify-center gap-1 mx-auto cursor-not-allowed border border-gray-200"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Terkunci</button>
                                                @endif
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

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="text-lg font-bold text-gray-800">Rencana Aksi</h3>
                
                <div class="flex gap-2">
                    @if(auth()->check() && auth()->user()->role !== 'pimpinan')
                        <button wire:click="openTambahAksi" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg flex items-center gap-2 shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Manual
                        </button>
                    @endif
                    
                    <button class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white text-xs font-bold rounded-lg flex items-center gap-2 shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Sinkron Dari E-Dialog
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="bg-red-50 border border-red-100 rounded-lg p-3 mb-6 flex items-start gap-3">
                    <div class="text-xs text-red-600"><span class="font-bold">Mode Sinkron:</span> Gunakan tombol "Sinkron Dari E-Dialog" untuk menarik Rencana Aksi. Atau gunakan "Tambah Manual" jika diperlukan.</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 w-1/2">RENCANA AKSI</th>
                                <th class="px-4 py-4 text-center w-24">TARGET</th>
                                <th class="px-4 py-4 text-center w-24">SATUAN</th>
                                <th class="px-4 py-4 text-center w-24">REALISASI</th>
                                <th class="px-4 py-4 text-center w-24">CAPAIAN</th>
                                <th class="px-4 py-4 text-center w-32">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($rencanaAksis as $aksi)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 align-middle text-gray-800 font-medium leading-relaxed">{{ $aksi->nama_aksi }}</td>
                                    <td class="px-4 py-4 text-center align-middle font-bold text-blue-600 bg-blue-50 rounded-lg">{{ $aksi->target }}</td>
                                    <td class="px-4 py-4 text-center align-middle text-gray-600"><span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold">{{ $aksi->satuan }}</span></td>
                                    <td class="px-4 py-4 text-center align-middle text-gray-800 font-medium">{{ $aksi->realisasi_bulan ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center align-middle">
                                        @if($aksi->capaian_bulan !== null)
                                            <span class="px-2 py-1 rounded text-xs font-bold text-white bg-green-500">{{ $aksi->capaian_bulan }}%</span>
                                        @else - @endif
                                    </td>
                                    
                                    <td class="px-4 py-4 text-center align-middle">
                                        @if(auth()->check() && auth()->user()->role !== 'pimpinan')
                                            @if($isScheduleOpen)
                                                <button wire:click="openRealisasiAksi({{ $aksi->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow-sm flex items-center justify-center gap-1 mx-auto transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    {{ $aksi->realisasi_bulan ? 'Edit' : 'Isi Realisasi' }}
                                                </button>
                                            @else
                                                <button disabled class="px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-bold rounded flex items-center justify-center gap-1 mx-auto cursor-not-allowed border border-gray-200"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Terkunci</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 italic"><div class="flex flex-col items-center justify-center gap-2"><svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>Belum ada data Rencana Aksi.</span></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($isOpenTambahAksi)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
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
                        <input type="text" wire:model="formAksiSatuan" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Dokumen">
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

    @if($isOpenRealisasi)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
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

    @if($isOpenTanggapan)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Beri Tanggapan Pimpinan</h3>
                <button wire:click="closeTanggapan" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs font-bold text-blue-500 uppercase">Indikator</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $indikatorNama }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Tanggapan</label>
                    <textarea wire:model="tanggapanInput" rows="4" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
                              placeholder="Berikan masukan atau arahan terkait capaian ini..."></textarea>
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

    @if($isOpenRealisasiAksi)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
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

</div>