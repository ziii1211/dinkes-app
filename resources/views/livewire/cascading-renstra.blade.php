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
        
        <!-- INFO BOX -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 shadow-sm flex gap-4 items-start">
            <div class="text-blue-500 mt-1 flex-shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-base mb-1">Penjelasan</h4>
                <p class="text-blue-600 text-sm leading-relaxed font-medium">
                    Cascading SKPD adalah proses penjabaran tujuan dan sasaran dari dokumen perencanaan di tingkat atas (nasional → provinsi → kabupaten/kota → SKPD), hingga ke tujuan dan sasaran SKPD. Lakukan secara sistematis, maka SKPD memiliki kontribusi yang jelas terhadap pencapaian sasaran daerah — inilah yang disebut cascading.
                </p>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-800 text-lg">Crosscutting Renstra</h3>
                <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah
                </button>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-gray-700 font-bold border-b border-gray-200 bg-gray-50">
                                <th class="p-4 w-16 text-center">No</th>
                                <th class="p-4 w-2/12">Antar</th>
                                <th class="p-4 w-4/12">Sumber</th>
                                <th class="p-4 w-4/12">Tujuan</th>
                                <th class="p-4 w-24 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @forelse($crosscuttings as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 text-center">{{ $index + 1 }}</td>
                                    
                                    <td class="p-4 uppercase font-medium text-xs">
                                        @php
                                            $sumberType = class_basename($item->sumber_type);
                                            $tujuanType = class_basename($item->tujuan_type);
                                            if($sumberType == 'Kegiatan') $sumberType = 'OUTPUT';
                                            if($tujuanType == 'Kegiatan') $tujuanType = 'OUTPUT';
                                        @endphp
                                        <span class="bg-gray-100 px-2 py-1 rounded text-gray-500">
                                            {{ strtoupper($sumberType) }} &rarr; {{ strtoupper($tujuanType) }}
                                        </span>
                                    </td>

                                    <td class="p-4 leading-relaxed">
                                        {{ $this->getKinerjaLabel($item->sumber) }}
                                    </td>

                                    <td class="p-4 leading-relaxed">
                                        {{ $this->getKinerjaLabel($item->tujuan) }}
                                    </td>

                                    <td class="p-4 text-center">
                                        <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus data crosscutting ini?" class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400 italic bg-gray-50 border border-dashed border-gray-200 rounded-lg">
                                        Belum ada data Crosscutting. Silakan klik tombol <strong>Tambah</strong>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH DENGAN CUSTOM DROPDOWN -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                    <h3 class="text-lg font-bold text-gray-800">Tambah Crosscutting</h3>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar">
                
                <!-- 1. PILIH KINERJA SUMBER (CUSTOM SELECT) -->
                <div x-data="{ 
                    open: false, 
                    search: '', 
                    selectedLabel: 'Pilih Sasaran / Outcome / Output', 
                    value: @entangle('selected_sumber') 
                }" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Pilih Kinerja Sumber <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Trigger Button -->
                    <button @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" type="button" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-left text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex justify-between items-center shadow-sm">
                        <span x-text="value ? $refs['opt_'+value]?.innerText : 'Pilih Sasaran / Outcome / Output'" class="truncate" :class="value ? 'text-gray-900' : 'text-gray-500'"></span>
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="open" @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-80 overflow-hidden flex flex-col" style="display: none;">
                        <!-- Search Box -->
                        <div class="p-2 border-b border-gray-100 bg-gray-50 sticky top-0">
                            <input x-model="search" x-ref="searchInput" type="text" placeholder="Cari kinerja..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Options List -->
                        <div class="overflow-y-auto flex-1 p-1">
                            @php
                                $groups = [
                                    'Sasaran' => $kinerja_list->where('type', 'sasaran'),
                                    'Outcome' => $kinerja_list->where('type', 'outcome'),
                                    'Output' => $kinerja_list->where('type', 'kegiatan')
                                ];
                            @endphp

                            @foreach($groups as $label => $items)
                                <div x-show="'{{ strtolower($label) }}'.includes(search.toLowerCase()) || $el.querySelectorAll('li').length > 0">
                                    <div class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50 mt-1 rounded">{{ $label }} ({{ $items->count() }})</div>
                                    <ul class="mt-1 space-y-1">
                                        @foreach($items as $item)
                                            <li x-show="'{{ strtolower($item->teks) }}'.includes(search.toLowerCase())" 
                                                @click="value = '{{ $item->type }}|{{ $item->id }}'; open = false"
                                                class="px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded cursor-pointer transition-colors"
                                                :class="value === '{{ $item->type }}|{{ $item->id }}' ? 'bg-blue-50 text-blue-600 font-medium' : ''">
                                                <!-- Hidden ref for label display -->
                                                <span x-ref="opt_{{ $item->type }}|{{ $item->id }}" style="display:none">{{ $item->teks }}</span>
                                                {{ $item->teks }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('selected_sumber') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- 2. PILIH SKPD TUJUAN -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Pilih SKPD Tujuan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select wire:model="skpd_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none appearance-none bg-white shadow-sm">
                            <option value="">Pilih SKPD</option>
                            @foreach($skpds as $skpd)
                                <option value="{{ $skpd->id }}">{{ $skpd->nama_skpd }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                    </div>
                    @error('skpd_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- 3. PILIH KINERJA TUJUAN (CUSTOM SELECT - SAME LOGIC) -->
                <div x-data="{ 
                    open: false, 
                    search: '', 
                    value: @entangle('selected_tujuan') 
                }" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pilih Kinerja Tujuan <span class="text-red-500">*</span>
                    </label>
                    
                    <button @click="open = !open; if(open) $nextTick(() => $refs.searchInputTujuan.focus())" type="button" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-left text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex justify-between items-center shadow-sm">
                        <span x-text="value ? $refs['opt_t_'+value]?.innerText : 'Pilih Sasaran / Outcome / Output'" class="truncate" :class="value ? 'text-gray-900' : 'text-gray-500'"></span>
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-80 overflow-hidden flex flex-col" style="display: none;">
                        <div class="p-2 border-b border-gray-100 bg-gray-50 sticky top-0">
                            <input x-model="search" x-ref="searchInputTujuan" type="text" placeholder="Cari kinerja..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="overflow-y-auto flex-1 p-1">
                            @foreach($groups as $label => $items)
                                <div x-show="'{{ strtolower($label) }}'.includes(search.toLowerCase()) || $el.querySelectorAll('li').length > 0">
                                    <div class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50 mt-1 rounded">{{ $label }} ({{ $items->count() }})</div>
                                    <ul class="mt-1 space-y-1">
                                        @foreach($items as $item)
                                            <li x-show="'{{ strtolower($item->teks) }}'.includes(search.toLowerCase())" 
                                                @click="value = '{{ $item->type }}|{{ $item->id }}'; open = false"
                                                class="px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded cursor-pointer transition-colors"
                                                :class="value === '{{ $item->type }}|{{ $item->id }}' ? 'bg-blue-50 text-blue-600 font-medium' : ''">
                                                <!-- Hidden ref with unique ID prefix for Tujuan -->
                                                <span x-ref="opt_t_{{ $item->type }}|{{ $item->id }}" style="display:none">{{ $item->teks }}</span>
                                                {{ $item->teks }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('selected_tujuan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                <button wire:click="closeModal" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    Batal
                </button>
                <button wire:click="store" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>