@props(['node', 'level' => 0])

{{-- KONFIGURASI WARNA BERDASARKAN LEVEL --}}
@php
    $headerColor = match($level) {
        0 => 'bg-[#dc3545] text-white', // Merah (Level 0 - Kepala Dinas)
        1 => 'bg-[#0d6efd] text-white', // Biru (Level 1 - Kabid)
        2 => 'bg-[#ffc107] text-black', // Kuning (Level 2 - Subbag/Seksi)
        default => 'bg-gray-600 text-white', // Level > 2
    };
    
    $borderColor = match($level) {
        0 => 'border-[#dc3545]',
        1 => 'border-[#0d6efd]',
        2 => 'border-[#ffc107]',
        default => 'border-gray-600',
    };
@endphp

<div class="flex flex-col items-center">
    
    {{-- CARD NODE --}}
    <div class="w-[450px] bg-white border-2 {{ $borderColor }} shadow-sm z-10 mb-0">
        
        {{-- HEADER JABATAN --}}
        <div class="{{ $headerColor }} p-2 text-center font-bold text-xs uppercase border-b border-gray-300">
            {{ $node->jabatan ?: 'Jabatan Belum Diisi' }}
        </div>

        {{-- BODY TABEL GRID --}}
        <div class="text-[10px] text-gray-800 bg-white">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-1 w-[35%] text-center">Kinerja</th>
                        <th class="border border-gray-300 p-1 w-[45%] text-center">Indikator</th>
                        <th class="border border-gray-300 p-1 w-[20%] text-center">Target</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($node->kinerja_items as $kinerja)
                        @php 
                            $rowSpan = count($kinerja['indikators']) > 0 ? count($kinerja['indikators']) : 1; 
                        @endphp

                        @if(count($kinerja['indikators']) > 0)
                            @foreach($kinerja['indikators'] as $index => $ind)
                                <tr>
                                    {{-- Kolom Kinerja (Rowspan) --}}
                                    @if($index === 0)
                                        <td rowspan="{{ $rowSpan }}" class="border border-gray-300 p-1.5 align-top bg-white">
                                            {{ $kinerja['kinerja_utama'] }}
                                        </td>
                                    @endif

                                    {{-- Kolom Indikator --}}
                                    <td class="border border-gray-300 p-1.5 align-top">
                                        {{ $ind['nama'] }}
                                    </td>

                                    {{-- Kolom Target --}}
                                    <td class="border border-gray-300 p-1.5 text-center align-top bg-gray-50">
                                        {{ $ind['nilai'] }} {{ $ind['satuan'] }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            {{-- Jika tidak ada indikator, tampilkan baris kosong --}}
                            <tr>
                                <td class="border border-gray-300 p-1.5 align-top">{{ $kinerja['kinerja_utama'] }}</td>
                                <td class="border border-gray-300 p-1.5 text-center text-gray-400 italic">-</td>
                                <td class="border border-gray-300 p-1.5 text-center text-gray-400">-</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="3" class="border border-gray-300 p-2 text-center text-gray-400 italic">Belum ada data kinerja</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- KONEKTOR / GARIS POHON --}}
    @if($node->children->count() > 0)
        {{-- Garis Turun Parent --}}
        <div class="h-8 w-0.5 bg-gray-400"></div>

        <div class="flex justify-center relative">
            {{-- Garis Horizontal (Crossbar) --}}
            @if($node->children->count() > 1)
                {{-- Logic: Garis melintang dari tengah anak pertama ke tengah anak terakhir --}}
                <div class="absolute top-0 h-0.5 bg-gray-400" 
                     style="left: calc(50% / {{ $node->children->count() }}); right: calc(50% / {{ $node->children->count() }});">
                </div>
            @endif

            {{-- Looping Anak --}}
            @foreach($node->children as $child)
                <div class="flex flex-col items-center relative px-4 pt-0">
                    {{-- Garis Naik Anak --}}
                    <div class="h-8 w-0.5 bg-gray-400"></div>
                    
                    {{-- Rekursif --}}
                    @include('livewire.partials.preview-tree-node', ['node' => $child, 'level' => $level + 1])
                </div>
            @endforeach
        </div>
    @endif
</div>