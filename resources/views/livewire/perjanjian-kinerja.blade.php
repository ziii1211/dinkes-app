<div>
    <x-slot:title>Perjanjian Kinerja</x-slot>
    
    <x-slot:breadcrumb>
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Perencanaan Kinerja</span>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Perangkat Daerah</span>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">Perjanjian Kinerja</span>
    </x-slot>

    <!-- INFO BOX (BIRU - Penjelasan) -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 shadow-sm mb-8">
        <div class="flex gap-4 items-start">
            <div class="text-blue-500 mt-1 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-base mb-1">Penjelasan</h4>
                <p class="text-blue-700 text-sm leading-relaxed">
                    Perjanjian Kinerja adalah komitmen tertulis target kinerja yang disepakati, menjadi dasar penilaian kinerja & akuntabilitas. Silakan pilih jabatan di bawah ini untuk memulai input data.
                </p>
                <div class="flex gap-3 mt-3">
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Akuntabilitas
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Target Terukur
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        IKU
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI: INFO UNIT KERJA -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-100 bg-white">
                    <h3 class="font-bold text-gray-800 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Informasi Unit Kerja
                    </h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-sm text-gray-500">Nama SKPD</span>
                        <span class="text-sm font-bold text-gray-800 text-right">DINAS KESEHATAN</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-sm text-gray-500">Kode SKPD</span>
                        <span class="text-sm font-bold text-gray-800 font-mono text-right">1.02.0.00.0.00.01.0000</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">
                            SKPD
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: TABEL DAFTAR JABATAN -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <!-- Header Tabel: Daftar Jabatan -->
                <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
                    <h3 class="font-bold text-gray-800 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m100v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 30 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 019.288 0M15 7a3 30 11-6 0 3 3 0 016 0zm6 3a2 2011-40220014 0zM7 10a2 2011-40220014 0z"></path></svg>
                        Daftar Jabatan
                    </h3>
                    
                    <!-- Tombol Buat PK TELAH DIHAPUS -->
                </div>

                <div class="p-6 bg-white">
                    <!-- Filters: Show & Search -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-3">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 mr-2">Show</span>
                            <select wire:model.live="perPage" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
                        <div class="flex items-center w-full sm:w-auto">
                            <span class="text-sm text-gray-500 mr-2">Search:</span>
                            <input type="text" wire:model.live="search" class="w-full sm:w-64 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm" placeholder="Cari jabatan...">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="p-4 text-center w-12">No</th>
                                    <th class="p-4">Jabatan</th>
                                    <th class="p-4 text-center w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($jabatans as $index => $jabatan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500">{{ $jabatans->firstItem() + $index }}</td>
                                    
                                    <td class="p-4">
                                        <div class="font-medium text-gray-800">{{ $jabatan->nama }}</div>
                                    </td>

                                    <td class="p-4 text-center">
                                        <!-- Tombol Detail -->
                                        <!-- Ini link menuju halaman input PK untuk jabatan tersebut -->
                                        <a href="{{ route('perjanjian.kinerja.detail', $jabatan->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-200 text-blue-600 rounded-md hover:bg-blue-50 text-xs font-medium transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-gray-400 bg-gray-50 italic">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            <p>Data jabatan belum tersedia.</p>
                                            <p class="text-xs mt-1">Silakan input data di menu <strong>Master Data > Struktur Organisasi</strong>.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $jabatans->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>