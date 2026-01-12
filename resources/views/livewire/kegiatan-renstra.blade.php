<div>
    {{-- Konfigurasi Judul Halaman & Breadcrumb --}}
    <x-slot:title>Kegiatan Renstra</x-slot>

    <x-slot:breadcrumb>
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Matrik Renstra</span>
            <span class="mx-2">/</span>
            <a href="{{ route('matrik.program') }}" class="hover:text-white transition-colors">Program</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Kegiatan</span>
        </div>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="space-y-6 md:space-y-8 relative z-10 mt-6 md:mt-8 px-4 md:px-0">

        {{-- INFORMASI ATAS (KEMBALI KE LAYOUT 2 KOLOM) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            
            {{-- KOTAK 1: Perangkat Daerah --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h4 class="text-sm font-bold text-gray-800 mb-1">Perangkat Daerah</h4>
                <p class="text-gray-600 text-sm">1.02.0.00.0.00.01.0000 DINAS KESEHATAN</p>
            </div>

            {{-- KOTAK 2: Program & Outcome (Digabung seperti semula) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
                <h4 class="text-sm font-bold text-gray-800 mb-1">Program</h4>
                <p class="text-gray-600 text-sm font-medium uppercase leading-relaxed">
                    <span class="font-bold">{{ $program->kode }}</span> {{ $program->nama }}
                </p>
                
                {{-- LIST OUTCOME (Akan tampil 1 saja jika difilter dari halaman sebelumnya) --}}
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <h5 class="text-xs font-bold text-gray-500 mb-1">OUTCOME</h5>
                    <div class="text-gray-600 text-xs leading-relaxed">
                        @forelse($program->outcomes as $outcome)
                        <div class="mb-1 flex items-start">
                            <span class="mr-2">•</span>
                            <span>{{ $outcome->outcome }}</span>
                        </div>
                        @empty
                        <span class="italic text-gray-400">Belum ada outcome terpilih.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

            {{-- Header Tabel --}}
            <div class="px-4 py-4 md:px-6 md:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <h3 class="font-bold text-gray-800 text-base md:text-lg">Kegiatan / Indikator</h3>
                
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="create" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex justify-center items-center transition-colors shadow-sm active:scale-95">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Kegiatan
                </button>
                @endif
            </div>

            {{-- Table Wrapper --}}
            <div class="p-4 sm:p-6 pb-10">
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px] pb-40">
                    <table class="w-full text-left border-collapse whitespace-nowrap sm:whitespace-normal">
                        <thead>
                            <tr class="bg-white text-gray-700 text-sm font-bold border-b border-gray-200">
                                <th rowspan="2" class="p-4 border-r border-gray-200 align-middle min-w-[250px] sm:w-96">Kegiatan / Output / Indikator Output</th>
                                <th colspan="6" class="p-4 border-b border-r border-gray-200 text-center align-middle">Periode</th>
                                @if(auth()->user()->hasRole('admin'))
                                <th rowspan="2" class="p-4 text-center align-middle w-48">Aksi</th>
                                @endif
                            </tr>
                            <tr class="bg-white text-gray-800 text-sm font-bold border-b border-gray-200">
                                <th class="p-4 border-r text-center min-w-[80px]">2025</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2026</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2027</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2028</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2029</th>
                                <th class="p-4 border-r text-center min-w-[80px]">2030</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-600">
                            @forelse($kegiatans as $kegiatan)

                            {{-- BARIS 1: KEGIATAN --}}
                            <tr class="bg-white border-b border-gray-100 group">
                                <td class="p-6 border-r border-gray-100 align-top whitespace-normal">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 h-6 whitespace-nowrap">
                                                Kegiatan
                                            </span>
                                            <span class="text-gray-800 font-bold leading-relaxed uppercase">
                                                {{ $kegiatan->kode }} {{ $kegiatan->nama }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="6" class="p-4 border-r text-center text-gray-300 align-middle">&mdash;</td>

                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm">
                                            Menu <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 text-left">
                                            <div class="py-1">
                                                {{-- 1. Tambah Output --}}
                                                <button wire:click="tambahOutput({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2 text-sm text-green-600 hover:bg-green-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Output
                                                </button>

                                                {{-- 2. Hapus Kegiatan --}}
                                                <button wire:click="delete({{ $kegiatan->id }})" wire:confirm="Hapus Kegiatan ini? Data sub-kegiatan dan indikator akan ikut terhapus." @click="open = false" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Kegiatan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>

                            {{-- BARIS 2: LIST OUTPUT (LOOPING) --}}
                            @foreach($kegiatan->outputs as $output)
                            <tr class="bg-gray-50 border-b border-gray-100 hover:bg-gray-100 transition-colors">
                                <td class="p-6 border-r border-gray-100 align-top pl-6 sm:pl-12 whitespace-normal">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start sm:items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800 border border-green-200 h-6 whitespace-nowrap">
                                                Output
                                            </span>
                                            <span class="text-gray-800 font-bold leading-relaxed text-sm">
                                                {{ $output->deskripsi }}
                                            </span>
                                        </div>

                                        {{-- TAMPILKAN PJ DI SINI (PER OUTPUT) --}}
                                        @if($output->jabatan)
                                            @php
                                                $pjClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                                $pjText = 'PJ: ' . $output->jabatan->nama;
                                                $pegawai = $output->jabatan->pegawai;
                                                if ($pegawai) {
                                                    $status = strtolower($pegawai->status);
                                                    if (str_contains($status, 'definitif')) {
                                                        $pjClass = 'bg-green-100 text-green-700 border-green-200';
                                                    } elseif (str_contains($status, 'plt') || str_contains($status, 'plh')) {
                                                        $pjClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                                    } else {
                                                        $pjClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                                    }
                                                } else {
                                                    $pjClass = 'bg-red-50 text-red-600 border-red-100';
                                                }
                                            @endphp
                                            <div class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-xs border w-fit {{ $pjClass }}">
                                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                <span class="font-bold mr-1">{{ Str::limit($pjText, 60) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td colspan="6" class="p-4 border-r text-center text-gray-300 align-middle">&mdash;</td>

                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm">
                                            Menu <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 text-left">
                                            <div class="py-1">
                                                {{-- 1. Penanggung Jawab --}}
                                                <button wire:click="pilihPenanggungJawab({{ $output->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>Penanggung Jawab
                                                </button>

                                                {{-- 2. Sub Kegiatan (DENGAN FILTER OUTPUT_ID AGAR HALAMAN SELANJUTNYA TAMPIL 1 SAJA) --}}
                                                <a href="{{ route('renstra.sub_kegiatan', ['id' => $kegiatan->id, 'output_id' => $output->id, 'outcome_id' => request()->query('outcome_id')]) }}" class="group flex w-full items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition-colors">
    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Sub Kegiatan
</a>

                                                {{-- 3. + Tambah Indikator --}}
                                                <button wire:click="tambahIndikator({{ $output->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Indikator
                                                </button>

                                                {{-- 4. Edit Output --}}
                                                <button wire:click="editOutput({{ $output->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-purple-600 hover:bg-purple-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Edit Output
                                                </button>

                                                {{-- 5. Hapus Output --}}
                                                <button wire:click="hapusOutput({{ $output->id }})" wire:confirm="Hapus output ini?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Output
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            
                            {{-- BARIS 3: INDIKATOR (LOOPING DARI OUTPUT) --}}
                            @foreach($output->indikators as $indikator)
                            <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="p-6 border-r border-gray-100 align-top pl-6 sm:pl-12 whitespace-normal">
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-start ml-0 sm:ml-8 border-l-2 border-yellow-200 pl-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-yellow-50 text-yellow-700 border border-yellow-100 h-5 whitespace-nowrap">
                                            Indikator Output
                                        </span>
                                        <span class="text-gray-600 text-sm leading-relaxed font-medium">
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

                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-4 text-center align-middle relative">
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm">
                                            Menu <svg class="-mr-1 ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </button>
                                        <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 text-left">
                                            <div class="py-1">
                                                {{-- 1. Edit Indikator --}}
                                                <button wire:click="editIndikator({{ $indikator->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50">
                                                    <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Edit Indikator
                                                </button>

                                                {{-- 2. Atur Target --}}
                                                <button wire:click="aturTarget({{ $indikator->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-purple-600 hover:bg-purple-50">
                                                    <svg class="mr-3 h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>Atur Target
                                                </button>

                                                {{-- 3. Hapus Indikator --}}
                                                <button wire:click="deleteIndikator({{ $indikator->id }})" wire:confirm="Hapus indikator ini?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Hapus Indikator
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach {{-- END LOOP INDIKATOR --}}

                            @endforeach {{-- END LOOP OUTPUT --}}

                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('admin') ? 8 : 7 }}" class="p-10 text-center text-gray-400 italic bg-gray-50">
                                    Belum ada kegiatan untuk program ini.
                                    @if(auth()->user()->hasRole('admin'))
                                    Silakan klik tombol <strong>Tambah Kegiatan</strong>.
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

    {{-- MODAL HANYA UNTUK ADMIN --}}
    @if(auth()->user()->hasRole('admin'))
    
    {{-- MODAL KEGIATAN --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="kode" placeholder="Contoh: 1.02.02.1.01" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('kode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                    <textarea wire:model="nama" rows="3" placeholder="Nama Kegiatan" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                    @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">Batal</button>
                <button wire:click="store" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL OUTPUT --}}
    @if($isOpenOutput)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Form Output</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
                    <textarea wire:model="output" rows="4" placeholder="Keterangan Output" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                    @error('output') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">Batal</button>
                <button wire:click="storeOutput" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PJ --}}
    @if($isOpenPJ)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Penanggung Jawab</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-600 italic">"{{ $pj_kegiatan_text }}"</div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <select wire:model="pj_jabatan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-blue-500 outline-none bg-white">
                        <option value="">Pilih Jabatan</option>
                        @foreach($jabatans as $j) <option value="{{ $j->id }}">{{ $j->nama }}</option> @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">Batal</button>
                <button wire:click="simpanPenanggungJawab" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL INDIKATOR --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg sm:mx-4 p-6 animate-fade-in-down h-auto max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Indikator' : 'Tambah Indikator' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea wire:model="ind_keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                    @error('ind_keterangan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <select wire:model="ind_satuan" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
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
            </div>
            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">Batal</button>
                <button wire:click="storeIndikator" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TARGET --}}
    @if($isOpenTarget)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 sm:p-0">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl sm:mx-4 transform transition-all scale-100 h-[85vh] sm:h-auto flex flex-col animate-fade-in-down">
            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-xl font-bold text-gray-800">Form Target</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto custom-scroll flex-1">
                @foreach([2025,2026,2027,2028,2029,2030] as $y)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-3 items-start sm:items-center">
                    <div class="col-span-1 sm:col-span-3"><label class="text-sm font-medium text-gray-700">Target {{$y}}</label></div>
                    <div class="col-span-1 sm:col-span-9 relative">
                        <input type="text" wire:model="target_{{$y}}" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <div class="absolute inset-y-0 right-0 px-3 flex items-center bg-gray-50 border-l border-gray-300 rounded-r-lg text-xs text-gray-500">{{ $target_satuan ?? 'Angka' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3 bg-gray-50 flex-shrink-0 rounded-b-xl">
                <button wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">Batal</button>
                <button wire:click="simpanTarget" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>