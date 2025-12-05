<div>
    <!-- HEADER DIHAPUS (Judul sudah ditangani Layout Utama) -->

    <div class="space-y-8 relative z-10 mt-6">
        
        <!-- ROW 1: KARTU RINGKASAN -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Kartu 1: Menunggu Verifikasi (PENTING) -->
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-400 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute right-0 top-0 h-full w-24 bg-yellow-50 transform skew-x-12 translate-x-12 group-hover:translate-x-8 transition-transform"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Perjanjian Kinerja</p>
                    <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $pkMenunggu }}</h3>
                    <p class="text-sm text-yellow-600 font-medium mt-2 flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                        Menunggu Verifikasi
                    </p>
                </div>
            </div>

            <!-- Kartu 2: Sudah Disetujui -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Dokumen Final</p>
                    <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $pkDisetujui }}</h3>
                    <p class="text-sm text-green-600 font-medium mt-2">Sudah Disetujui</p>
                </div>
            </div>

            <!-- Kartu 3: Total Pegawai -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pegawai</p>
                    <h3 class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalPegawai }}</h3>
                    <p class="text-sm text-blue-600 font-medium mt-2">Aktif di Dinas</p>
                </div>
            </div>

        </div>

        <!-- ROW 2: SHORTCUT MENU -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Akses Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('perjanjian.kinerja') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all group">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-bold text-gray-800">Verifikasi Perjanjian Kinerja</h4>
                        <p class="text-sm text-gray-500">Lihat dokumen yang diajukan pegawai.</p>
                    </div>
                </a>

                <a href="{{ route('pengukuran.bulanan') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all group">
                    <div class="p-3 bg-green-100 text-green-600 rounded-lg group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="font-bold text-gray-800">Monitoring Pengukuran Bulanan</h4>
                        <p class="text-sm text-gray-500">Pantau capaian kinerja bulanan.</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>