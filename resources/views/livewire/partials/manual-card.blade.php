@props(['node', 'color', 'width'])

{{-- Konfigurasi Warna Berdasarkan Level (dikirim dari parent) --}}
@php
    $borderColor = match($color) {
        'blue' => 'border-blue-500',
        'cyan' => 'border-cyan-500',
        'purple' => 'border-purple-500',
        default => 'border-gray-500',
    };
    $headerColor = match($color) {
        'blue' => 'bg-blue-700',
        'cyan' => 'bg-cyan-600',
        'purple' => 'bg-purple-700',
        default => 'bg-gray-700',
    };
    $btnColor = match($color) {
        'blue' => 'bg-blue-600 hover:bg-blue-700',
        'cyan' => 'bg-cyan-600 hover:bg-cyan-700',
        'purple' => 'bg-purple-600 hover:bg-purple-700',
        default => 'bg-gray-600',
    };
@endphp

<div class="relative group z-20">
    <div class="{{ $width }} bg-white border {{ $borderColor }} shadow-lg rounded-sm overflow-hidden relative transition-all hover:shadow-xl">
        
        {{-- HEADER KARTU (Opsional, bisa untuk Nama Jabatan/Level) --}}
        {{-- <div class="bg-white p-2 border-b border-gray-200 text-center">
             <h2 class="font-bold text-gray-800 text-xs uppercase tracking-wide">Manual Input</h2> 
        </div> --}}

        <div class="p-0">
            {{-- TABEL TAMPILAN (Sesuai Request Anda) --}}
            <div class="">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="{{ $headerColor }} text-white font-semibold text-center text-[10px] p-1.5 border-r border-white/20 w-1/2 align-middle">
                                Kinerja Utama
                            </th>
                            <th class="{{ $headerColor }} text-white font-semibold text-center text-[10px] p-1.5 w-1/2 align-middle">
                                Indikator & Target
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 text-xs bg-white">
                        <tr class="border-b {{ $borderColor }}">
                            {{-- KOLOM 1: KINERJA UTAMA --}}
                            <td class="p-2 border-r {{ $borderColor }} align-top font-bold text-gray-700 leading-snug">
                                {{-- Jika kosong, tampilkan placeholder agar bisa diklik --}}
                                {{ $node->kinerja_utama ?: '(Klik tombol Input...)' }}
                            </td>

                            {{-- KOLOM 2: INDIKATOR & TARGET --}}
                            <td class="p-2 align-top space-y-2">
                                @if($node->indikator)
                                    <div class="text-gray-600">{{ $node->indikator }}</div>
                                    
                                    {{-- Tampilkan Target dalam badge kecil --}}
                                    @if($node->target_nilai)
                                        <div class="inline-block bg-gray-100 border border-gray-200 text-[10px] px-1.5 py-0.5 rounded text-gray-600 font-semibold mt-1">
                                            Target: {{ $node->target_nilai }} {{ $node->target_satuan }}
                                        </div>
                                    @endif
                                @else
                                    <span class="italic text-gray-400 text-[10px]">Belum ada indikator</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- TOMBOL AKSI (FOOTER) --}}
            <div class="p-2 bg-gray-50 flex flex-col gap-2 border-t border-gray-100">
                
                {{-- Tombol UTAMA: INPUT DATA (Membuka Modal Form) --}}
                <div class="flex justify-center">
                    <button wire:click="openManualInput('{{ $node->id }}')" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1.5 rounded text-[11px] font-bold shadow-sm transition-colors flex items-center justify-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit Inputan
                    </button>
                </div>
                
                {{-- Tombol TAMBAHAN: Tambah Anak & Hapus --}}
                <div class="flex gap-2 opacity-80 hover:opacity-100 transition-opacity duration-200 pt-1 border-t border-gray-200/50">
                    
                    {{-- Tombol Tambah Anak (Non-aktif di level ungu/terakhir jika mau dibatasi) --}}
                    @if($color !== 'purple')
                    <button wire:click="addManualChild('{{ $node->id }}')" class="flex-1 {{ $btnColor }} text-white py-1.5 rounded text-[10px] font-medium shadow-sm transition-colors text-center uppercase tracking-wide">
                        + Cabang
                    </button>
                    @endif

                    {{-- Tombol Hapus --}}
                    <button wire:click="deleteManualNode('{{ $node->id }}')" wire:confirm="Yakin hapus kotak ini beserta anak-anaknya?" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-1.5 rounded text-[10px] font-medium shadow-sm transition-colors text-center uppercase tracking-wide">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>