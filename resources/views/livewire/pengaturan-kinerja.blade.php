<div>
    {{-- 1. HEADER BIRU --}}
    <x-slot:title>
        Pengaturan Kinerja
    </x-slot>

    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-blue-100 transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Pengukuran Kinerja</span>
            <span class="mx-2">/</span>
            <a href="{{ route('pengukuran.bulanan') }}" class="hover:text-blue-100 transition-colors">Pengukuran Bulanan</a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-100 p-4 md:p-6">
        
        @if (session()->has('message'))
            <div class="max-w-7xl mx-auto mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mt-4">
            
            {{-- TOMBOL KEMBALI (Absolute di Desktop, Relative di Mobile biar gak nutup konten) --}}
            <div class="md:absolute md:top-6 md:right-6 flex justify-end p-4 md:p-0">
                <a href="{{ route('pengukuran.bulanan') }}" class="text-gray-400 hover:text-gray-600 transition-colors z-10 p-2 bg-gray-50 md:bg-transparent rounded-full md:rounded-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>

            <div class="p-4 md:p-8 space-y-6 md:space-y-8">
                
                {{-- HEADER & FILTER TAHUN --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-100 pb-6 gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <h2 class="text-xl font-bold text-gray-800 border-l-4 border-blue-600 pl-3">Pengaturan Kinerja</h2>
                        <span class="text-gray-300 text-2xl font-light hidden sm:inline">|</span>
                        <span class="text-lg md:text-xl font-medium text-gray-800">{{ $jabatan->nama }}</span>
                    </div>
                    
                    <div class="w-full md:w-auto md:pr-8">
                        <select wire:model.live="filterTahun" class="w-full md:w-auto border border-gray-300 rounded-md text-sm py-2 px-4 focus:ring-blue-500 focus:border-blue-500 outline-none cursor-pointer">
                            @for($y = date('Y')-1; $y <= date('Y')+1; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- PILIH DOKUMEN PK --}}
                <div class="bg-gray-50 rounded-xl p-4 md:p-6 border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Dokumen PK (status: Terpublikasi)</label>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <select wire:model="selectedPkId" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 outline-none bg-white cursor-pointer">
                                <option value="">-- Pilih PK --</option>
                                @forelse($pkList as $pk)
                                    <option value="{{ $pk->id }}">{{ $pk->keterangan }}</option>
                                @empty
                                    <option value="" disabled>Tidak ada PK Terpublikasi di tahun {{ $filterTahun }}</option>
                                @endforelse
                            </select>
                        </div>
                        <button wire:click="loadPkDetail" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center justify-center gap-2 shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Tampilkan PK
                        </button>
                    </div>
                </div>

                @if($currentPk)
                <div class="pt-2 animate-fade-in-down">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex flex-wrap items-center gap-2">
                        Rencana Hasil Kerja & Indikator <span class="text-gray-300 font-light hidden sm:inline">|</span> <span class="font-normal text-gray-600 text-sm sm:text-base">Tahun {{ $filterTahun }}</span>
                    </h3>

                    <div class="bg-white border border-gray-200 rounded-xl p-4 md:p-6 shadow-sm">
                        
                        {{-- JUDUL PK & TOMBOL AKSI --}}
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b border-gray-100 pb-4 gap-4">
                            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wide leading-relaxed">
                                {{ $currentPk->keterangan }}
                            </h4>
                            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                                <button wire:click="deleteRkh" wire:confirm="Yakin ingin menghapus?" class="flex-1 md:flex-none justify-center px-4 py-2 bg-pink-50 text-pink-600 hover:bg-pink-100 text-xs font-bold rounded-lg flex items-center gap-2 transition-colors border border-pink-100 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus RKH
                                </button>
                                
                                <button wire:click="updateBulananRhk" wire:loading.attr="disabled" class="flex-1 md:flex-none justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg flex items-center gap-2 shadow-sm transition-colors whitespace-nowrap">
                                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <svg wire:loading class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Perbarui Bulanan RHK
                                </button>
                            </div>
                        </div>

                        {{-- PILIH BULAN --}}
                        <div class="flex flex-wrap items-center gap-2 mb-6">
                            <span class="text-sm text-gray-500 mr-2 font-medium w-full sm:w-auto mb-2 sm:mb-0">Pilih bulan pengisian:</span>
                            @php
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            
                            @foreach($months as $index => $name)
                                <button wire:click="selectMonth({{ $index }})" 
                                    class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors shadow-sm mb-1
                                    {{ $selectedMonth == $index 
                                        ? 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700' 
                                        : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600' 
                                    }}">
                                    {{ $name }}
                                </button>
                            @endforeach
                        </div>

                        @if($selectedMonth == (int)date('n'))
                            {{-- TABEL SCROLLABLE --}}
                            <div class="overflow-x-auto border border-gray-200 rounded-lg animate-fade-in-down min-h-[300px]">
                                <table class="w-full text-left text-sm border-collapse whitespace-nowrap md:whitespace-normal">
                                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider">
                                        <tr>
                                            <th class="px-4 py-3 text-center border-b border-gray-200 w-12 min-w-[50px]">NO</th>
                                            <th class="px-4 py-3 border-b border-gray-200 min-w-[250px] md:w-1/3">KINERJA UTAMA (KU)</th>
                                            <th class="px-4 py-3 border-b border-gray-200 min-w-[250px] md:w-1/3">INDIKATOR KINERJA (IKU)</th>
                                            <th class="px-4 py-3 text-center border-b border-gray-200 min-w-[100px]">SATUAN</th>
                                            <th class="px-4 py-3 text-center border-b border-gray-200 min-w-[100px]">TARGET</th>
                                            <th class="px-4 py-3 text-center border-b border-gray-200 min-w-[100px]">ARAH</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-gray-700 bg-white">
                                        @forelse($currentPk->sasarans as $index => $sasaran)
                                            @php 
                                                $indikators = $sasaran->indikators ?? collect([]);
                                                $rowspan = $indikators->count() ?: 1;
                                                $firstIndikator = $indikators->first();
                                            @endphp

                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-4 text-center align-top border-r border-gray-50 text-gray-500 font-medium" rowspan="{{ $rowspan }}">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-4 py-4 align-top border-r border-gray-50 font-medium text-gray-800 leading-relaxed whitespace-normal" rowspan="{{ $rowspan }}">
                                                    {{ $sasaran->sasaran }}
                                                </td>

                                                @if($firstIndikator)
                                                    <td class="px-4 py-4 align-top border-r border-gray-50 whitespace-normal">{{ $firstIndikator->nama_indikator }}</td>
                                                    <td class="px-4 py-4 text-center align-top border-r border-gray-50">{{ $firstIndikator->satuan }}</td>
                                                    <td class="px-4 py-4 text-center align-top font-bold text-gray-800 border-r border-gray-50">{{ $firstIndikator->target }}</td>
                                                    <td class="px-4 py-4 text-center align-top uppercase text-xs font-bold text-gray-500">{{ $firstIndikator->arah }}</td>
                                                @else
                                                    <td colspan="4" class="px-4 py-4 text-center italic text-gray-400">Tidak ada indikator</td>
                                                @endif
                                            </tr>

                                            @if($rowspan > 1)
                                                @foreach($indikators->skip(1) as $ind)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-4 align-top border-r border-gray-50 whitespace-normal">{{ $ind->nama_indikator }}</td>
                                                    <td class="px-4 py-4 text-center align-top border-r border-gray-50">{{ $ind->satuan }}</td>
                                                    <td class="px-4 py-4 text-center align-top font-bold text-gray-800 border-r border-gray-50">{{ $ind->target }}</td>
                                                    <td class="px-4 py-4 text-center align-top uppercase text-xs font-bold text-gray-500">{{ $ind->arah }}</td>
                                                </tr>
                                                @endforeach
                                            @endif

                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic bg-gray-50">
                                                    Data sasaran kinerja kosong.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-12 flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-200 rounded-lg animate-fade-in-down">
                                <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-lg font-medium text-gray-500">Data untuk bulan {{ $months[$selectedMonth] }} belum tersedia.</p>
                                <p class="text-sm mt-1">Data hanya tersedia untuk bulan berjalan ({{ $months[(int)date('n')] }}).</p>
                            </div>
                        @endif

                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>