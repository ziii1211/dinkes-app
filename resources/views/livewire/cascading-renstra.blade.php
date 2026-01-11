<div>
    <x-slot:title>Cascading Renstra</x-slot>

    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Perencanaan Kinerja</span>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Cascading Renstra</span>
        </div>
    </x-slot>

    {{-- Alert Messages --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm z-50">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm z-50">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        
        {{-- BAGIAN 1: TABEL DATA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-4 md:px-6 md:py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Data Cascading Renstra</h3>
                
                {{-- TOMBOL TAMBAH HANYA UNTUK ADMIN --}}
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="openModal" class="w-full md:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Cascading Baru
                </button>
                @endif
            </div>
             <div class="p-4 md:p-6">
                {{-- WRAPPER SCROLL HORIZONTAL --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200 min-h-[400px] pb-4">
                    <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
                        <thead>
                            <tr class="text-gray-700 font-bold border-b border-gray-200 bg-gray-50">
                                {{-- Min-width agar tabel tidak gepeng --}}
                                <th class="p-4 min-w-[350px]">Sasaran / Kondisi</th>
                                <th class="p-4 min-w-[300px]">Indikator</th>
                                {{-- HEADER MENU HANYA UNTUK ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                {{-- Min-width besar (400px) agar tombol aksi muat satu baris --}}
                                <th class="p-4 min-w-[400px] text-right">Menu</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($pohons as $pohon)
                                <tr class="border-b border-gray-100 last:border-0 group hover:bg-gray-50 bg-white">
                                    <td class="p-4 align-top text-gray-800 whitespace-normal">
                                        @if($pohon->depth == 0)
                                            @if($pohon->tujuan)
                                                <div class="font-bold text-gray-900 text-base mb-2">{{ $pohon->tujuan->sasaran_rpjmd }}</div>
                                            @endif
                                            <div class="text-gray-600 font-medium">{{ $pohon->nama_pohon }}</div>
                                        @else
                                            <div class="flex items-start text-gray-600" style="padding-left: {{ $pohon->depth * 1.5 }}rem;">
                                                <span class="text-gray-400 mr-2 font-bold">@for($i = 0; $i < $pohon->depth; $i++)↳@endfor</span>
                                                <span>{{ $pohon->nama_pohon }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4 align-top text-gray-700 whitespace-normal">
                                        @if($pohon->indikators->count() > 0)
                                            <ol class="list-decimal list-inside space-y-1">
                                                @foreach($pohon->indikators as $ind) <li>{{ $ind->nama_indikator }}</li> @endforeach
                                            </ol>
                                        @else <span class="text-gray-400 italic">-</span> @endif
                                    </td>
                                    
                                    {{-- MENU AKSI: DIKEMBALIKAN SEPERTI SEMULA (TOMBOL BERJAJAR) --}}
                                    @if(auth()->user()->hasRole('admin'))
                                    <td class="p-4 align-top text-right">
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="openIndikator({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors shadow-sm whitespace-nowrap">
                                                Indikator
                                            </button>
                                            
                                            <button wire:click="addChild({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors shadow-sm whitespace-nowrap">
                                                + Tambah Kondisi
                                            </button>
                                            
                                            <button wire:click="edit({{ $pohon->id }})" class="px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-white text-xs rounded transition-colors shadow-sm">
                                                Edit
                                            </button>
                                            
                                            <button wire:click="delete({{ $pohon->id }})" wire:confirm="Yakin ingin menghapus data ini beserta turunannya?" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition-colors shadow-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole('admin') ? 3 : 2 }}" class="p-8 text-center text-gray-400 italic bg-white">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: VISUALISASI CANVAS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8 pb-4">
            <div class="px-4 py-4 md:px-6 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-20">
                <div class="flex items-center gap-4">
                    <h3 class="font-bold text-gray-800 text-lg">Visualisasi Pohon Kinerja</h3>
                    <button wire:click="openPreview" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Preview
                    </button>
                </div>
                <div class="text-xs text-gray-500 italic">Scroll untuk Zoom, Klik & Tahan untuk Geser.</div>
            </div>

            <div x-data="{ zoom: 0.8, panning: false, pointX: 0, pointY: 50, startX: 0, startY: 0 }" 
                 class="relative w-full h-[600px] md:h-[800px] bg-gray-100 overflow-hidden cursor-grab active:cursor-grabbing border-b border-gray-200"
                 @mousedown="panning = true; startX = $event.clientX - pointX; startY = $event.clientY - pointY"
                 @mousemove="if(panning) { pointX = $event.clientX - startX; pointY = $event.clientY - startY }"
                 @mouseup="panning = false" @mouseleave="panning = false" @wheel.prevent="zoom += $event.deltaY * -0.001">
                
                <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image: radial-gradient(#6b7280 1px, transparent 1px); background-size: 20px 20px;"></div>
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white p-2 rounded shadow border border-gray-200">
                    <button @click="zoom += 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded">+</button>
                    <button @click="zoom -= 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded">-</button>
                    <button @click="zoom = 0.8; pointX = 0; pointY = 50" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-xs text-gray-600 rounded">R</button>
                </div>

                <div class="w-full min-h-full flex justify-center items-start pt-20 origin-top" :style="`transform: translate(${pointX}px, ${pointY}px) scale(${zoom}); transition: transform 0.1s linear;`">
                    <div class="flex flex-row gap-32">
                        @forelse($manualTree as $root)
                            @include('livewire.partials.tree-node', ['node' => $root, 'isRoot' => true])
                        @empty
                            <div class="flex flex-col items-center mt-20 opacity-60">
                                <span class="text-gray-500 font-medium">Data Visualisasi Kosong.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW --}}
    @if($modalPreviewOpen)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-90 backdrop-blur-md p-0">
        <div class="bg-white w-full h-full flex flex-col overflow-hidden animate-fade-in">
            <div class="px-6 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 shadow-sm z-20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-500 rounded-lg text-white shadow-sm hidden md:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800">Preview Cascading</h3>
                        <p class="text-xs text-gray-500 hidden md:block">Visualisasi Hierarki Kinerja</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="closePreview" class="px-4 md:px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-md transition-colors flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        TUTUP
                    </button>
                </div>
            </div>

            <div x-data="{ zoom: 1, panning: false, pointX: 0, pointY: 0, startX: 0, startY: 0 }" 
                 class="flex-1 overflow-hidden relative bg-gray-100 cursor-grab active:cursor-grabbing"
                 @mousedown="panning = true; startX = $event.clientX - pointX; startY = $event.clientY - pointY"
                 @mousemove="if(panning) { pointX = $event.clientX - startX; pointY = $event.clientY - startY }"
                 @mouseup="panning = false" @mouseleave="panning = false" @wheel.prevent="zoom += $event.deltaY * -0.001">

                <div class="absolute inset-0 pointer-events-none opacity-5" style="background-image: radial-gradient(#6b7280 1px, transparent 1px); background-size: 20px 20px;"></div>

                <div class="absolute bottom-8 right-8 z-30 flex flex-col gap-2 bg-white p-2 rounded shadow-lg border border-gray-200">
                    <button @click="zoom += 0.1" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded text-lg">+</button>
                    <button @click="zoom -= 0.1" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded text-lg">-</button>
                    <button @click="zoom = 1; pointX = 0; pointY = 0" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-xs text-gray-600 rounded">R</button>
                </div>

                <div class="w-full min-h-full flex justify-center items-start pt-20 origin-top" 
                     :style="`transform: translate(${pointX}px, ${pointY}px) scale(${zoom}); transition: transform 0.1s linear;`">
                    
                    <div class="flex flex-row gap-16 px-10">
                        @forelse($manualTree as $root)
                            @include('livewire.partials.preview-tree-node', ['node' => $root, 'level' => 0])
                        @empty
                            <div class="text-gray-400 italic text-xl mt-20">Tidak ada data untuk dipreview.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL FORM --}}
    @if(auth()->user()->hasRole('admin'))
        @if($isOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                        <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Data' : ($isChild ? 'Tambah Anak' : 'Buat Cascading Baru') }}</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    <div class="p-6 space-y-6 overflow-y-auto">
                        @if(!$isChild || $isEditMode)
                            @if(!$parent_id)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Sasaran RPJMD</label>
                                <select wire:model="tujuan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    <option value="">Pilih Sasaran RPJMD</option>
                                    @foreach($sasaran_rpjmds as $item) <option value="{{ $item->id }}">{{ $item->sasaran_rpjmd }}</option> @endforeach
                                </select>
                            </div>
                            @endif
                        @endif
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kinerja / Kondisi</label>
                            <textarea wire:model="nama_pohon" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 shrink-0">
                        <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">Batal</button>
                        <button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">Simpan</button>
                    </div>
                </div>
            </div>
        @endif

        @if($isOpenIndikator)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                        <h3 class="text-lg font-bold text-gray-800">Kelola Indikator</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    <div class="p-6 space-y-6 overflow-y-auto">
                        
                        {{-- Form Input --}}
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex flex-col md:flex-row gap-3 items-end">
                                <div class="w-full">
                                    <label class="text-xs font-bold text-gray-700 mb-1 block">Nama Indikator</label>
                                    <input type="text" wire:model="indikator_input" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 outline-none" placeholder="Ketik nama indikator...">
                                </div>
                                <button wire:click="addIndikatorToList" class="w-full md:w-auto bg-blue-600 text-white py-2 px-4 rounded text-sm hover:bg-blue-700 font-bold shadow-sm whitespace-nowrap">
                                    Tambahkan
                                </button>
                            </div>
                        </div>

                        {{-- Tabel List Indikator --}}
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-100 border-b">
                                    <tr>
                                        <th class="p-3 w-10 text-center">#</th>
                                        <th class="p-3">Nama Indikator</th>
                                        <th class="p-3 w-20 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($indikator_list as $index => $ind) 
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 text-center text-gray-500">{{ $index + 1 }}</td>
                                        <td class="p-3 font-medium text-gray-700">{{ $ind['nama'] }}</td>
                                        <td class="p-3 text-center">
                                            <button wire:click="removeIndikatorFromList({{ $index }})" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr> 
                                    @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-400 italic">Belum ada indikator ditambahkan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                        <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">Tutup</button>
                        <button wire:click="saveIndikators" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm font-medium">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>