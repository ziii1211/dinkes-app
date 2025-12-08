<div>
    <x-slot:title>Pohon Kinerja</x-slot>

    <x-slot:breadcrumb>
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Perencanaan Kinerja</span>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">Pohon Kinerja</span>
    </x-slot>

    <div class="space-y-6">
        
        {{-- BAGIAN 1: TABEL DATA (DATA MANUAL) --}}
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
                            {{-- Filter: Data 'visualisasi' & 'crosscutting' TIDAK muncul di tabel --}}
                            @forelse($pohons->whereNotIn('jenis', ['visualisasi', 'crosscutting']) as $pohon)
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

        {{-- BAGIAN 2: TABEL CROSSCUTTING (Updated Headers & Content) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Crosscutting Pohon Kinerja</h3>
                
                {{-- Tombol Tambah Crosscutting --}}
                <button wire:click="openCrosscuttingModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Crosscutting
                </button>
            </div>
            <div class="p-6">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="text-gray-700 font-bold border-b border-gray-200 bg-gray-50">
                            {{-- Header Diperbarui & Kolom No Dihapus --}}
                            <th class="p-3 w-4/12">Sumber</th>
                            <th class="p-3 w-3/12">Tujuan OPD</th>
                            <th class="p-3 w-4/12">Pohon Kinerja OPD</th>
                            <th class="p-3 w-1/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($crosscuttings as $index => $cc)
                            <tr>
                                {{-- Kolom Sumber (Hapus tulisan jenis/hide/visualisasi) --}}
                                <td class="p-3 text-gray-900">
                                    {{ $cc->pohonSumber->nama_pohon ?? '-' }}
                                </td>
                                
                                {{-- Kolom Tujuan OPD (Warna Hitam) --}}
                                <td class="p-3 text-gray-900">
                                    {{ $cc->skpdTujuan->nama_skpd ?? '-' }}
                                </td>
                                
                                {{-- Kolom Pohon Kinerja OPD --}}
                                <td class="p-3 text-gray-900">
                                    {{ $cc->pohonTujuan->nama_pohon ?? '-' }}
                                </td>
                                
                                {{-- Aksi --}}
                                <td class="p-3 text-center">
                                    <button wire:click="deleteCrosscutting({{ $cc->id }})" wire:confirm="Hapus data ini?" class="p-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-400 italic">Belum ada data Crosscutting.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $crosscuttings->links() }}
                </div>
            </div>
        </div>

        {{-- BAGIAN 3: VISUALISASI POHON KINERJA (ZOOMABLE) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8 pb-4">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center relative z-20">
                <h3 class="font-bold text-gray-800 text-lg">Visualisasi Pohon Kinerja</h3>
                <button wire:click="openModalKinerjaUtama" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow flex items-center gap-2 transform transition hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Kinerja Utama
                </button>
            </div>

            {{-- AREA CANVAS (Zoom & Pan) --}}
            <div 
                x-data="{ 
                    zoom: 1, 
                    panning: false, 
                    pointX: 0, 
                    pointY: 0, 
                    startX: 0, 
                    startY: 0,
                    init() {
                        this.$watch('zoom', value => {
                            if (value < 0.5) this.zoom = 0.5;
                            if (value > 2) this.zoom = 2;
                        });
                    }
                }" 
                class="relative w-full h-[800px] bg-gray-100 overflow-hidden cursor-grab active:cursor-grabbing border-b border-gray-200"
                @mousedown="panning = true; startX = $event.clientX - pointX; startY = $event.clientY - pointY"
                @mousemove="if(panning) { pointX = $event.clientX - startX; pointY = $event.clientY - startY }"
                @mouseup="panning = false"
                @mouseleave="panning = false"
                @wheel.prevent="zoom += $event.deltaY * -0.001"
            >
                {{-- TOOLBAR ZOOM --}}
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white p-2 rounded shadow border border-gray-200">
                    <button @click="zoom += 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 rounded text-gray-700 font-bold text-lg">+</button>
                    <span class="text-xs text-center font-mono text-gray-500" x-text="Math.round(zoom * 100) + '%'"></span>
                    <button @click="zoom -= 0.1" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 rounded text-gray-700 font-bold text-lg">-</button>
                    <button @click="zoom = 1; pointX = 0; pointY = 0" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-100 rounded text-gray-700 font-bold text-xs" title="Reset">R</button>
                </div>

                {{-- KONTEN POHON (TREE STRUCTURE) --}}
                <div 
                    class="w-full min-h-full flex justify-center p-10 origin-top"
                    :style="`transform: translate(${pointX}px, ${pointY}px) scale(${zoom}); transition: transform 0.1s linear;`"
                >
                    <div class="flex flex-col gap-16 items-center">
                        
                        {{-- LOOP ROOT (INDUK) --}}
                        @forelse($pohons->where('parent_id', null)->where('jenis', '!=', 'hide') as $root)
                        <div class="flex flex-col items-center">
                            
                            {{-- === KOTAK INDUK (PARENT) === --}}
                            <div class="bg-white border border-gray-300 shadow-lg max-w-2xl w-[600px] rounded-sm overflow-hidden relative z-10">
                                <div class="bg-white p-2 border-b border-gray-200 text-center">
                                    <h2 class="font-bold text-gray-800 text-sm uppercase tracking-wide">DINAS KESEHATAN</h2> 
                                </div>
                                <div class="p-3">
                                    <div class="border border-purple-500">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="bg-purple-700 text-white font-semibold text-center text-xs p-2 border-r border-purple-500 w-1/3 align-middle">Kinerja Utama</th>
                                                    <th rowspan="2" class="bg-purple-700 text-white font-semibold text-center text-xs p-2 border-r border-purple-500 w-1/3 align-middle">Indikator</th>
                                                    <th colspan="2" class="bg-purple-700 text-white font-semibold text-center text-xs p-2 border-b border-purple-500">Target</th>
                                                </tr>
                                                <tr>
                                                    <th class="bg-purple-700 text-white font-semibold text-center text-xs p-1 border-r border-purple-500 w-16">Nilai</th>
                                                    <th class="bg-purple-700 text-white font-semibold text-center text-xs p-1">Satuan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-800 text-xs bg-white">
                                                @if($root->indikators->count() > 0)
                                                    @foreach($root->indikators as $index => $ind)
                                                    <tr class="border-b border-purple-500 hover:bg-gray-50">
                                                        @if($index === 0)<td rowspan="{{ $root->indikators->count() }}" class="p-2 border-r border-purple-500 align-top font-medium">{{ $root->nama_pohon }}</td>@endif
                                                        <td class="p-2 border-r border-purple-500">{{ $ind->nama_indikator }}</td>
                                                        <td class="p-2 border-r border-purple-500 text-center font-medium">{{ $ind->target ?? '-' }}</td>
                                                        <td class="p-2 text-center">{{ $ind->satuan ?? '-' }}</td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="border-b border-purple-500"><td class="p-2 border-r border-purple-500 align-top font-medium">{{ $root->nama_pohon }}</td><td class="p-2 border-r border-purple-500 italic text-gray-400">Belum ada indikator</td><td class="p-2 border-r border-purple-500 text-center">-</td><td class="p-2 text-center">-</td></tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4 flex flex-col gap-2">
                                        <div class="flex justify-center"><button wire:click="openIndikator({{ $root->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-xs font-bold flex items-center shadow-sm"><span class="mr-1 text-sm">+</span> Indikator</button></div>
                                        <div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
                                            <button wire:click="deleteKinerjaUtama({{ $root->id }})" wire:confirm="Hapus visualisasi Kinerja Utama ini? Data pada tabel tidak akan terhapus." class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">Hapus Kinerja Utama</button>
                                            <button wire:click="openCrosscuttingModal({{ $root->id }})" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">Tambah Crosscutting Perangkat Daerah</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- === LEVEL 2: ANAK (CROSSCUTTING) === --}}
                            @if($root->children->count() > 0)
                                <div class="h-12 w-px bg-gray-400"></div>
                                <div class="flex gap-16 items-start relative">
                                    @if($root->children->count() > 1)
                                        <div class="absolute top-0 left-0 h-px bg-gray-400" style="left: 50%; transform: translateX(-50%); width: {{ ($root->children->count() - 1) * 664 }}px;"></div>
                                    @endif

                                    @foreach($root->children as $child)
                                    <div class="flex flex-col items-center relative">
                                        <div class="h-8 w-px bg-gray-400 -mt-8 mb-0"></div> 
                                        
                                        {{-- KOTAK ANAK --}}
                                        <div class="bg-white border border-gray-300 shadow-lg max-w-2xl w-[600px] rounded-sm overflow-hidden relative z-10">
                                            <div class="bg-white p-2 border-b border-gray-200 text-center">
                                                <h2 class="font-bold text-gray-800 text-sm uppercase tracking-wide">DINAS KESEHATAN</h2> 
                                            </div>
                                            <div class="p-3">
                                                <div class="border border-purple-500">
                                                    <table class="w-full text-left border-collapse">
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="2" class="bg-purple-700 text-white font-semibold text-center text-xs p-2 border-r border-purple-500 w-1/3 align-middle">Kinerja Utama</th>
                                                                <th rowspan="2" class="bg-purple-700 text-white font-semibold text-center text-xs p-2 border-r border-purple-500 w-1/3 align-middle">Indikator</th>
                                                                <th colspan="2" class="bg-purple-700 text-white font-semibold text-center text-xs p-2 border-b border-purple-500">Target</th>
                                                            </tr>
                                                            <tr>
                                                                <th class="bg-purple-700 text-white font-semibold text-center text-xs p-1 border-r border-purple-500 w-16">Nilai</th>
                                                                <th class="bg-purple-700 text-white font-semibold text-center text-xs p-1">Satuan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-gray-800 text-xs bg-white">
                                                            @if($child->indikators->count() > 0)
                                                                @foreach($child->indikators as $cIndex => $cInd)
                                                                <tr class="border-b border-purple-500 hover:bg-gray-50">
                                                                    @if($cIndex === 0)
                                                                        <td rowspan="{{ $child->indikators->count() }}" class="p-2 border-r border-purple-500 align-top bg-white font-medium text-gray-900">{{ $child->nama_pohon }}</td>
                                                                    @endif
                                                                    <td class="p-2 border-r border-purple-500">{{ $cInd->nama_indikator }}</td>
                                                                    <td class="p-2 border-r border-purple-500 text-center font-medium">{{ $cInd->target ?? '-' }}</td>
                                                                    <td class="p-2 text-center">{{ $cInd->satuan ?? '-' }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @else
                                                                <tr class="border-b border-purple-500"><td class="p-2 border-r border-purple-500 align-top font-medium">{{ $child->nama_pohon }}</td><td class="p-2 border-r border-purple-500 italic text-gray-400">Belum ada indikator</td><td class="p-2 border-r border-purple-500 text-center">-</td><td class="p-2 text-center">-</td></tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Tombol Aksi Anak --}}
                                                <div class="mt-4 flex flex-col gap-2">
                                                    <div class="flex justify-center"><button wire:click="openIndikator({{ $child->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-xs font-bold flex items-center shadow-sm"><span class="mr-1 text-sm">+</span> Indikator</button></div>
                                                    <div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
                                                        <button wire:click="deleteKinerjaUtama({{ $child->id }})" wire:confirm="Hapus Crosscutting ini?" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">Hapus Kinerja Utama</button>
                                                        <button wire:click="openCrosscuttingModal({{ $child->id }})" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">Tambah Crosscutting Perangkat Daerah</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- === LEVEL 3: CUCU (GRANDCHILDREN) === --}}
                                        @if($child->children->count() > 0)
                                            <div class="h-12 w-px bg-gray-400"></div> 
                                            
                                            <div class="flex gap-16 items-start relative">
                                                @if($child->children->count() > 1)
                                                    <div class="absolute top-0 left-0 h-px bg-gray-400" style="left: 50%; transform: translateX(-50%); width: {{ ($child->children->count() - 1) * 664 }}px;"></div>
                                                @endif

                                                @foreach($child->children as $grandchild)
                                                <div class="flex flex-col items-center relative">
                                                    <div class="h-8 w-px bg-gray-400 -mt-8 mb-0"></div>
                                                    
                                                    {{-- BOX CUCU --}}
                                                    <div class="bg-white border border-gray-300 shadow-lg max-w-2xl w-[600px] rounded-sm overflow-hidden relative z-10">
                                                        <div class="bg-white p-2 border-b border-gray-200 text-center">
                                                            <h2 class="font-bold text-gray-800 text-sm uppercase tracking-wide">DINAS KESEHATAN</h2> 
                                                        </div>
                                                        <div class="p-3">
                                                            <div class="border border-purple-500">
                                                                <table class="w-full text-left border-collapse">
                                                                    <tbody class="text-gray-800 text-xs bg-white">
                                                                        @if($grandchild->indikators->count() > 0)
                                                                            @foreach($grandchild->indikators as $gIndex => $gInd)
                                                                            <tr class="border-b border-purple-500 hover:bg-gray-50">
                                                                                @if($gIndex === 0)
                                                                                    <td rowspan="{{ $grandchild->indikators->count() }}" class="p-2 border-r border-purple-500 align-top bg-white font-medium text-gray-900">{{ $grandchild->nama_pohon }}</td>
                                                                                @endif
                                                                                <td class="p-2 border-r border-purple-500">{{ $gInd->nama_indikator }}</td>
                                                                                <td class="p-2 border-r border-purple-500 text-center font-medium">{{ $gInd->target ?? '-' }}</td>
                                                                                <td class="p-2 text-center">{{ $gInd->satuan ?? '-' }}</td>
                                                                            </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr class="border-b border-purple-500"><td class="p-2 border-r border-purple-500 align-top font-medium">{{ $grandchild->nama_pohon }}</td><td class="p-2 border-r border-purple-500 italic text-gray-400">Belum ada indikator</td><td class="p-2 border-r border-purple-500 text-center">-</td><td class="p-2 text-center">-</td></tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="mt-4 flex flex-col gap-2">
                                                                <div class="flex justify-center"><button wire:click="openIndikator({{ $grandchild->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-xs font-bold flex items-center shadow-sm"><span class="mr-1 text-sm">+</span> Indikator</button></div>
                                                                <div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
                                                                    <button wire:click="deleteKinerjaUtama({{ $grandchild->id }})" wire:confirm="Hapus Crosscutting?" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">Hapus Kinerja Utama</button>
                                                                    <button wire:click="openCrosscuttingModal({{ $grandchild->id }})" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">Tambah Crosscutting Perangkat Daerah</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                                <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                                <p class="text-sm">Belum ada Visualisasi.</p>
                                <p class="text-xs">Klik tombol "Tambah Kinerja Utama" di atas untuk memulai.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 1: FORM UTAMA --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Data' : ($modeKinerjaUtama ? 'Tambah Kinerja Utama' : ($isChild ? 'Tambah Kondisi' : 'Buat Pohon Baru')) }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-6">
                @if($modeKinerjaUtama)
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Unit Kerja (SKPD)</label><select wire:model="unit_kerja_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"><option value="">-- Pilih Unit Kerja --</option>@foreach($skpds as $skpd) <option value="{{ $skpd->id }}">{{ $skpd->nama_skpd }}</option> @endforeach</select>@error('unit_kerja_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kinerja (Referensi)</label><select wire:model="kinerja_utama_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"><option value="">-- Pilih Kinerja --</option>@foreach($all_pohons as $ref) <option value="{{ $ref->id }}">{{ $ref->nama_pohon }}</option> @endforeach</select>@error('kinerja_utama_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                @else
                    @if(!$isChild || $isEditMode)<div><label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Sasaran RPJMD</label><select wire:model="tujuan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"><option value="">Pilih Sasaran RPJMD</option>@foreach($sasaran_rpjmds as $item) <option value="{{ $item->id }}">{{ $item->sasaran_rpjmd }}</option> @endforeach</select></div>@endif
                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi Yang Diharapkan</label><textarea wire:model="nama_pohon" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"></textarea></div>
                @endif
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3"><button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Batal</button><button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg">Simpan</button></div>
        </div>
    </div>
    @endif

    {{-- MODAL 2: INDIKATOR --}}
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center"><h3 class="text-lg font-bold text-gray-800">Kelola Indikator</h3><button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>
            <div class="p-6 space-y-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200"><div class="grid grid-cols-12 gap-4 items-end"><div class="col-span-5"><label class="text-xs font-bold text-gray-700">Nama Indikator</label><input type="text" wire:model="indikator_input" class="w-full border rounded px-3 py-2 text-sm"></div><div class="col-span-2"><label class="text-xs font-bold text-gray-700">Nilai</label><input type="number" wire:model="indikator_nilai" class="w-full border rounded px-3 py-2 text-sm"></div><div class="col-span-2"><label class="text-xs font-bold text-gray-700">Satuan</label><input type="text" wire:model="indikator_satuan" class="w-full border rounded px-3 py-2 text-sm"></div><div class="col-span-3"><button wire:click="addIndikatorToList" class="w-full bg-blue-600 text-white py-2 rounded text-sm">Tambahkan</button></div></div></div>
                <div class="border rounded-lg overflow-hidden"><table class="w-full text-left text-sm"><thead class="bg-gray-100 border-b"><tr><th class="p-3">Indikator</th><th class="p-3">Nilai</th><th class="p-3">Satuan</th><th class="p-3">Aksi</th></tr></thead><tbody>@foreach($indikator_list as $index => $ind) <tr class="hover:bg-gray-50"><td class="p-3">{{ $ind['nama'] }}</td><td class="p-3">{{ $ind['nilai'] }}</td><td class="p-3">{{ $ind['satuan'] }}</td><td class="p-3"><button wire:click="removeIndikatorFromList({{ $index }})" class="text-red-500 text-xs font-bold">Hapus</button></td></tr> @endforeach</tbody></table></div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3"><button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg">Tutup</button><button wire:click="saveIndikators" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg">Simpan</button></div>
        </div>
    </div>
    @endif

    {{-- MODAL 3: CROSSCUTTING --}}
    @if($isOpenCrosscutting)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Tambah Crosscutting</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-4">
                
                {{-- 1. Pilih Kinerja Sumber --}}
                @if($active_pohon_id)
                    {{-- Jika dibuka dari Tree (Visualisasi), otomatis terpilih --}}
                    <div class="bg-blue-50 p-3 rounded text-xs text-blue-700 mb-2 border border-blue-100">
                        <strong>Kinerja Sumber:</strong> <span class="italic">Terpilih otomatis dari diagram.</span>
                    </div>
                @else
                    {{-- Jika dibuka dari Tombol Tambah Tabel, user harus memilih --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kinerja Sumber</label>
                        <select wire:model="cross_sumber_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Ambil dari Data Pohon Kinerja (Induk/Anak/Cucu) --</option>
                            @foreach($opsiPohon as $pk)
                                <option value="{{ $pk->id }}">
                                    {{ $pk->nama_pohon }} ({{ $pk->jenis ?? 'Manual' }})
                                </option>
                            @endforeach
                        </select>
                        @error('cross_sumber_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif

                {{-- 2. Pilih SKPD Tujuan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih SKPD Tujuan</label>
                    <select wire:model="cross_skpd_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih SKPD Tujuan --</option>
                        @foreach($opsiSkpd as $s) 
                            <option value="{{ $s->id }}">{{ $s->nama_skpd }}</option> 
                        @endforeach
                    </select>
                    @error('cross_skpd_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- 3. Pilih Kinerja Tujuan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kinerja Tujuan</label>
                    <select wire:model="cross_tujuan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kinerja Tujuan (Sama dengan Sumber) --</option>
                        @foreach($opsiPohon as $p) 
                            <option value="{{ $p->id }}">{{ $p->nama_pohon }}</option> 
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">*Data diambil persis sama dengan Pilih Kinerja Sumber</p>
                    @error('cross_tujuan_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button wire:click="storeCrosscutting" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>