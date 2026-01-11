<div>
    <x-slot:title>
        Pengukuran Bulanan
    </x-slot>

    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-blue-100 transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Pengukuran Bulanan</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="px-4 py-4 md:px-6 md:py-5 border-b border-gray-100 bg-white">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    Pengampu PK — <span class="ml-1 text-gray-800">DINAS KESEHATAN</span>
                </h2>
            </div>

            <div class="p-4 md:p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <div class="flex items-center gap-2 text-sm text-gray-600 w-full sm:w-auto">
                    <span>Tampil</span>
                    <select wire:model.live="perPage" class="border border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500 outline-none cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-sm text-gray-600 hidden sm:inline">Cari:</span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="w-full sm:w-64 border border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500 outline-none bg-gray-50 focus:bg-white transition-colors" placeholder="Cari jabatan...">
                </div>
            </div>

            {{-- TABEL SCROLLABLE --}}
            <div class="overflow-x-auto border-t border-gray-100 min-h-[300px]">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-white text-xs uppercase text-gray-400 font-bold tracking-wider border-b border-gray-100">
                        <tr>
                            {{-- Min-width ditambahkan agar kolom tidak gepeng di mobile --}}
                            <th class="px-6 py-4 w-16 text-center min-w-[60px]">#</th>
                            <th class="px-6 py-4 min-w-[250px]">JABATAN</th>
                            <th class="px-6 py-4 min-w-[250px]">PENANGGUNG JAWAB</th>
                            <th class="px-6 py-4 text-center min-w-[120px]">STATUS</th>
                            <th class="px-6 py-4 text-center w-64 min-w-[280px]">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-600 divide-y divide-gray-50">
                        @forelse ($jabatans as $index => $jabatan)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 text-center font-medium text-gray-500">
                                {{ $jabatans->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-normal">
                                {{ $jabatan->nama ?? 'Nama Jabatan' }}
                            </td>

                            <td class="px-6 py-4">
                                @if($jabatan->pegawai)
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs overflow-hidden border-2 border-white shadow-sm flex-shrink-0">
                                            @if($jabatan->pegawai->foto)
                                                <img src="{{ asset('storage/'.$jabatan->pegawai->foto) }}" class="h-full w-full object-cover">
                                            @else
                                                {{ substr($jabatan->pegawai->nama ?? 'U', 0, 1) }}
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <div class="text-gray-800 text-sm font-medium whitespace-normal">{{ $jabatan->pegawai->nama }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">NIP. {{ $jabatan->pegawai->nip }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">- Belum ada pejabat -</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @php
                                    $status = $jabatan->pegawai->status ?? 'Definitif'; 
                                    $badgeClass = '';
                                    
                                    if ($status === 'Definitif') {
                                        $badgeClass = 'bg-green-100 text-green-600';
                                    } elseif ($status === 'Plt') {
                                        $badgeClass = 'bg-yellow-100 text-yellow-600';
                                    } else {
                                        $badgeClass = 'bg-gray-100 text-gray-600';
                                    }
                                @endphp
                                
                                @if($jabatan->pegawai)
                                    <span class="px-3 py-1 rounded-md text-xs font-bold {{ $badgeClass }}">
                                        {{ $status }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-400">
                                        Kosong
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pengukuran.atur', $jabatan->id) }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-500 rounded-md hover:bg-red-100 hover:text-red-600 transition-colors text-xs font-medium border border-red-50 whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                        Atur Kinerja
                                    </a>

                                    <a href="{{ route('pengukuran.detail', $jabatan->id) }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-500 rounded-md hover:bg-blue-100 hover:text-blue-600 transition-colors text-xs font-medium border border-blue-50 whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        Pengukuran
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tidak ada data jabatan ditemukan.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                {{ $jabatans->links() }}
            </div>
        </div>
    </div>
</div>