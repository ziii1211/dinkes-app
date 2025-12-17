<div>
    <x-slot:title>Cascading Renstra</x-slot>

    <x-slot:breadcrumb>
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Perencanaan Kinerja</span>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">Cascading Renstra</span>
    </x-slot>

    <div class="space-y-6">
        
        {{-- BAGIAN 1: TABEL DATA --}}
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
                                            <div class="text-gray-600">{{ $pohon->nama_pohon }}</div>
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
                                            <button wire:click="addChild({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">Tambah Anak</button>
                                            <button wire:click="edit({{ $pohon->id }})" class="px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-white text-xs rounded">Edit</button>
                                            <button wire:click="delete({{ $pohon->id }})" wire:confirm="Hapus? Anak-anaknya juga akan terhapus." class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded">Hapus</button>
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

        {{-- BAGIAN 2: TABEL CROSSCUTTING --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Crosscutting Cascading Renstra</h3>
                <button wire:click="openCrosscuttingModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Crosscutting
                </button>
            </div>
            <div class="p-6">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="text-gray-700 font-bold border-b border-gray-200 bg-gray-50">
                            <th class="p-3 w-4/12">Sumber</th>
                            <th class="p-3 w-3/12">Tujuan OPD</th>
                            <th class="p-3 w-4/12">Cascading Renstra OPD</th>
                            <th class="p-3 w-1/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($crosscuttings as $cc)
                            <tr>
                                <td class="p-3 text-gray-900">{{ $cc->pohonSumber->nama_pohon ?? '-' }}</td>
                                <td class="p-3 text-gray-900">{{ $cc->skpdTujuan->nama_skpd ?? '-' }}</td>
                                <td class="p-3 text-gray-900">{{ $cc->pohonTujuan->nama_pohon ?? '-' }}</td>
                                <td class="p-3 text-center">
                                    <button wire:click="deleteCrosscutting({{ $cc->id }})" wire:confirm="Hapus data ini?" class="p-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded text-xs">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-400 italic">Belum ada data Crosscutting.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $crosscuttings->links() }}</div>
            </div>
        </div>

        {{-- BAGIAN 3: VISUALISASI (Diagram) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8 pb-4">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center relative z-20">
                <h3 class="font-bold text-gray-800 text-lg">Visualisasi Cascading Renstra</h3>
                <div class="text-sm text-gray-500 italic">Generated otomatis dari data di atas</div>
            </div>

            {{-- AREA CANVAS --}}
            <div x-data="{ zoom: 1, panning: false, pointX: 0, pointY: 0, startX: 0, startY: 0 }" 
                 class="relative w-full h-[800px] bg-gray-100 overflow-hidden cursor-grab active:cursor-grabbing border-b border-gray-200"
                 @mousedown="panning = true; startX = $event.clientX - pointX; startY = $event.clientY - pointY"
                 @mousemove="if(panning) { pointX = $event.clientX - startX; pointY = $event.clientY - startY }"
                 @mouseup="panning = false" @mouseleave="panning = false" @wheel.prevent="zoom += $event.deltaY * -0.001">
                
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white p-2 rounded shadow border border-gray-200">
                    <button @click="zoom += 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold">+</button>
                    <button @click="zoom -= 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold">-</button>
                    <button @click="zoom = 1; pointX = 0; pointY = 0" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 font-bold text-xs">R</button>
                </div>

                <div class="w-full min-h-full flex justify-center p-10 origin-top" :style="`transform: translate(${pointX}px, ${pointY}px) scale(${zoom}); transition: transform 0.1s linear;`">
                    <div class="flex flex-col gap-16 items-center">
                        @forelse($pohons->where('parent_id', null) as $root)
                        <div class="flex flex-col items-center">
                            {{-- CARD ROOT --}}
                            @include('livewire.partials.pohon-card', ['node' => $root])

                            {{-- LEVEL 2: CHILDREN --}}
                            @if($root->children->count() > 0)
                                <div class="h-12 w-px bg-gray-400"></div>
                                <div class="flex gap-16 items-start relative">
                                    @if($root->children->count() > 1)
                                        <div class="absolute top-0 left-0 h-px bg-gray-400" style="left: 50%; transform: translateX(-50%); width: {{ ($root->children->count() - 1) * 664 }}px;"></div>
                                    @endif

                                    @foreach($root->children as $child)
                                    <div class="flex flex-col items-center relative">
                                        <div class="h-8 w-px bg-gray-400 -mt-8 mb-0"></div> 
                                        @include('livewire.partials.pohon-card', ['node' => $child])

                                        {{-- LEVEL 3: GRANDCHILDREN --}}
                                        @if($child->children->count() > 0)
                                            <div class="h-12 w-px bg-gray-400"></div> 
                                            <div class="flex gap-16 items-start relative">
                                                @if($child->children->count() > 1)
                                                    <div class="absolute top-0 left-0 h-px bg-gray-400" style="left: 50%; transform: translateX(-50%); width: {{ ($child->children->count() - 1) * 664 }}px;"></div>
                                                @endif
                                                @foreach($child->children as $grandchild)
                                                <div class="flex flex-col items-center relative">
                                                    <div class="h-8 w-px bg-gray-400 -mt-8 mb-0"></div>
                                                    @include('livewire.partials.pohon-card', ['node' => $grandchild])
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @empty
                            <div class="text-gray-400">Belum ada data untuk divisualisasikan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 1: FORM UTAMA --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Data' : ($isChild ? 'Tambah Anak' : 'Buat Cascading Baru') }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
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
                    @error('nama_pohon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Batal</button>
                <button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 2: INDIKATOR --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Kelola Indikator</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-12 gap-4 items-end">
                        <div class="col-span-5">
                            <label class="text-xs font-bold text-gray-700">Nama Indikator</label>
                            <input type="text" wire:model="indikator_input" class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-gray-700">Nilai</label>
                            <input type="number" wire:model="indikator_nilai" class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-bold text-gray-700">Satuan</label>
                            <input type="text" wire:model="indikator_satuan" class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="col-span-3">
                            <button wire:click="addIndikatorToList" class="w-full bg-blue-600 text-white py-2 rounded text-sm hover:bg-blue-700">Tambahkan</button>
                        </div>
                    </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr><th class="p-3">Indikator</th><th class="p-3">Nilai</th><th class="p-3">Satuan</th><th class="p-3">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach($indikator_list as $index => $ind) 
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3">{{ $ind['nama'] }}</td>
                                    <td class="p-3">{{ $ind['nilai'] }}</td>
                                    <td class="p-3">{{ $ind['satuan'] }}</td>
                                    <td class="p-3">
                                        <button wire:click="removeIndikatorFromList({{ $index }})" class="text-red-500 text-xs font-bold hover:underline">Hapus</button>
                                    </td>
                                </tr> 
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Tutup</button>
                <button wire:click="saveIndikators" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 3: CROSSCUTTING --}}
    @if($isOpenCrosscutting)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Tambah Crosscutting</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                @if($cross_sumber_id)
                    <div class="bg-blue-50 p-3 rounded text-xs text-blue-700 mb-2 border border-blue-100">
                        <strong>Kinerja Sumber:</strong> <span class="italic">Terpilih otomatis dari diagram.</span>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kinerja Sumber</label>
                        <select wire:model="cross_sumber_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">-- Ambil dari Data Cascading Renstra --</option>
                            @foreach($opsiPohon as $pk)
                                <option value="{{ $pk->id }}">{{ $pk->nama_pohon }}</option>
                            @endforeach
                        </select>
                        @error('cross_sumber_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih SKPD Tujuan</label>
                    <select wire:model="cross_skpd_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <option value="">-- Pilih SKPD Tujuan --</option>
                        @foreach($opsiSkpd as $s) <option value="{{ $s->id }}">{{ $s->nama_skpd }}</option> @endforeach
                    </select>
                    @error('cross_skpd_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kinerja Tujuan</label>
                    <select wire:model="cross_tujuan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <option value="">-- Pilih Kinerja Tujuan --</option>
                        @foreach($opsiPohon as $p) <option value="{{ $p->id }}">{{ $p->nama_pohon }}</option> @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">*Data diambil persis sama dengan Pilih Kinerja Sumber</p>
                    @error('cross_tujuan_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Batal</button>
                <button wire:click="storeCrosscutting" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>