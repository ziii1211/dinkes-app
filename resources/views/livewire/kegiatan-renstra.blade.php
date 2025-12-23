<div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 relative z-10">
        <h4 class="text-sm font-bold text-gray-800 mb-1">Perangkat Daerah</h4>
        <p class="text-gray-600 text-sm">1.02.0.00.0.00.01.0000 DINAS KESEHATAN</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 relative z-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Program</h4>
            <p class="text-gray-600 text-sm font-medium uppercase leading-relaxed">
                <span class="font-bold">{{ $program->kode }}</span> {{ $program->nama }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Outcome</h4>
            <div class="text-gray-600 text-sm leading-relaxed">
                @forelse($program->outcomes as $outcome)
                    <div class="mb-2 last:mb-0 flex items-start"><span class="mr-2 text-gray-400">•</span><span>{{ $outcome->outcome }}</span></div>
                @empty
                    <span class="italic text-gray-400">Belum ada outcome.</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-8 relative z-10">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Kegiatan / Indikator</h3>
                <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Kegiatan
                </button>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-700 text-sm font-bold border-b border-gray-200">
                                <th rowspan="2" class="p-4 border-r border-gray-200 align-middle w-96">Kegiatan / Output / Indikator Output</th>
                                <th colspan="6" class="p-4 border-b border-r border-gray-200 text-center align-middle">Periode</th>
                                <th rowspan="2" class="p-4 text-center align-middle w-48">Aksi</th>
                            </tr>
                            <tr class="bg-white text-gray-800 text-sm font-bold border-b border-gray-200">
                                <th class="p-4 border-r text-center w-24">2025</th><th class="p-4 border-r text-center w-24">2026</th><th class="p-4 border-r text-center w-24">2027</th><th class="p-4 border-r text-center w-24">2028</th><th class="p-4 border-r text-center w-24">2029</th><th class="p-4 border-r text-center w-24">2030</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-600">
                            @forelse($kegiatans as $kegiatan)
                                <tr class="bg-white border-b border-gray-100">
                                    <td class="p-6 border-r border-gray-100 align-top">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex gap-3 items-start">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 h-6 whitespace-nowrap">
                                                    Kegiatan
                                                </span>
                                                <span class="text-gray-800 font-bold leading-relaxed uppercase">
                                                    {{ $kegiatan->kode }} {{ $kegiatan->nama }}
                                                </span>
                                            </div>
                                            @if($kegiatan->jabatan && !$kegiatan->output)
                                                <div class="mt-1 ml-14 inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-50 text-yellow-700 border border-yellow-100 w-fit">
                                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    PJ: {{ $kegiatan->jabatan->nama }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td colspan="6" class="p-4 border-r text-center text-gray-300 align-middle">&mdash;</td>
                                    
                                    <td class="p-4 text-center align-middle relative">
                                        <div class="flex justify-center items-center gap-2">
                                            
                                            @if(!$kegiatan->output)
                                                <button wire:click="tambahOutput({{ $kegiatan->id }})" class="inline-flex items-center px-3 py-1.5 border border-green-600 text-xs font-medium rounded-md text-green-600 bg-white hover:bg-green-50 focus:outline-none transition-colors">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Tambah Output
                                                </button>

                                                <button wire:click="delete({{ $kegiatan->id }})" wire:confirm="Hapus Kegiatan ini?" class="inline-flex items-center px-3 py-1.5 border border-red-600 text-xs font-medium rounded-md text-red-600 bg-white hover:bg-red-50 focus:outline-none transition-colors">
                                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus
                                                </button>
                                                
                                                <button wire:click="edit({{ $kegiatan->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                                                     <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Edit
                                                </button>

                                            @else
                                                <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                                    <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none shadow-sm">
                                                        Menu <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                    </button>
                                                    
                                                    <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                                        <div class="py-1">
                                                            <button wire:click="edit({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                                                                <svg class="mr-3 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                                Edit Kegiatan
                                                            </button>
                                                            <button wire:click="delete({{ $kegiatan->id }})" wire:confirm="Hapus Kegiatan ini?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                                <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                Hapus Kegiatan
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @if($kegiatan->output)
                                <tr class="bg-gray-50 border-b border-gray-100 hover:bg-gray-100 transition-colors">
                                    <td class="p-6 border-r border-gray-100 align-top pl-12">
                                        <div class="flex gap-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800 border border-green-200 h-6 whitespace-nowrap">
                                                Output
                                            </span>
                                            <span class="text-gray-800 font-bold leading-relaxed text-sm">
                                                {{ $kegiatan->output }}
                                            </span>
                                        </div>
                                    </td>
                                    <td colspan="6" class="p-4 border-r text-center text-gray-300 align-middle">&mdash;</td>
                                    
                                    <td class="p-4 text-center align-middle">
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm">
                                                Menu <svg class="-mr-1 ml-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </button>
                                            
                                            <div x-show="open" style="display: none;" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    <button wire:click="pilihPenanggungJawab({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors">
                                                        <svg class="mr-3 h-4 w-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                        Penanggung Jawab
                                                    </button>
                                                    
                                                    <a href="{{ route('renstra.sub_kegiatan', ['id' => $kegiatan->id]) }}" wire:navigate class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50 transition-colors">
                                                        <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        Sub Kegiatan
                                                    </a>

                                                    <button wire:click="tambahIndikator({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-blue-600 hover:bg-blue-50 transition-colors">
                                                        <svg class="mr-3 h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                        Tambah Indikator
                                                    </button>

                                                    <button wire:click="editOutput({{ $kegiatan->id }})" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-purple-600 hover:bg-purple-50 transition-colors">
                                                        <svg class="mr-3 h-4 w-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        Edit Output
                                                    </button>
                                                    
                                                    <button wire:click="hapusOutput({{ $kegiatan->id }})" wire:confirm="Hapus output kegiatan ini?" @click="open = false" class="group flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                        <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus Output
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif

                                @foreach($kegiatan->indikators as $indikator)
                                <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="p-6 border-r border-gray-100 align-top pl-12"> 
                                        <div class="flex gap-3 ml-8 border-l-2 border-yellow-200 pl-3">
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
                                    
                                    <td class="p-4 text-center align-middle relative">
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-200 px-3 py-1.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none shadow-sm">
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
                                </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="8" class="p-10 text-center text-gray-400 italic bg-gray-50">Belum ada kegiatan untuk program ini. Silakan klik tombol <strong>+ Tambah Kegiatan</strong>.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="space-y-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Kode Kegiatan <span class="text-red-500">*</span></label><input type="text" wire:model="kode" placeholder="Contoh: 1.02.02.1.01" class="w-full border rounded px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">@error('kode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Nama Kegiatan <span class="text-red-500">*</span></label><textarea wire:model="nama" rows="3" placeholder="Nama Kegiatan" class="w-full border rounded px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>@error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
            </div>
            <div class="mt-8 flex justify-end gap-3"><button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded font-medium hover:bg-gray-200 transition-colors">Batal</button><button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 transition-colors shadow-sm">Simpan</button></div>
        </div>
    </div>
    @endif

    @if($isOpenOutput)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Form Output</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                    <textarea wire:model="output" rows="4" placeholder="Keterangan Output" class="w-full border rounded px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                    @error('output') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded font-medium hover:bg-gray-200 transition-colors">Batal</button>
                <button wire:click="storeOutput" class="px-5 py-2.5 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    @if($isOpenPJ)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Penanggung Jawab</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded border text-sm text-gray-600 italic">"{{ $pj_kegiatan_text }}"</div>
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
                <h3 class="text-xl font-bold text-gray-800">{{ $isEditMode ? 'Edit Indikator' : 'Tambah Indikator' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="space-y-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label><textarea wire:model="ind_keterangan" rows="3" class="w-full border rounded px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>@error('ind_keterangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                    <select wire:model="ind_satuan" class="w-full border rounded px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
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
                        <option value="Laporan">Laporan</option>
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
            <div class="mt-8 flex justify-end gap-3"><button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded font-medium hover:bg-gray-200 transition-colors">Batal</button><button wire:click="storeIndikator" class="px-5 py-2.5 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 transition-colors shadow-sm">Simpan</button></div>
        </div>
    </div>
    @endif

    @if($isOpenTarget)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Form Target</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="grid grid-cols-12 gap-4 max-h-[60vh] overflow-y-auto">
                @foreach([2025,2026,2027,2028,2029,2030] as $y)
                <div class="col-span-3 py-2"><label class="text-sm font-medium text-gray-700">Target {{$y}}</label></div>
                <div class="col-span-9 relative"><input type="text" wire:model="target_{{$y}}" class="w-full border rounded px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"><div class="absolute inset-y-0 right-0 px-4 flex items-center bg-gray-50 border-l text-sm text-gray-500">{{ $target_satuan ?? 'Angka' }}</div></div>
                @endforeach
            </div>
            <div class="mt-8 flex justify-end gap-3"><button wire:click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded font-medium hover:bg-gray-200 transition-colors">Batal</button><button wire:click="simpanTarget" class="px-5 py-2.5 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 transition-colors shadow-sm">Simpan</button></div>
        </div>
    </div>
    @endif
</div>