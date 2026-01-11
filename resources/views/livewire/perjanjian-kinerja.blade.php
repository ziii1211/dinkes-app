<div>
    <x-slot:title>Perjanjian Kinerja</x-slot>
    
    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Perencanaan Kinerja</span>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">Perjanjian Kinerja</span>
        </div>
    </x-slot>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-start">
            <div class="text-blue-500 mt-1 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-base mb-1">Penjelasan</h4>
                <p class="text-blue-700 text-sm leading-relaxed">
                    Perjanjian Kinerja adalah komitmen tertulis target kinerja yang disepakati, menjadi dasar penilaian kinerja & akuntabilitas.
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Akuntabilitas
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Target Terukur
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700 border border-purple-200">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        IKU
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- BAGIAN KIRI: INFORMASI (Sticky di Desktop, Statis di Mobile) --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden lg:sticky lg:top-24">
                <div class="px-6 py-4 border-b border-gray-100 bg-white">
                    <h3 class="font-bold text-gray-800 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 2H15V7H9V2H5C3.9 2 3 2.9 3 4V20C3 21.1 3.9 22 5 22H19C20.1 22 21 21.1 21 20V4C21 2.9 20.1 2 19 2ZM11 18H7V16H11V18ZM11 14H7V12H11V14ZM11 10H7V8H11V10ZM17 18H13V16H17V18ZM17 14H13V12H17V14ZM17 10H13V8H17V10Z"/>
                        </svg>
                        Informasi Unit Kerja
                    </h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center border-b border-gray-50 pb-3 gap-1">
                        <span class="text-sm text-gray-500 font-medium">Nama SKPD</span>
                        <span class="text-sm text-gray-800 sm:text-right font-semibold">DINAS KESEHATAN</span>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center border-b border-gray-50 pb-3 gap-1">
                        <span class="text-sm text-gray-500 font-medium">Kode SKPD</span>
                        <span class="text-sm text-gray-800 font-mono sm:text-right">1.02.0.00.0.00.01.0000</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 font-medium">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">
                            SKPD
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: DAFTAR JABATAN --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                <div class="px-6 py-5 border-b border-gray-100 bg-white flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-base flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                        </svg>
                        Daftar Jabatan
                    </h3>
                </div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-3">
                        <div class="flex items-center w-full sm:w-auto justify-between sm:justify-start">
                            <span class="text-sm text-gray-500 mr-2">Show</span>
                            <select wire:model.live="perPage" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm py-1">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
                        <div class="flex items-center w-full sm:w-auto">
                            <span class="text-sm text-gray-500 mr-2 hidden sm:inline">Search:</span>
                            <input type="text" wire:model.live="search" class="w-full sm:w-64 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm py-1 bg-gray-50" placeholder="Cari jabatan...">
                        </div>
                    </div>

                    {{-- TABEL DATA RESPONSIF --}}
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 text-xs uppercase tracking-wider">
                                <tr>
                                    {{-- Tambahkan min-w agar tidak gepeng di HP --}}
                                    <th class="p-4 text-center w-16 min-w-[50px]">No</th>
                                    <th class="p-4 min-w-[200px]">Jabatan</th>
                                    <th class="p-4 text-center w-32 min-w-[100px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($jabatans as $index => $jabatan)
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $jabatans->firstItem() + $index }}</td>
                                    
                                    <td class="p-4 whitespace-normal">
                                        <div class="font-semibold text-gray-800">{{ $jabatan->nama }}</div>
                                    </td>

                                    <td class="p-4 text-center">
                                        <a href="{{ route('perjanjian.kinerja.detail', $jabatan->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 border border-blue-200 text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
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