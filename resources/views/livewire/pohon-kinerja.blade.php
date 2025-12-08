<div>
    <x-slot:title>Pohon Kinerja</x-slot>

    {{-- CSS KHUSUS ORG CHART --}}
    <style>
        .tree ul { padding-top: 20px; position: relative; transition: all 0.5s; display: flex; justify-content: center; }
        .tree li { float: left; text-align: center; list-style-type: none; position: relative; padding: 20px 5px 0 5px; transition: all 0.5s; }
        .tree li::before, .tree li::after { content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #ccc; width: 50%; height: 20px; }
        .tree li::after { right: auto; left: 50%; border-left: 2px solid #ccc; }
        .tree li:only-child::after, .tree li:only-child::before { display: none; }
        .tree li:only-child { padding-top: 0; }
        .tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
        .tree li:last-child::before{ border-right: 2px solid #ccc; border-radius: 0 5px 0 0; }
        .tree li:first-child::after{ border-radius: 5px 0 0 0; }
        .tree ul ul::before{ content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #ccc; width: 0; height: 20px; }
        
        .tree-card { display: inline-block; background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; min-width: 300px; max-width: 350px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.3s; z-index: 10; position: relative; }
        .tree-card:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2); border-color: #3b82f6; }
        .tree-header { background-color: #4f46e5; color: white; padding: 8px; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
    </style>

    <x-slot:breadcrumb>
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Perencanaan Kinerja</span>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">Pohon Kinerja</span>
    </x-slot>

    <div class="space-y-6">
        
        {{-- BAGIAN 1: INFO & TABEL DATA POHON KINERJA (ASLI) --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex gap-4 items-start">
            <div class="text-blue-600 mt-1 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-base mb-2">Penjelasan</h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Pohon Kinerja SKPD (Satuan Kerja Perangkat Daerah) adalah representasi visual dan sistematis dari hubungan antara tujuan, sasaran, dan indikator kinerja suatu perangkat daerah.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Data Pohon Kinerja (Tabel)</h3>
                <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Pohon Manual
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
                                                @foreach($pohon->indikators as $ind) <li>{{ $ind->nama_indikator }}</li> @endforeach
                                            </ol>
                                        @else <span class="text-gray-400 italic">-</span> @endif
                                    </td>
                                    <td class="py-4 align-top text-right">
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="openIndikator({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">Indikator</button>
                                            <button wire:click="addChild({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">Tambah Kondisi</button>
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

        {{-- BAGIAN 2: TABEL CROSSCUTTING (ASLI) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Crosscutting Pohon Kinerja</h3>
                <button wire:click="openCrosscuttingModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah
                </button>
            </div>
            <div class="p-6">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="text-gray-700 font-bold border-b border-gray-200 bg-gray-50">
                            <th class="p-3 w-4/12">Sumber</th>
                            <th class="p-3 w-3/12">Tujuan OPD</th>
                            <th class="p-3 w-4/12">Pohon Kinerja OPD</th>
                            <th class="p-3 w-1/12 text-center">Menu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($crosscuttings as $cc)
                            <tr>
                                <td class="p-3">{{ $cc->pohonSumber->nama_pohon ?? '-' }}</td>
                                <td class="p-3 font-medium text-gray-700">{{ $cc->skpdTujuan->nama_skpd ?? '-' }}</td>
                                <td class="p-3">{{ $cc->pohonTujuan->nama_pohon ?? '-' }}</td>
                                <td class="p-3 text-center">
                                    <button wire:click="deleteCrosscutting({{ $cc->id }})" wire:confirm="Hapus data ini?" class="p-1.5 bg-red-500 hover:bg-red-600 text-white rounded transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-400 italic">Belum ada data Crosscutting.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- BAGIAN 3: VISUALISASI POHON (ORG CHART) & TOMBOL UTAMA --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative mt-8">
            
            {{-- HEADER: JUDUL & TOMBOL (DIGABUNG DI SINI) --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Visualisasi Pohon Kinerja</h3>
                    <p class="text-xs text-gray-500">Representasi grafis akan muncul otomatis setelah data diinput.</p>
                </div>
                
                {{-- TOMBOL KINERJA UTAMA --}}
                <button wire:click="openModalKinerjaUtama" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-lg shadow hover:shadow-md transition-all gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                     Tambah Kinerja Utama
                </button>
            </div>
            
            <div class="p-6 overflow-x-auto" style="min-height: 500px;">
                <div class="tree min-w-max">
                    <ul>
                        {{-- HANYA LOOPING DATA ASLI (Tidak ada Dummy) --}}
                        {{-- Jika data kosong, ul ini kosong dan tidak menampilkan apa-apa --}}
                        @foreach($pohons->where('parent_id', null) as $root)
                        <li>
                            <div class="tree-card">
                                <div class="tree-header bg-indigo-600">
                                    {{ $root->tujuan->sasaran_rpjmd ?? 'SASARAN RPJMD' }}
                                </div>
                                <div class="p-3 text-left">
                                    <div class="text-xs font-bold text-gray-800 mb-2 border-b pb-2">{{ $root->nama_pohon }}</div>
                                    @if($root->indikators->count() > 0)
                                        <div class="text-[10px] text-gray-600 bg-gray-50 p-2 rounded">
                                            <strong>Indikator:</strong>
                                            <ul class="list-disc list-inside mt-1">
                                                @foreach($root->indikators as $ind) <li>{{ $ind->nama_indikator }}</li> @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- LEVEL 2 --}}
                            @php $children2 = $pohons->where('parent_id', $root->id); @endphp
                            @if($children2->count() > 0)
                            <ul>
                                @foreach($children2 as $child2)
                                <li>
                                    <div class="tree-card">
                                        <div class="tree-header bg-purple-600">KINERJA TURUNAN</div>
                                        <div class="p-3 text-left">
                                            <div class="text-xs font-bold text-gray-800 mb-2 border-b pb-2">{{ $child2->nama_pohon }}</div>
                                            @if($child2->indikators->count() > 0)
                                                <div class="text-[10px] text-gray-600 mb-2">
                                                    @foreach($child2->indikators as $ind) • {{ $ind->nama_indikator }}<br> @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- LEVEL 3 --}}
                                    @php $children3 = $pohons->where('parent_id', $child2->id); @endphp
                                    @if($children3->count() > 0)
                                    <ul>
                                        @foreach($children3 as $child3)
                                        <li>
                                            <div class="tree-card">
                                                <div class="tree-header bg-blue-500">LEVEL 3</div>
                                                <div class="p-3 text-left">
                                                    <div class="text-xs font-bold text-gray-800">{{ $child3->nama_pohon }}</div>
                                                </div>
                                            </div>
                                            
                                            {{-- LEVEL 4 --}}
                                            @php $children4 = $pohons->where('parent_id', $child3->id); @endphp
                                            @if($children4->count() > 0)
                                            <ul>
                                                @foreach($children4 as $child4)
                                                <li>
                                                     <div class="tree-card">
                                                        <div class="tree-header bg-green-500">LEVEL 4</div>
                                                        <div class="p-3 text-left">
                                                            <div class="text-xs font-bold text-gray-800">{{ $child4->nama_pohon }}</div>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif

                                        </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL UTAMA (Form Dinamis) --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">
                    {{ $isEditMode ? 'Edit Data' : ($modeKinerjaUtama ? 'Tambah Kinerja Utama (Pilih Referensi)' : ($isChild ? 'Tambah Kondisi' : 'Buat Pohon Baru')) }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            
            <div class="p-6 space-y-6">
                @if($modeKinerjaUtama)
                    {{-- FORM KHUSUS TOMBOL BESAR --}}
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-4">
                        <p class="text-xs text-blue-700">Fitur ini menghubungkan Unit Kerja dengan Kinerja yang sudah ada.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Unit Kerja (SKPD) <span class="text-red-500">*</span></label>
                        <select wire:model="unit_kerja_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none bg-white">
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($skpds as $skpd) <option value="{{ $skpd->id }}">{{ $skpd->nama_skpd }}</option> @endforeach
                        </select>
                        @error('unit_kerja_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kinerja (Referensi) <span class="text-red-500">*</span></label>
                        <select wire:model="kinerja_utama_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none bg-white">
                            <option value="">-- Pilih Kinerja --</option>
                            @foreach($all_pohons as $ref) <option value="{{ $ref->id }}">{{ $ref->nama_pohon }}</option> @endforeach
                        </select>
                        @error('kinerja_utama_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @else
                    {{-- FORM INPUT MANUAL BIASA --}}
                    @if(!$isChild || $isEditMode)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Sasaran RPJMD <span class="text-red-500">*</span></label>
                        <select wire:model="tujuan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none bg-white">
                            <option value="">Pilih Sasaran RPJMD</option>
                            @foreach($sasaran_rpjmds as $item) <option value="{{ $item->id }}">{{ $item->sasaran_rpjmd }}</option> @endforeach
                        </select>
                        @error('tujuan_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi Yang Diharapkan <span class="text-red-500">*</span></label>
                        <textarea wire:model="nama_pohon" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none resize-none"></textarea>
                        @error('nama_pohon') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL LAIN (INDIKATOR & CROSSCUTTING) --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <div><h3 class="text-lg font-bold text-gray-800">Indikator</h3></div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Indikator</label>
                    <textarea wire:model="indikator_input" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                    @error('indikator_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <button wire:click="addIndikatorToList" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">Tambah ke Daftar</button>
                <div class="border rounded overflow-hidden">
                    <table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="p-2">Indikator</th><th class="p-2 w-16">Aksi</th></tr></thead>
                        <tbody class="divide-y">
                            @foreach($indikator_list as $index => $ind)
                                <tr><td class="p-2">{{ $ind['nama'] }}</td><td class="p-2"><button wire:click="removeIndikatorFromList({{ $index }})" class="text-red-500 text-xs">Hapus</button></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 border rounded">Tutup</button>
                <button wire:click="saveIndikators" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </div>
    @endif
    
    @if($isOpenCrosscutting)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">Tambah Crosscutting</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Kinerja Sumber</label><select wire:model="cross_sumber_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"><option value="">-- Pilih --</option>@foreach($all_pohons as $p)<option value="{{ $p->id }}">{{ $p->nama_pohon }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">SKPD Tujuan</label><select wire:model="cross_skpd_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"><option value="">-- Pilih --</option>@foreach($skpds as $s)<option value="{{ $s->id }}">{{ $s->nama_skpd }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-bold text-gray-700 mb-1">Kinerja Tujuan</label><select wire:model="cross_tujuan_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"><option value="">-- Pilih --</option>@foreach($all_pohons as $p)<option value="{{ $p->id }}">{{ $p->nama_pohon }}</option>@endforeach</select></div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Batal</button>
                <button wire:click="storeCrosscutting" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>