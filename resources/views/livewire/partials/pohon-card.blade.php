<div class="bg-white border border-gray-300 shadow-lg max-w-2xl w-[600px] rounded-sm overflow-hidden relative z-10">
    <div class="bg-white p-2 border-b border-gray-200 text-center">
        <h2 class="font-bold text-gray-800 text-sm uppercase tracking-wide">DINAS KESEHATAN</h2> 
    </div>
    <div class="p-3">
        <div class="border border-purple-500">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="bg-purple-700 text-white font-semibold text-center text-xs p-2 border-r border-purple-500 w-1/2 align-middle">Kinerja Utama</th>
                        <th class="bg-purple-700 text-white font-semibold text-center text-xs p-2 w-1/2 align-middle">Indikator</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 text-xs bg-white">
                    @if($node->indikators->count() > 0)
                        @foreach($node->indikators as $index => $ind)
                        <tr class="border-b border-purple-500 hover:bg-gray-50">
                            @if($index === 0)
                                <td rowspan="{{ $node->indikators->count() }}" class="p-2 border-r border-purple-500 align-top font-medium">{{ $node->nama_pohon }}</td>
                            @endif
                            <td class="p-2">{{ $ind->nama_indikator }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="border-b border-purple-500">
                            <td class="p-2 border-r border-purple-500 align-top font-medium">{{ $node->nama_pohon }}</td>
                            <td class="p-2 italic text-gray-400">Belum ada indikator</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex flex-col gap-2">
            <div class="flex justify-center">
                {{-- Tombol Tambah Indikator --}}
                <button wire:click="openIndikator({{ $node->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-xs font-bold flex items-center shadow-sm">
                    <span class="mr-1 text-sm">+</span> Indikator
                </button>
            </div>
            
            <div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
                {{-- Tombol Tambah Anak (Agar bisa menambah cabang langsung dari diagram) --}}
                <button wire:click="addChild({{ $node->id }})" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">
                    Tambah Anak (Cabang)
                </button>

                {{-- Tombol Crosscutting --}}
                <button wire:click="openCrosscuttingModal({{ $node->id }})" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">
                    Tambah Crosscutting OPD
                </button>

                {{-- Tombol Hapus --}}
                <button wire:click="delete({{ $node->id }})" wire:confirm="Hapus item ini? Hati-hati, seluruh anak (cabang) di bawahnya juga akan terhapus permanen." class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded text-xs font-medium flex justify-center items-center shadow-sm uppercase tracking-wide transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>