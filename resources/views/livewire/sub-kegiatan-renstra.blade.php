<div>
    {{-- Konfigurasi Judul Halaman & Breadcrumb --}}
    <x-slot:title>Sub Kegiatan Renstra</x-slot>

    <x-slot:breadcrumb>
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Matrik Renstra</span>
            <span class="mx-2">/</span>
            <a href="{{ route('matrik.program') }}" class="hover:text-white transition-colors">Program</a>
            <span class="mx-2">/</span>
            {{-- UPDATE LINK KEGIATAN: Sertakan outcome_id dari URL agar filter tetap jalan --}}
            <a href="{{ route('matrik.kegiatan', ['id' => $program->id, 'outcome_id' => request()->query('outcome_id')]) }}" class="hover:text-white transition-colors">Kegiatan</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Sub Kegiatan</span>
        </div>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="space-y-6 md:space-y-8 relative z-10 mt-6 md:mt-8 px-2 md:px-0">

        {{-- INFORMASI ATAS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h4 class="text-sm font-bold text-gray-800 mb-1">Perangkat Daerah</h4>
                <p class="text-gray-600 text-sm">1.02.0.00.0.00.01.0000 DINAS KESEHATAN</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h4 class="text-sm font-bold text-gray-800 mb-2">Program</h4>
                <p class="text-gray-600 text-sm uppercase font-medium">{{ $program->kode }} {{ $program->nama }}</p>
            </div>
        </div>

        {{-- INFORMASI TENGAH --}}
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <h4 class="text-sm font-bold text-gray-800 mb-2">Outcome</h4>
        <div class="text-gray-600 text-sm">
            {{-- PERBAIKAN: Gunakan variabel $outcomes_view yang dikirim dari controller --}}
            @forelse($outcomes_view as $out)
                <div class="mb-1 flex items-start">
                    <span class="mr-2">•</span>
                    <span>{{ $out->outcome }}</span>
                </div>
            @empty
                <span class="italic text-gray-400">Data outcome tidak spesifik atau belum dipilih.</span>
            @endforelse
        </div>
    </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h4 class="text-sm font-bold text-gray-800 mb-2">Kegiatan</h4>
                <p class="text-gray-600 text-sm uppercase font-medium">{{ $kegiatan->kode }} {{ $kegiatan->nama }}</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h4 class="text-sm font-bold text-gray-800 mb-2">Output</h4>
                <div class="text-gray-600 text-sm">
                    {{-- LOOP OUTPUT (Otomatis hanya 1 karena sudah difilter di PHP) --}}
                    @forelse($kegiatan->outputs as $output)
                        <div class="mb-1 flex items-start">
                            <span class="mr-2">•</span>
                            <span>{{ $output->deskripsi }}</span>
                        </div>
                    @empty
                        <span class="italic text-gray-400">-</span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

            <div class="px-4 py-4 md:px-6 md:py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Sub Kegiatan</h3>

                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                    {{-- UPDATE TOMBOL KEMBALI: Sertakan outcome_id --}}
                    <a href="{{ route('matrik.kegiatan', ['id' => $program->id, 'outcome_id' => request()->query('outcome_id')]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>

                    @if(auth()->user()->hasRole('admin'))
                    <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Sub Kegiatan
                    </button>
                    @endif
                </div>
            </div>

            <div class="p-4 md:p-6">
                {{-- Table Wrapper --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px]">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-white text-gray-700 font-bold border-b border-gray-200">
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle min-w-[250px] md:min-w-[300px]">Sub Kegiatan</th>
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle min-w-[200px]">Output</th>
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle min-w-[200px]">Indikator</th>
                                <th rowspan="3" class="p-4 border-r border-gray-200 align-middle min-w-[100px]">Satuan</th>
                                <th colspan="12" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50">Periode</th>
                                @if(auth()->user()->hasRole('admin'))
                                <th rowspan="3" class="p-4 text-center align-middle min-w-[80px]">Aksi</th>
                                @endif
                            </tr>
                            <tr class="bg-white text-gray-700 font-bold border-b border-gray-200">
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50 min-w-[140px]">2025</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center min-w-[140px]">2026</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50 min-w-[140px]">2027</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center min-w-[140px]">2028</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center bg-gray-50 min-w-[140px]">2029</th>
                                <th colspan="2" class="p-2 border-b border-r border-gray-200 text-center min-w-[140px]">2030</th>
                            </tr>
                            <tr class="bg-white text-gray-700 font-bold border-b border-gray-200 text-[10px]">
                                <th class="p-2 border-r text-center bg-gray-50">Target</th>
                                <th class="p-2 border-r text-center bg-gray-50">Pagu</th>
                                <th class="p-2 border-r text-center">Target</th>
                                <th class="p-2 border-r text-center">Pagu</th>
                                <th class="p-2 border-r text-center bg-gray-50">Target</th>
                                <th class="p-2 border-r text-center bg-gray-50">Pagu</th>
                                <th class="p-2 border-r text-center">Target</th>
                                <th class="p-2 border-r text-center">Pagu</th>
                                <th class="p-2 border-r text-center bg-gray-50">Target</th>
                                <th class="p-2 border-r text-center bg-gray-50">Pagu</th>
                                <th class="p-2 border-r text-center">Target</th>
                                <th class="p-2 border-r text-center">Pagu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-gray-600">
                            @forelse($sub_kegiatans as $sub)
                            @php
                            $rowCount = $sub->indikators->count();
                            $rowSpan = $rowCount > 0 ? $rowCount : 1;
                            @endphp

                            @if($rowCount > 0)
                            @foreach($sub->indikators as $index => $ind)
                            <tr class="hover:bg-gray-50 border-b border-gray-100">
                                @if($index === 0)
                                <td rowspan="{{ $rowSpan }}" class="p-4 border-r align-top font-bold text-gray-800 bg-white">
                                    <div>{{ $sub->kode }}</div>
                                    <div class="mt-1 min-w-[200px] whitespace-normal">{{ $sub->nama }}</div>

                                    @php
                                    $pjClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                    if(auth()->user()->hasRole('admin')) { $pjClass .= ' hover:bg-gray-200'; }
                                    $pjText = 'Pilih PJ';

                                    if ($sub->jabatan) {
                                    $pjText = 'PJ: ' . $sub->jabatan->nama;
                                    $pegawai = $sub->jabatan->pegawai;

                                    if ($pegawai) {
                                    $status = strtolower($pegawai->status);
                                    if (str_contains($status, 'definitif')) {
                                    $pjClass = 'bg-green-100 text-green-700 border-green-200';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-green-200';
                                    } elseif (str_contains($status, 'plt') || str_contains($status, 'plh')) {
                                    $pjClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-yellow-200';
                                    } else {
                                    $pjClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-blue-200';
                                    }
                                    } else {
                                    $pjClass = 'bg-red-50 text-red-600 border-red-100';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-red-100';
                                    }
                                    }
                                    @endphp

                                    @if(auth()->user()->hasRole('admin'))
                                    <button wire:click="pilihPenanggungJawab({{ $sub->id }})" class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] border w-fit transition-colors shadow-sm {{ $pjClass }}">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-bold mr-1">{{ Str::limit($pjText, 40) }}</span>
                                    </button>
                                    @else
                                    @if($sub->jabatan)
                                    <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] border w-fit shadow-sm cursor-default {{ $pjClass }}">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-bold mr-1">{{ Str::limit($pjText, 40) }}</span>
                                    </span>
                                    @endif
                                    @endif
                                </td>
                                <td rowspan="{{ $rowSpan }}" class="p-4 border-r align-top bg-white min-w-[200px] whitespace-normal">{{ $sub->output ?? '-' }}</td>
                                @endif

                                <td class="p-4 border-r text-gray-700 font-medium min-w-[200px] whitespace-normal">{{ $ind->keterangan }}</td>
                                <td class="p-4 border-r text-center">{{ $ind->satuan }}</td>

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

                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm transition-colors">
                                            Menu 
                                            <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/30 z-[60] md:hidden backdrop-blur-[1px]" style="display: none;"></div>

                                        <div x-show="open" 
                                             style="display: none;" 
                                             @click.outside="open = false"
                                             class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 z-[70] rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y divide-gray-100 text-left 
                                                    md:absolute md:top-auto md:left-auto md:right-0 md:translate-x-0 md:translate-y-0 md:mt-2 md:w-56 md:origin-top-right md:shadow-xl md:rounded-md">
                                            
                                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 md:hidden rounded-t-xl">
                                                <p class="text-sm font-bold text-gray-700">Aksi Sub Kegiatan</p>
                                            </div>

                                            <div class="py-1">
                                                <button wire:click="pilihPenanggungJawab({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-3 md:py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors">
                                                    <svg class="mr-3 h-5 w-5 md:h-4 md:w-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Penanggung Jawab
                                                </button>

                                                <button wire:click="aturTarget({{ $ind->id }})" @click="open = false" class="group flex w-full items-center px-4 py-3 md:py-2.5 text-sm text-purple-600 hover:bg-purple-50 transition-colors">
                                                    <svg class="mr-3 h-5 w-5 md:h-4 md:w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                    Atur Target
                                                </button>

                                                <div class="border-t border-gray-100 my-1"></div>

                                                <button wire:click="delete({{ $sub->id }})" wire:confirm="Hapus?" @click="open = false" class="group flex w-full items-center px-4 py-3 md:py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors rounded-b-xl md:rounded-b-md">
                                                    <svg class="mr-3 h-5 w-5 md:h-4 md:w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus Sub Kegiatan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr class="bg-white border-b border-gray-100">
                                <td class="p-4 border-r align-top font-bold text-gray-800">
                                    <div>{{ $sub->kode }}</div>
                                    <div class="mt-1 min-w-[200px] whitespace-normal">{{ $sub->nama }}</div>

                                    @php
                                    $pjClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                    if(auth()->user()->hasRole('admin')) { $pjClass .= ' hover:bg-gray-200'; }
                                    $pjText = 'Pilih PJ';

                                    if ($sub->jabatan) {
                                    $pjText = 'PJ: ' . $sub->jabatan->nama;
                                    $pegawai = $sub->jabatan->pegawai;

                                    if ($pegawai) {
                                    $status = strtolower($pegawai->status);
                                    if (str_contains($status, 'definitif')) {
                                    $pjClass = 'bg-green-100 text-green-700 border-green-200';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-green-200';
                                    } elseif (str_contains($status, 'plt') || str_contains($status, 'plh')) {
                                    $pjClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-yellow-200';
                                    } else {
                                    $pjClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-blue-200';
                                    }
                                    } else {
                                    $pjClass = 'bg-red-50 text-red-600 border-red-100';
                                    if(auth()->user()->hasRole('admin')) $pjClass .= ' hover:bg-red-100';
                                    }
                                    }
                                    @endphp

                                    @if(auth()->user()->hasRole('admin'))
                                    <button wire:click="pilihPenanggungJawab({{ $sub->id }})" class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] border w-fit transition-colors shadow-sm {{ $pjClass }}" title="Klik untuk ubah Penanggung Jawab">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-bold mr-1">{{ Str::limit($pjText, 40) }}</span>
                                    </button>
                                    @else
                                    @if($sub->jabatan)
                                    <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] border w-fit shadow-sm cursor-default {{ $pjClass }}">
                                        <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-bold mr-1">{{ Str::limit($pjText, 40) }}</span>
                                    </span>
                                    @endif
                                    @endif
                                </td>
                                <td class="p-4 border-r align-top min-w-[200px] whitespace-normal">{{ $sub->output ?? '-' }}</td>
                                <td colspan="12" class="p-4 border-r text-center text-gray-300 italic min-w-[300px]">Belum ada indikator</td>

                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm transition-colors">
                                            Menu 
                                            <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/30 z-[60] md:hidden backdrop-blur-[1px]" style="display: none;"></div>

                                        <div x-show="open" 
                                             style="display: none;" 
                                             @click.outside="open = false"
                                             class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 z-[70] rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y divide-gray-100 text-left 
                                                    md:absolute md:top-auto md:left-auto md:right-0 md:translate-x-0 md:translate-y-0 md:mt-2 md:w-56 md:origin-top-right md:shadow-xl md:rounded-md">
                                            
                                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 md:hidden rounded-t-xl">
                                                <p class="text-sm font-bold text-gray-700">Aksi Sub Kegiatan</p>
                                            </div>

                                            <div class="py-1">
                                                <button wire:click="pilihPenanggungJawab({{ $sub->id }})" @click="open = false" class="group flex w-full items-center px-4 py-3 md:py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors">
                                                    <svg class="mr-3 h-5 w-5 md:h-4 md:w-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Penanggung Jawab
                                                </button>

                                                <div class="border-t border-gray-100 my-1"></div>

                                                <button wire:click="delete({{ $sub->id }})" wire:confirm="Hapus?" @click="open = false" class="group flex w-full items-center px-4 py-3 md:py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors rounded-b-xl md:rounded-b-md">
                                                    <svg class="mr-3 h-5 w-5 md:h-4 md:w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus Sub Kegiatan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('admin') ? 17 : 16 }}" class="p-10 text-center text-gray-400 italic">Belum ada data Sub Kegiatan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HANYA UNTUK ADMIN --}}
    @if(auth()->user()->hasRole('admin'))
    
    {{-- MODAL UTAMA: TAMBAH / EDIT SUB KEGIATAN --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">

            {{-- Modal Header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit' : 'Tambah' }} Sub Kegiatan</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="p-5 overflow-y-auto">
                <div class="space-y-3"> {{-- Reduced spacing --}}

                    {{-- Kode --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Sub Kegiatan <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="kode" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder-gray-400" placeholder="Contoh: 1.02.02.1.01.01">
                        @error('kode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Sub Kegiatan <span class="text-red-500">*</span></label>
                        <textarea wire:model="nama" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none placeholder-gray-400" placeholder="Nama Sub Kegiatan"></textarea>
                        @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Output --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Output</label>
                        <textarea wire:model="output" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none placeholder-gray-400" placeholder="Output (Opsional)"></textarea>
                    </div>

                    {{-- Indikator Awal (Hanya saat Tambah) --}}
                    @if(!$isEditMode)
                    <div class="pt-3 mt-2 border-t border-gray-100">
                        <h4 class="text-sm font-bold text-gray-800 mb-2">Indikator Awal (Opsional)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Indikator Kinerja</label>
                                <input type="text" wire:model="ind_keterangan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 outline-none placeholder-gray-400" placeholder="Contoh: Jumlah Dokumen...">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Satuan</label>
                                {{-- UPDATE DROPDOWN DISINI --}}
                                <select wire:model="ind_satuan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-blue-500 outline-none">
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
                                    <option value="Sarana">Sarana</option>
                                    <option value="Unit">Unit</option>
                                </select>
                                @error('ind_satuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row justify-end gap-3 shrink-0">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-200">Batal</button>
                <button wire:click="store" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="store">Simpan</span>
                    <span wire:loading wire:target="store" class="flex items-center gap-1">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL INDIKATOR --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit' : 'Tambah' }} Indikator</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea wire:model="ind_keterangan" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none mt-1"></textarea>
                    @error('ind_keterangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Satuan</label>
                    {{-- UPDATE DROPDOWN DISINI --}}
                    <select wire:model="ind_satuan" class="w-full border rounded-lg px-3 py-2 bg-white text-sm focus:ring-blue-500 focus:border-blue-500 outline-none mt-1">
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
                        <option value="Keluarga">Keluarga</option> {{-- TAMBAHAN BARU --}}
                        <option value="Laporan">Laporan</option>
                        <option value="Layanan">Layanan</option> {{-- TAMBAHAN BARU --}}
                        <option value="Level">Level</option>
                        <option value="Nilai">Nilai</option>
                        <option value="Orang">Orang</option>
                        <option value="Paket">Paket</option>
                        <option value="Permil">Permil</option>
                        <option value="Persen">Persen</option>
                        <option value="Poin">Poin</option>
                        <option value="Rupiah">Rupiah</option>
                        <option value="Sarana">Sarana</option>
                        <option value="Unit">Unit</option>
                    </select>
                    @error('ind_satuan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-6 flex flex-col md:flex-row justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="storeIndikator" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TARGET --}}
    @if($isOpenTarget)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Form Target Indikator</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                <div class="space-y-6">
                    @foreach([2025, 2026, 2027, 2028, 2029, 2030] as $y)
                    <div class="grid grid-cols-1 gap-y-4 border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                            <label class="md:col-span-3 text-sm font-medium text-gray-700">Target {{ $y }}</label>
                            <div class="md:col-span-9">
                                <div class="flex rounded-md shadow-sm">
                                    <input type="text" wire:model="target_{{ $y }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="0">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        {{ $target_satuan ?? 'Satuan' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                            <label class="md:col-span-3 text-sm font-medium text-gray-700 mt-2">Pagu {{ $y }}</label>
                            <div class="md:col-span-9">
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

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row justify-end gap-3 shrink-0">
                <button wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Batal
                </button>
                <button wire:click="simpanTarget" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 border border-transparent text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center justify-center shadow-sm">
                    <span wire:loading.remove wire:target="simpanTarget">Simpan</span>
                    <span wire:loading wire:target="simpanTarget">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($isOpenPJ)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Penanggung Jawab</h3>
            <div class="mb-4 text-sm italic bg-gray-50 p-3 rounded border text-gray-600">"{{ $pj_sub_kegiatan_text }}"</div>
            <div>
                <label class="text-sm font-medium block mb-2 text-gray-700">Jabatan</label>
                <select wire:model="pj_jabatan_id" class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-blue-500 outline-none text-sm">
                    <option value="">Pilih Jabatan</option>
                    @foreach($jabatans as $j)<option value="{{$j->id}}">{{$j->nama}}</option>@endforeach
                </select>
            </div>
            <div class="mt-6 flex flex-col md:flex-row justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="simpanPenanggungJawab" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center justify-center shadow-sm">
                    <span wire:loading.remove wire:target="simpanPenanggungJawab">Simpan</span>
                    <span wire:loading wire:target="simpanPenanggungJawab">...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>