<div>
    <x-slot:title>Detail Perjanjian Kinerja</x-slot>
    
    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-white transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-blue-200">Perencanaan Kinerja</span>
            <span class="mx-2">/</span>
            <a href="{{ route('perjanjian.kinerja') }}" class="hover:text-white transition-colors">Perjanjian Kinerja</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-white">DINAS KESEHATAN - {{ $jabatan->nama }}</span>
        </div>
    </x-slot>

    <div class="space-y-6 md:space-y-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-4 md:px-6 md:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white gap-4">
                <h3 class="font-medium text-gray-800 text-base flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m100v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 30 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 019.288 0M15 7a3 30 11-6 0 3 3 0 016 0zm6 3a2 2011-40220014 0zM7 10a2 2011-40220014 0z"></path></svg>
                    Informasi Jabatan & Pengampu
                </h3>
                
                <a href="{{ route('perjanjian.kinerja') }}" class="w-full sm:w-auto justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>
            
            {{-- PADDING RESPONSIF --}}
            <div class="p-4 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12">
                    {{-- INFO JABATAN --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-12 gap-2 md:gap-4 pb-4 border-b border-dashed border-gray-200 items-center">
                            <div class="col-span-12 sm:col-span-4 text-xs md:text-sm text-gray-500">Unit Kerja</div>
                            <div class="col-span-12 sm:col-span-8 text-sm font-medium text-gray-800 uppercase">DINAS KESEHATAN</div>
                        </div>
                        <div class="grid grid-cols-12 gap-2 md:gap-4 pb-4 border-b border-dashed border-gray-200 items-center">
                            <div class="col-span-12 sm:col-span-4 text-xs md:text-sm text-gray-500">Kode SKPD</div>
                            <div class="col-span-12 sm:col-span-8 text-sm font-medium text-gray-800 break-all">1.02.0.00.0.00.01.0000</div>
                        </div>
                        <div class="grid grid-cols-12 gap-2 md:gap-4 pb-4 border-b border-dashed border-gray-200 items-center">
                            <div class="col-span-12 sm:col-span-4 text-xs md:text-sm text-gray-500">Jabatan</div>
                            <div class="col-span-12 sm:col-span-8 text-sm font-medium text-gray-800">{{ $jabatan->nama }}</div>
                        </div>
                        <div class="grid grid-cols-12 gap-2 md:gap-4 pt-2 items-center">
                            <div class="col-span-12 sm:col-span-4 text-xs md:text-sm text-gray-500 self-start pt-0 sm:pt-2">Pengampu Sekarang</div>
                            <div class="col-span-12 sm:col-span-8">
                                <div class="flex items-center gap-4 mt-2 sm:mt-0">
                                    <div class="h-12 w-12 md:h-14 md:w-14 rounded-full bg-blue-100 border-2 border-white shadow-md overflow-hidden flex-shrink-0">
                                        @if($pegawai && $pegawai->foto)
                                            <img src="{{ asset('storage/'.$pegawai->foto) }}" class="h-full w-full object-cover">
                                        @else
                                            <img src="{{ asset('user-icon.png') }}" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        @if($pegawai)
                                            <p class="text-sm font-bold text-gray-800 uppercase truncate">{{ $pegawai->nama }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <p class="text-xs text-gray-500">NIP: {{ $pegawai->nip }}</p>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-500 text-white uppercase tracking-wide shadow-sm">
                                                    {{ $pegawai->status }}
                                                </span>
                                            </div>
                                        @else
                                            <p class="text-sm text-red-500 italic">Belum ada pejabat definitif.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STATISTIK KECIL --}}
                    <div class="bg-gray-50 rounded-xl p-4 md:p-6 border border-gray-100 flex flex-col justify-between">
                        <div class="space-y-3 mb-6">
                            <div class="bg-white p-4 rounded-lg border border-blue-100 shadow-sm flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                                    <div><p class="text-xs text-gray-500 font-medium">Total PK</p><p class="text-lg font-bold text-gray-800">{{ $totalPk }}</p></div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-yellow-100 shadow-sm flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></div>
                                    <div><p class="text-xs text-gray-500 font-medium">Draft</p><p class="text-lg font-bold text-gray-800">{{ $draftPk }}</p></div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-green-100 shadow-sm flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-green-50 text-green-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                    <div><p class="text-xs text-gray-500 font-medium">Final</p><p class="text-lg font-bold text-gray-800">{{ $finalPk }}</p></div>
                                </div>
                            </div>
                        </div>

                        {{-- HANYA ADMIN YANG BISA BUAT PK --}}
                        @if(auth()->user()->hasRole('admin'))
                        <button wire:click="openModal" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition-transform transform active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Buat PK
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL DAFTAR PK --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Daftar Perjanjian Kinerja
                </h3>
            </div>

            <div class="p-4 md:p-6">
                {{-- FORM FILTER & SEARCH (STACK DI MOBILE) --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div class="flex items-center text-sm text-gray-600 w-full sm:w-auto">
                        <span class="mr-2">Show</span>
                        <select class="border border-gray-300 rounded px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white w-full sm:w-auto">
                            <option>10</option><option>25</option>
                        </select>
                    </div>
                    <div class="flex items-center w-full sm:w-auto">
                        <span class="mr-2 text-sm text-gray-600 hidden sm:inline">Search:</span>
                        <input type="text" wire:model.live="search" class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64 bg-gray-50" placeholder="Cari keterangan...">
                    </div>
                </div>

                {{-- TABEL SCROLLABLE --}}
                <div class="overflow-x-auto rounded-lg border border-gray-100 min-h-[300px]">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-white text-gray-500 border-b border-gray-100 uppercase tracking-wider text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-center w-16 min-w-[50px]">#</th>
                                <th class="px-6 py-4 w-24 min-w-[80px]">Tahun</th>
                                <th class="px-6 py-4 min-w-[250px]">Keterangan</th>
                                <th class="px-6 py-4 min-w-[200px]">Pengampu</th>
                                <th class="px-6 py-4 text-center min-w-[100px]">Status</th>
                                <th class="px-6 py-4 text-center w-48 min-w-[200px]">Menu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-600">
                            @forelse($pks as $index => $pk)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center">{{ $pks->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-normal text-gray-800">{{ $pk->tahun }}</td>
                                <td class="px-6 py-4 font-normal text-gray-800 whitespace-normal">{{ $pk->keterangan }}</td>
                                <td class="px-6 py-4 whitespace-normal">
                                    @if($pk->pegawai)
                                        <div class="text-sm font-normal text-gray-800">{{ $pk->pegawai->nama }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">NIP: {{ $pk->pegawai->nip }}</div>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($pk->status_verifikasi == 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-600 uppercase">
                                            FINAL
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-600 uppercase">
                                            DRAFT
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('perjanjian.kinerja.lihat', $pk->id) }}" class="flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-xs font-medium transition-colors shadow-sm border border-gray-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat
                                        </a>
                                        <a href="{{ route('perjanjian.kinerja.print', $pk->id) }}" target="_blank" class="flex items-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 rounded text-xs font-medium transition-colors shadow-sm border border-green-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            Cetak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic bg-gray-50">
                                    Belum ada Perjanjian Kinerja untuk jabatan ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $pks->links() }}</div>
            </div>
        </div>
    </div>

    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Buat Perjanjian Kinerja</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 space-y-6 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                        <select wire:model="tahun" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            @for($y = date('Y')-1; $y <= date('Y')+2; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="keterangan" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: PK Sekretaris Tahun 2025">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">Pihak 1 (Penandatangan)</h4>
                                <p class="text-xs text-gray-500">Pengampu Jabatan Ini</p>
                            </div>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">Definitif</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-white border border-gray-200 overflow-hidden flex-shrink-0">
                                @if($pegawai && $pegawai->foto)
                                    <img src="{{ asset('storage/'.$pegawai->foto) }}" class="h-full w-full object-cover">
                                @else
                                    <img src="{{ asset('user-icon.png') }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $pegawai->nama ?? 'Kosong' }}</p>
                                <p class="text-xs text-gray-500">NIP: {{ $pegawai->nip ?? '-' }}</p>
                                <p class="text-xs text-gray-500 truncate">Jabatan: {{ $jabatan->nama }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">Pihak 2 (Atasan Langsung)</h4>
                                <p class="text-xs text-gray-500">Otomatis dari jabatan atasan</p>
                            </div>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">Definitif</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-white border border-gray-200 overflow-hidden flex-shrink-0">
                                @if($is_kepala_dinas)
                                    <img src="{{ asset('storage/'.$gubernur_foto) }}" class="h-full w-full object-cover">
                                @elseif($atasan_pegawai && $atasan_pegawai->foto)
                                    <img src="{{ asset('storage/'.$atasan_pegawai->foto) }}" class="h-full w-full object-cover">
                                @else
                                    <img src="{{ asset('user-icon.png') }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                @if($is_kepala_dinas)
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $gubernur_nama }}</p>
                                    <p class="text-xs text-gray-500">NIP: -</p>
                                    <p class="text-xs text-gray-500 truncate">Jabatan: {{ $gubernur_jabatan }}</p>
                                @elseif($atasan_pegawai)
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $atasan_pegawai->nama }}</p>
                                    <p class="text-xs text-gray-500">NIP: {{ $atasan_pegawai->nip }}</p>
                                    <p class="text-xs text-gray-500 truncate">Jabatan: {{ $atasan_jabatan->nama ?? 'Atasan' }}</p>
                                @else
                                    <p class="text-sm font-bold text-gray-400 italic">Tidak ada atasan langsung</p>
                                    <p class="text-xs text-gray-400">(Data belum diinput)</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                <button wire:click="closeModal" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none">Batal</button>
                <button wire:click="store" class="px-6 py-2.5 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 focus:outline-none flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>