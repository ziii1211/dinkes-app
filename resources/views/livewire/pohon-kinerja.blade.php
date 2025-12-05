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
        <!-- INFO BOX -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex gap-4 items-start">
            <div class="text-blue-600 mt-1 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-base mb-2">Penjelasan</h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Pohon Kinerja SKPD (Satuan Kerja Perangkat Daerah) adalah representasi visual dan sistematis dari hubungan antara tujuan, sasaran, dan indikator kinerja suatu perangkat daerah dalam rangka pencapaian visi dan misi daerah, terutama dalam dokumen perencanaan seperti RPJMD dan Renstra SKPD.
                </p>
            </div>
        </div>

        <!-- SECTION 1: DATA POHON KINERJA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Data Pohon Kinerja</h3>
                <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Pohon
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
                                                <div class="font-bold text-gray-900 text-base mb-2">
                                                    {{ $pohon->tujuan->sasaran_rpjmd }}
                                                </div>
                                            @endif
                                            <div class="text-gray-600">
                                                {{ $pohon->nama_pohon }}
                                            </div>
                                        @else
                                            <div class="flex items-start text-gray-600" style="padding-left: {{ $pohon->depth * 1.5 }}rem;">
                                                <span class="text-gray-400 mr-2 font-bold">
                                                    @for($i = 0; $i < $pohon->depth; $i++)↳@endfor
                                                </span>
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
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-4 align-top text-right">
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="openIndikator({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg> Indikator</button>
                                            <button wire:click="addChild({{ $pohon->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tambah Kondisi</button>
                                            <button wire:click="edit({{ $pohon->id }})" class="px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-white text-xs rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit</button>
                                            <button wire:click="delete({{ $pohon->id }})" wire:confirm="Hapus Pohon ini?" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                        Belum ada data Pohon Kinerja. Silakan klik tombol <strong>Buat Pohon</strong> di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 2: CROSSCUTTING -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Crosscutting Pohon Kinerja</h3>
                <!-- TOMBOL TAMBAH CROSSCUTTING -->
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
                                    <button wire:click="deleteCrosscutting({{ $cc->id }})" wire:confirm="Hapus data ini?" class="p-1.5 bg-red-500 hover:bg-red-600 text-white rounded transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-400 italic">Belum ada data Crosscutting.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 1: POHON KINERJA -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800">{{ $isEditMode ? 'Edit Pohon Kinerja' : ($isChild ? 'Tambah Kondisi' : 'Tambah Pohon Kinerja') }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-6">
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
                    <textarea wire:model="nama_pohon" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none resize-none" placeholder="Kondisi Yang Diharapkan"></textarea>
                    @error('nama_pohon') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="store" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL 2: INDIKATOR -->
    @if($isOpenIndikator)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <div><h3 class="text-lg font-bold text-gray-800">Tambah Indikator</h3><p class="text-xs text-gray-500 mt-1">Masukkan indikator untuk kondisi terpilih.</p></div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Indikator <span class="text-red-500">*</span></label>
                    <textarea wire:model="indikator_input" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none resize-none"></textarea>
                    @error('indikator_input') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <button wire:click="addIndikatorToList" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg flex items-center justify-center gap-2 shadow-sm">Tambah ke Daftar</button>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-left text-sm"><thead class="bg-gray-50 border-b"><tr><th class="px-4 py-3 w-16 text-center">No</th><th class="px-4 py-3">Uraian Indikator</th><th class="px-4 py-3 w-24 text-center">Aksi</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($indikator_list as $index => $ind)
                                <tr><td class="px-4 py-3 text-center">{{ $index + 1 }}</td><td class="px-4 py-3">{{ $ind['nama'] }}</td><td class="px-4 py-3 text-center"><button wire:click="removeIndikatorFromList({{ $index }})" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded text-xs font-medium">Hapus</button></td></tr>
                            @empty <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 italic">Belum ada indikator.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Batal</button>
                <button wire:click="saveIndikators" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL 3: CROSSCUTTING (BARU) -->
    @if($isOpenCrosscutting)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Tambah Crosscutting
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <!-- Body Modal -->
            <div class="p-6 space-y-6">
                
                <!-- 1. Pilih Kinerja Sumber -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Pilih Kinerja Sumber <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="cross_sumber_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none bg-white">
                        <option value="">Pilih Kinerja Sumber</option>
                        @foreach($all_pohons as $p) <option value="{{ $p->id }}">{{ $p->nama_pohon }}</option> @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pilih kinerja yang menjadi sumber crosscutting.</p>
                    @error('cross_sumber_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- 2. Pilih SKPD Tujuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Pilih SKPD Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="cross_skpd_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none bg-white">
                        <option value="">Pilih SKPD</option>
                        @foreach($skpds as $s) <option value="{{ $s->id }}">{{ $s->nama_skpd }}</option> @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">SKPD tujuan akan menentukan daftar kinerja yang muncul.</p>
                    @error('cross_skpd_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- 3. Pilih Kinerja Tujuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pilih Kinerja Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="cross_tujuan_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-blue-500 outline-none bg-white">
                        <option value="">Pilih Kinerja Tujuan</option>
                        @foreach($all_pohons as $p) <option value="{{ $p->id }}">{{ $p->nama_pohon }}</option> @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Pilih kinerja tujuan setelah SKPD dipilih.</p>
                    @error('cross_tujuan_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

            </div>

            <!-- Footer Modal -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    Batal
                </button>
                <button wire:click="storeCrosscutting" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
    @endif

</div>