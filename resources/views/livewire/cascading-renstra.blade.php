<div>
    <x-slot:title>Cascading Renstra</x-slot>

    {{-- BREADCRUMB --}}
    <x-slot:breadcrumb>
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Perencanaan Kinerja</span>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">Cascading Renstra</span>
    </x-slot>

    {{-- ALERT MESSAGES --}}
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
        
        {{-- ======================================================================== --}}
        {{-- BAGIAN 1: TABEL DATA LAMA (DATA DATABASE) --}}
        {{-- ======================================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Data Cascading Renstra</h3>
                <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Cascading Baru
                </button>
            </div>
             <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-gray-700 font-bold border-b border-gray-200">
                                <th class="pb-3 w-6/12">Sasaran / Kondisi</th>
                                <th class="pb-3 w-3/12">Indikator</th>
                                <th class="pb-3 w-3/12 text-right">Menu</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($pohons as $pohon)
                                <tr class="border-b border-gray-100 last:border-0 group hover:bg-gray-50">
                                    <td class="py-4 align-top text-gray-800">
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
                                    <td class="py-4 align-top text-gray-700">
                                        @if($pohon->indikators->count() > 0)
                                            <ol class="list-decimal list-inside space-y-1">
                                                @foreach($pohon->indikators as $ind) 
                                                    <li>{{ $ind->nama_indikator }}</li> 
                                                @endforeach
                                            </ol>
                                        @else <span class="text-gray-400 italic">-</span> @endif
                                    </td>
                                    <td class="py-4 align-top text-right">
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="openIndikator({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">Indikator</button>
                                            <button wire:click="addChild({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">Tambah</button>
                                            <button wire:click="edit({{ $pohon->id }})" class="px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-white text-xs rounded">Edit</button>
                                            <button wire:click="delete({{ $pohon->id }})" wire:confirm="Hapus?" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-8 text-center text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ======================================================================== --}}
        {{-- BAGIAN 2: VISUALISASI CANVAS (EDIT MODE) --}}
        {{-- ======================================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8 pb-4">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center relative z-20">
                <div class="flex items-center gap-4">
                    <h3 class="font-bold text-gray-800 text-lg">Visualisasi Pohon Kinerja (Struktur)</h3>
                    {{-- TOMBOL PREVIEW --}}
                    <button wire:click="openPreview" class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Preview Data
                    </button>
                </div>
                <div class="text-xs text-gray-500 italic">Gunakan scroll mouse untuk Zoom, klik & tahan untuk Geser.</div>
            </div>

            <div x-data="{ zoom: 0.8, panning: false, pointX: 0, pointY: 50, startX: 0, startY: 0 }" 
                 class="relative w-full h-[800px] bg-gray-100 overflow-hidden cursor-grab active:cursor-grabbing border-b border-gray-200"
                 @mousedown="panning = true; startX = $event.clientX - pointX; startY = $event.clientY - pointY"
                 @mousemove="if(panning) { pointX = $event.clientX - startX; pointY = $event.clientY - startY }"
                 @mouseup="panning = false" @mouseleave="panning = false" @wheel.prevent="zoom += $event.deltaY * -0.001">
                
                <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image: radial-gradient(#6b7280 1px, transparent 1px); background-size: 20px 20px;"></div>
                
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white p-2 rounded shadow border border-gray-200">
                    <button @click="zoom += 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded">+</button>
                    <button @click="zoom -= 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded">-</button>
                    <button @click="zoom = 0.8; pointX = 0; pointY = 50" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-xs text-gray-600 rounded">R</button>
                </div>

                {{-- CANVAS TREE CONTAINER --}}
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

    {{-- ======================================================================== --}}
    {{-- MODAL PREVIEW (UPDATED: POHON BERWARNA SESUAI GAMBAR) --}}
    {{-- ======================================================================== --}}
    @if($modalPreviewOpen)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-90 backdrop-blur-md p-0">
        <div class="bg-white w-full h-full flex flex-col overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="px-6 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 shadow-sm z-20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-500 rounded-lg text-white shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Preview Cascading Renstra</h3>
                        <p class="text-xs text-gray-500">Visualisasi Hierarki Kinerja</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="closePreview" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        TUTUP
                    </button>
                </div>
            </div>

            {{-- Content Modal (Scrollable Area dengan Zoom) --}}
            <div x-data="{ zoom: 1, panning: false, pointX: 0, pointY: 0, startX: 0, startY: 0 }" 
                 class="flex-1 overflow-hidden relative bg-gray-100 cursor-grab active:cursor-grabbing"
                 @mousedown="panning = true; startX = $event.clientX - pointX; startY = $event.clientY - pointY"
                 @mousemove="if(panning) { pointX = $event.clientX - startX; pointY = $event.clientY - startY }"
                 @mouseup="panning = false" @mouseleave="panning = false" @wheel.prevent="zoom += $event.deltaY * -0.001">

                {{-- Grid Background --}}
                <div class="absolute inset-0 pointer-events-none opacity-5" style="background-image: radial-gradient(#6b7280 1px, transparent 1px); background-size: 20px 20px;"></div>

                {{-- Zoom Controls Modal --}}
                <div class="absolute bottom-8 right-8 z-30 flex flex-col gap-2 bg-white p-2 rounded shadow-lg border border-gray-200">
                    <button @click="zoom += 0.1" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded text-lg">+</button>
                    <button @click="zoom -= 0.1" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-gray-600 rounded text-lg">-</button>
                    <button @click="zoom = 1; pointX = 0; pointY = 0" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-xs text-gray-600 rounded">RESET</button>
                </div>

                {{-- Tree Container Preview (Menggunakan Partial Baru: preview-tree-node) --}}
                <div class="w-full min-h-full flex justify-center items-start pt-20 origin-top" 
                     :style="`transform: translate(${pointX}px, ${pointY}px) scale(${zoom}); transition: transform 0.1s linear;`">
                    
                    <div class="flex flex-row gap-16">
                        @forelse($manualTree as $root)
                            {{-- Panggil partial preview-tree-node yang sudah dibuat --}}
                            {{-- Level 0 = Merah (Kepala Dinas) --}}
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

    {{-- ======================================================================== --}}
    {{-- MODAL LEGACY (TETAP ADA) --}}
    {{-- ======================================================================== --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Data' : ($isChild ? 'Tambah Anak' : 'Buat Cascading Baru') }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 space-y-6">
                    @if(!$isChild || $isEditMode)
                        @if(!$parent_id)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Sasaran RPJMD</label>
                            <select wire:model="tujuan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
                                <option value="">Pilih Sasaran RPJMD</option>
                                @foreach($sasaran_rpjmds as $item) <option value="{{ $item->id }}">{{ $item->sasaran_rpjmd }}</option> @endforeach
                            </select>
                        </div>
                        @endif
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kinerja / Kondisi</label>
                        <textarea wire:model="nama_pohon" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Batal</button>
                    <button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    @if($isOpenIndikator)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Kelola Indikator</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-12 gap-4 items-end">
                            <div class="col-span-5"><label class="text-xs font-bold text-gray-700">Nama Indikator</label><input type="text" wire:model="indikator_input" class="w-full border rounded px-3 py-2 text-sm"></div>
                            <div class="col-span-2"><label class="text-xs font-bold text-gray-700">Nilai</label><input type="number" wire:model="indikator_nilai" class="w-full border rounded px-3 py-2 text-sm"></div>
                            <div class="col-span-2"><label class="text-xs font-bold text-gray-700">Satuan</label><input type="text" wire:model="indikator_satuan" class="w-full border rounded px-3 py-2 text-sm"></div>
                            <div class="col-span-3"><button wire:click="addIndikatorToList" class="w-full bg-blue-600 text-white py-2 rounded text-sm hover:bg-blue-700">Tambahkan</button></div>
                        </div>
                    </div>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full text-left text-sm"><thead class="bg-gray-100 border-b"><tr><th class="p-3">Indikator</th><th class="p-3">Nilai</th><th class="p-3">Satuan</th><th class="p-3">Aksi</th></tr></thead>
                        <tbody>@foreach($indikator_list as $index => $ind) <tr class="hover:bg-gray-50"><td class="p-3">{{ $ind['nama'] }}</td><td class="p-3">{{ $ind['nilai'] }}</td><td class="p-3">{{ $ind['satuan'] }}</td><td class="p-3"><button wire:click="removeIndikatorFromList({{ $index }})" class="text-red-500 text-xs font-bold hover:underline">Hapus</button></td></tr> @endforeach</tbody></table>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Tutup</button>
                    <button wire:click="saveIndikators" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </div>
        </div>
    @endif
</div>