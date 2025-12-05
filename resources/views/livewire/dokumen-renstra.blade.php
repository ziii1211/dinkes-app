<div>
    <x-slot:title>Matrik Renstra</x-slot>
    
    <x-slot:breadcrumb>
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Master Data</span>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">Matrik Renstra</span>
    </x-slot>

    <div class="space-y-6">
        
        <!-- CARD UTAMA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Header Card & Tombol Aksi -->
            <div class="px-8 py-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h2 class="text-xl font-bold text-gray-800 text-blue-900">
                    Matriks RENSTRA
                </h2>
                <div class="flex gap-2">
                    <a href="{{ route('matrik.dokumen.print') }}" target="_blank" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded shadow-sm flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export PDF
                    </a>
                    <a href="{{ route('matrik.dokumen.excel') }}" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                    <button wire:click="openEditModal" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded shadow-sm flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit
                    </button>
                </div>
            </div>

            <!-- Informasi Dokumen -->
            <div class="px-8 py-6">
                <table class="w-full md:w-1/2 text-sm text-gray-700">
                    <tbody>
                        <tr>
                            <td class="py-2 font-bold w-40 text-blue-900">Unit Kerja</td>
                            <td class="py-2 w-4">:</td>
                            <td class="py-2 font-bold text-gray-800 uppercase">{{ $unit_kerja }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-bold text-blue-900">Nomor Dokumen</td>
                            <td class="py-2">:</td>
                            <td class="py-2 font-medium">{{ $nomor_dokumen }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-bold text-blue-900">Tanggal Dokumen</td>
                            <td class="py-2">:</td>
                            <td class="py-2 font-medium">{{ $tanggal_dokumen }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-bold text-blue-900">Periode</td>
                            <td class="py-2">:</td>
                            <td class="py-2 font-medium">{{ $periode }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Matriks -->
            <div class="border-t border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-800 font-bold text-sm">
                            <tr>
                                <th class="p-4 border-b border-gray-200 w-1/6">Tujuan</th>
                                <th class="p-4 border-b border-gray-200 w-1/6">Sasaran</th>
                                <th class="p-4 border-b border-gray-200 w-1/6">Outcome</th>
                                <th class="p-4 border-b border-gray-200 w-1/6">Output</th>
                                <th class="p-4 border-b border-gray-200 w-1/6">Indikator</th>
                                <th class="p-4 border-b border-gray-200 w-1/6 bg-gray-50">Program / Kegiatan / Sub Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-600 divide-y divide-gray-100">
                            
                            <!-- BAGIAN 1: TUJUAN -->
                            @foreach($tujuans as $tujuan)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="p-4 font-medium text-gray-800">{{ $tujuan->tujuan ?? $tujuan->sasaran_rpjmd }}</td>
                                <td class="p-4"></td><td class="p-4"></td><td class="p-4"></td>
                                
                                <!-- Kolom Indikator (DARI POHON KINERJA) -->
                                <td class="p-4">
                                    @if(isset($tujuan->indikators_from_pohon) && $tujuan->indikators_from_pohon->isNotEmpty())
                                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                                            @foreach($tujuan->indikators_from_pohon as $ind)
                                                <li>{{ $ind->nama_indikator }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endif
                                </td>
                                
                                <td class="p-4 bg-gray-50"></td>
                            </tr>
                            @endforeach

                            <!-- BAGIAN 2: SASARAN -->
                            @foreach($sasarans as $sasaran)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="p-4"></td>
                                <td class="p-4 font-medium text-gray-800">{{ $sasaran->sasaran }}</td>
                                <td class="p-4"></td><td class="p-4"></td>
                                
                                <!-- Kolom Indikator -->
                                <td class="p-4">
                                    @if(isset($sasaran->indikators_from_pohon) && $sasaran->indikators_from_pohon->isNotEmpty())
                                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                                            @foreach($sasaran->indikators_from_pohon as $ind)
                                                <li>{{ $ind->nama_indikator }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endif
                                </td>
                                
                                <td class="p-4 bg-gray-50"></td>
                            </tr>
                            @endforeach

                            <!-- BAGIAN 3: OUTCOME -->
                            @foreach($outcomes as $outcome)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="p-4"></td><td class="p-4"></td>
                                <td class="p-4 font-medium text-gray-800">{{ $outcome->outcome }}</td>
                                <td class="p-4"></td>
                                
                                <!-- Kolom Indikator -->
                                <td class="p-4">
                                    @if(isset($outcome->indikators_from_pohon) && $outcome->indikators_from_pohon->isNotEmpty())
                                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                                            @foreach($outcome->indikators_from_pohon as $ind)
                                                <li>{{ $ind->nama_indikator }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endif
                                </td>

                                <td class="p-4 bg-gray-50 text-gray-800">
                                    <div class="text-xs font-bold text-gray-500">{{ $outcome->program->kode ?? '' }}</div>
                                    <div class="font-medium font-bold">{{ $outcome->program->nama ?? '' }}</div>
                                </td>
                            </tr>
                            @endforeach

                            <!-- BAGIAN 4: OUTPUT (KEGIATAN) -->
                            @foreach($kegiatans as $kegiatan)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="p-4"></td><td class="p-4"></td><td class="p-4"></td>
                                <td class="p-4 font-medium text-gray-800">{{ $kegiatan->output }}</td>
                                
                                <!-- Kolom Indikator -->
                                <td class="p-4">
                                    @if(isset($kegiatan->indikators_from_pohon) && $kegiatan->indikators_from_pohon->isNotEmpty())
                                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                                            @foreach($kegiatan->indikators_from_pohon as $ind)
                                                <li>{{ $ind->nama_indikator }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endif
                                </td>

                                <td class="p-4 bg-gray-50 text-gray-800">
                                    <div class="text-xs font-bold text-gray-500">{{ $kegiatan->kode }}</div>
                                    <div class="font-medium">{{ $kegiatan->nama }}</div>
                                </td>
                            </tr>
                            @endforeach

                            <!-- BAGIAN 5: SUB KEGIATAN (RESTORED) -->
                            @foreach($sub_kegiatans as $sub)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="p-4"></td><td class="p-4"></td><td class="p-4"></td>
                                
                                <!-- Restore: Tampilkan Output Sub Kegiatan -->
                                <td class="p-4 font-medium text-gray-800">{{ $sub->output ?? '-' }}</td>
                                
                                <!-- Kolom Indikator (Sekarang sudah di-load dari controller) -->
                                <td class="p-4">
                                    @if(isset($sub->indikators_from_pohon) && $sub->indikators_from_pohon->isNotEmpty())
                                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                                            @foreach($sub->indikators_from_pohon as $ind)
                                                <li>{{ $ind->nama_indikator }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 text-xs italic">-</span>
                                    @endif
                                </td>
                                
                                <td class="p-4 bg-gray-50">
                                    <div class="text-xs font-bold text-gray-500">{{ $sub->kode ?? '' }}</div>
                                    <div class="font-medium">{{ $sub->nama ?? '' }}</div>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL EDIT DATA RENSTRA -->
    @if($isOpenEdit)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Data Renstra {{ $periode }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No Dokumen</label>
                    <input type="text" wire:model="edit_nomor_dokumen" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('edit_nomor_dokumen') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Penetapan</label>
                    <input type="text" wire:model="edit_tanggal_penetapan" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('edit_tanggal_penetapan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none">Batal</button>
                <button wire:click="updateRenstra" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>