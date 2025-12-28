@props(['node'])

<div class="relative group z-20">
    {{-- UBAH LEBAR DISINI: w-[900px] agar lebih lega --}}
    <div class="w-[900px] bg-white border border-gray-400 shadow-xl rounded-md overflow-hidden flex flex-col transition-all duration-300">
        
        {{-- ========================================== --}}
        {{-- STATE 1: MODE TAMPILAN (LOCKED / RESULT) --}}
        {{-- ========================================== --}}
        @if($node->is_locked)
            
            {{-- Header Jabatan --}}
            <div class="p-4 border-b border-gray-200 text-center relative bg-gray-50">
                <h3 class="font-bold text-gray-800 text-lg uppercase tracking-wide">
                    {{ $node->jabatan ?: 'Jabatan Belum Dipilih' }}
                </h3>
                
                {{-- Tombol Edit Jabatan (Unlock) HANYA ADMIN --}}
                @if(auth()->user()->hasRole('admin'))
                <button wire:click="unlockNode({{ $node->original_index }})" class="absolute right-3 top-3 text-gray-400 hover:text-blue-600 transition-colors p-1" title="Edit Jabatan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                @endif
            </div>

            {{-- Tabel Content --}}
            <div class="p-0">
                {{-- table-fixed: Agar lebar kolom paten sesuai persentase width --}}
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-300">
                            {{-- ATUR LEBAR KOLOM DISINI --}}
                            <th class="p-3 text-xs font-bold text-gray-700 w-[30%] border-r border-gray-300 text-center uppercase">Kinerja Utama</th>
                            <th class="p-3 text-xs font-bold text-gray-700 w-[40%] border-r border-gray-300 text-center uppercase">Indikator</th>
                            <th class="p-0 text-xs font-bold text-gray-700 w-[20%] text-center uppercase border-r border-gray-300">
                                <div class="border-b border-gray-300 p-1.5 bg-gray-200">Target</div>
                                <div class="flex">
                                    <div class="w-1/2 p-1.5 border-r border-gray-300">Nilai</div>
                                    <div class="w-1/2 p-1.5">Satuan</div>
                                </div>
                            </th>
                            {{-- HEADER AKSI HANYA ADMIN --}}
                            @if(auth()->user()->hasRole('admin'))
                            <th class="w-[10%] text-center text-xs font-bold text-gray-700 uppercase p-3">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @foreach($node->kinerja_items as $kIndex => $kinerja)
                            
                            {{-- BARIS 1: KINERJA UTAMA (HEADER GROUP) --}}
                            <tr class="border-b border-gray-200 bg-white hover:bg-gray-50 transition-colors">
                                {{-- Input Kinerja Utama --}}
                                <td class="p-3 border-r border-gray-200 align-top">
                                    <textarea 
                                        wire:model.blur="visualNodes.{{ $node->original_index }}.kinerja_items.{{ $kIndex }}.kinerja_utama"
                                        x-data="{ resize() { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } }"
                                        x-init="resize()" @input="resize()"
                                        @if(!auth()->user()->hasRole('admin')) disabled @endif
                                        class="w-full border-none bg-transparent focus:ring-0 text-sm p-1 resize-none overflow-hidden min-h-[50px] leading-relaxed placeholder-gray-400 font-medium text-gray-800 disabled:opacity-100 disabled:bg-transparent" 
                                        placeholder="Ketik Kinerja Utama disini..."></textarea>
                                </td>
                                
                                {{-- Tombol Tambah Indikator --}}
                                <td class="p-3 border-r border-gray-200 align-middle">
                                    @if(auth()->user()->hasRole('admin'))
                                    <button wire:click="addIndikatorItem({{ $node->original_index }}, {{ $kIndex }})"
                                            class="w-full bg-pink-500 hover:bg-pink-600 text-white text-xs font-bold py-2.5 rounded shadow-sm transition-colors flex items-center justify-center gap-2 group/btn">
                                        <span class="bg-white/20 rounded-full w-4 h-4 flex items-center justify-center group-hover/btn:bg-white/30 transition">+</span> 
                                        Tambah Indikator
                                    </button>
                                    @endif
                                </td>

                                {{-- Kolom Target (Kosong di baris judul) --}}
                                <td class="p-3 align-middle bg-gray-50 border-r border-gray-200"></td>

                                {{-- Aksi Hapus Kinerja HANYA ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-2 text-center align-middle">
                                    <button wire:click="removeKinerjaItem({{ $node->original_index }}, {{ $kIndex }})" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition-colors" title="Hapus seluruh baris Kinerja ini">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                                @endif
                            </tr>

                            {{-- BARIS 2 dst: LIST INDIKATOR (LOOPING ANAK) --}}
                            @foreach($kinerja['indikators'] as $iIndex => $indikator)
                            <tr class="border-b border-gray-200 bg-white">
                                {{-- Spacer Kinerja (Visual Indentasi) --}}
                                <td class="border-r border-gray-200 bg-gray-50/30"></td>

                                {{-- Input Indikator --}}
                                <td class="p-2 border-r border-gray-200 align-top">
                                    <div class="flex gap-2">
                                        <span class="text-gray-300 mt-1">•</span>
                                        <textarea 
                                            wire:model.blur="visualNodes.{{ $node->original_index }}.kinerja_items.{{ $kIndex }}.indikators.{{ $iIndex }}.nama"
                                            x-data="{ resize() { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } }"
                                            x-init="resize()" @input="resize()"
                                            @if(!auth()->user()->hasRole('admin')) disabled @endif
                                            class="w-full border border-gray-300 rounded text-xs p-2 focus:ring-blue-500 focus:border-blue-500 resize-none overflow-hidden min-h-[40px] leading-relaxed bg-gray-50 focus:bg-white transition-colors disabled:bg-gray-50 disabled:border-transparent disabled:text-gray-600" 
                                            placeholder="Ketik Indikator disini..."></textarea>
                                    </div>
                                </td>

                                {{-- Input Target (Nilai & Satuan) --}}
                                <td class="p-0 align-top border-r border-gray-200">
                                    <div class="flex h-full items-stretch">
                                        {{-- Nilai --}}
                                        <div class="w-1/2 p-1 border-r border-gray-200">
                                            <input type="text" 
                                                wire:model.blur="visualNodes.{{ $node->original_index }}.kinerja_items.{{ $kIndex }}.indikators.{{ $iIndex }}.nilai" 
                                                @if(!auth()->user()->hasRole('admin')) disabled @endif
                                                class="w-full h-full min-h-[40px] text-center text-sm font-semibold text-gray-700 border-transparent focus:ring-0 bg-transparent placeholder-gray-300 disabled:opacity-100" 
                                                placeholder="0">
                                        </div>
                                        {{-- Satuan --}}
                                        <div class="w-1/2 p-1">
                                            <input type="text" 
                                                wire:model.blur="visualNodes.{{ $node->original_index }}.kinerja_items.{{ $kIndex }}.indikators.{{ $iIndex }}.satuan" 
                                                @if(!auth()->user()->hasRole('admin')) disabled @endif
                                                class="w-full h-full min-h-[40px] text-center text-sm text-gray-600 border-transparent focus:ring-0 bg-transparent placeholder-gray-300 disabled:opacity-100" 
                                                placeholder="%">
                                        </div>
                                    </div>
                                </td>

                                {{-- Aksi Simpan & Hapus Indikator HANYA ADMIN --}}
                                @if(auth()->user()->hasRole('admin'))
                                <td class="p-2 text-center align-middle">
                                    <div class="flex justify-center gap-2">
                                        {{-- Tombol Save (Optional UX) --}}
                                        <button wire:click="saveNodeData({{ $node->original_index }})" class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition" title="Simpan data baris ini">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                        </button>
                                        {{-- Tombol Hapus --}}
                                        <button wire:click="removeIndikatorItem({{ $node->original_index }}, {{ $kIndex }}, {{ $iIndex }})" class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100 transition" title="Hapus indikator ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach

                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Tombol Tambah Kinerja HANYA ADMIN --}}
            @if(auth()->user()->hasRole('admin'))
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <button wire:click="addKinerjaItem({{ $node->original_index }})" 
                        class="w-full bg-white border-2 border-dashed border-pink-300 hover:border-pink-500 text-pink-500 hover:text-pink-600 font-bold py-2.5 rounded-lg text-sm shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Kinerja Utama Baru
                </button>
            </div>
            @endif

            {{-- Footer Aksi Pohon (Bawah Luar) HANYA ADMIN --}}
            @if(auth()->user()->hasRole('admin'))
            <div class="flex justify-center gap-3 p-4 bg-white border-t border-gray-100">
                <button wire:click="addManualChild('{{ $node->id }}')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    TAMBAH CABANG
                </button>
                <button wire:click="deleteManualNode('{{ $node->id }}')" wire:confirm="Hapus pohon ini? Data database juga akan terhapus."
                        class="bg-red-500 hover:bg-red-600 text-white py-2 px-6 rounded-lg text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    HAPUS POHON
                </button>
            </div>
            @endif

        @else
            {{-- ========================================== --}}
            {{-- STATE 2: MODE INPUT (LOCK / PILIH JABATAN) --}}
            {{-- ========================================== --}}
            <div class="p-8 bg-white flex flex-col items-center gap-6">
                @if(auth()->user()->hasRole('admin'))
                    <div class="text-center w-full">
                        <div class="mb-4">
                            <span class="inline-block p-3 rounded-full bg-blue-50 text-blue-500 mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </span>
                            <h4 class="font-bold text-gray-700 text-lg">Pilih Unit Kerja / Jabatan</h4>
                            <p class="text-xs text-gray-400">Silakan pilih jabatan untuk memulai cascading</p>
                        </div>
                        
                        {{-- DROPDOWN DINAMIS --}}
                        <div class="relative">
                            <select wire:model="visualNodes.{{ $node->original_index }}.jabatan" 
                                    class="w-full appearance-none border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white text-gray-700 cursor-pointer hover:border-blue-400 transition-colors">
                                <option value="">-- Klik untuk memilih --</option>
                                
                                @if(isset($listJabatans) && count($listJabatans) > 0)
                                    @foreach($listJabatans as $jab)
                                        @php
                                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $jab->level ?? 0);
                                            $arrow = ($jab->level ?? 0) > 0 ? '↳ ' : '';
                                            $style = ($jab->level == 0) ? 'font-weight:bold; color:#1f2937;' : 'color:#4b5563;';
                                        @endphp
                                        <option value="{{ $jab->nama }}" style="{{ $style }}">
                                            {!! $indent . $arrow . $jab->nama !!}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Data Jabatan Kosong</option>
                                @endif
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <button wire:click="lockNode({{ $node->original_index }})" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Mulai Input Data
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-300 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-500">Node Belum Diatur</h4>
                        <p class="text-xs text-gray-400 mt-1">Menunggu input data dari Admin.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>