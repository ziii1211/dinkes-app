<div class="animate-fade-in-down">
    <div class="mb-10 text-center sm:text-left">
        <h2 class="text-3xl font-extrabold text-white tracking-tight drop-shadow-md">
            Pusat Laporan Terpadu
        </h2>
        <p class="text-blue-100 mt-3 text-base max-w-2xl drop-shadow-sm">
            Akses, kelola, dan cetak dokumen laporan kinerja Dinas Kesehatan dalam format PDF. Semua data digenerate secara real-time dan otomatis.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8 relative z-10">

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-blue-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent dark:from-blue-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-blue-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Laporan Renstra</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak Matriks Dokumen Rencana Strategis (Tujuan, Sasaran, Program, Kegiatan).</p>
            </div>
            <div class="relative z-10 flex gap-3 mt-auto">
                <a href="{{ route('matrik.dokumen.print') }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition-colors shadow-sm hover:shadow-md hover:shadow-red-500/20 group/btn">
                    <svg class="w-4 h-4 mr-2 transition-transform group-hover/btn:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PDF Renstra
                </a>
            </div>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-indigo-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent dark:from-indigo-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-indigo-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Pohon Kinerja</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak visualisasi struktur cascading kinerja (penjabaran kinerja) dari atasan ke bawahan.</p>
            </div>
            <a href="{{ route('cascading.renstra.print') }}" target="_blank" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md hover:shadow-red-500/20 group/btn">
                <span>Cetak PDF Cascading</span>
            </a>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-teal-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-teal-50/50 to-transparent dark:from-teal-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-400 to-teal-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-teal-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Perjanjian Kinerja</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak lembar pengesahan Perjanjian Kinerja final per Pegawai/Jabatan.</p>
            </div>
            <button wire:click="openPkModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-900/30 hover:bg-teal-500 hover:text-white dark:hover:bg-teal-500 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Dokumen PK</span>
            </button>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-amber-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-transparent dark:from-amber-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-3 shadow-lg shadow-amber-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Rencana Aksi</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak target rencana aksi per triwulan untuk setiap indikator kinerja.</p>
            </div>
            <button wire:click="openAksiModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-500 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Rencana Aksi</span>
            </button>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-cyan-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-50/50 to-transparent dark:from-cyan-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-400 to-cyan-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-3 shadow-lg shadow-cyan-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Kinerja Bulanan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak laporan evaluasi dan pengukuran realisasi kinerja per bulan.</p>
            </div>
            <button wire:click="openBulananModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-cyan-700 dark:text-cyan-300 bg-cyan-50 dark:bg-cyan-900/30 hover:bg-cyan-500 hover:text-white dark:hover:bg-cyan-500 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Laporan Bulanan</span>
            </button>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-purple-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50/50 to-transparent dark:from-purple-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-purple-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Realisasi Tahunan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Rekapitulasi persentase capaian target fisik & anggaran dalam 1 tahun penuh.</p>
            </div>
            <button wire:click="openTahunanModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-500 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Laporan Tahunan</span>
            </button>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-rose-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-50/50 to-transparent dark:from-rose-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-rose-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Laporan E-Monev</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak laporan konsolidasi pengawasan dan evaluasi (E-Monev) keseluruhan.</p>
            </div>
            <button wire:click="openEmonevModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-500 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Parameter E-Monev</span>
            </button>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-slate-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-100/50 to-transparent dark:from-slate-700/30 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 dark:from-slate-600 dark:to-slate-800 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-3 shadow-lg shadow-slate-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Data Pegawai</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak rekapitulasi daftar pegawai beserta hierarki nomenklatur jabatannya.</p>
            </div>
            <button wire:click="openPegawaiModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 hover:bg-slate-800 hover:text-white dark:hover:bg-slate-600 rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Parameter Pegawai</span>
            </button>
        </div>

        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-yellow-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-50/50 to-transparent dark:from-yellow-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-yellow-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-yellow-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Top Performer</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Akses cepat melihat dan mencetak sertifikat data program/kegiatan dengan performa terbaik.</p>
            </div>
            <button wire:click="openTopPerformerModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-500 hover:text-white dark:hover:bg-yellow-500 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Parameter Cetak</span>
            </button>
        </div>

        {{-- KARTU BARU: PENILAIAN DIVISI --}}
        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-emerald-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-transparent dark:from-emerald-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-emerald-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Penilaian Divisi</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak laporan evaluasi target, realisasi, dan kendala/tanggapan kinerja per divisi.</p>
            </div>
            <button wire:click="openPenilaianDivisiModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Parameter Cetak</span>
            </button>
        </div>

        {{-- KARTU BARU: KEPUTUSAN KADIS (DIPINDAHKAN KE DALAM GRID) --}}
        <div class="group relative bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 transition-all duration-500 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:shadow-red-500/10 hover:-translate-y-2 border border-slate-200/60 dark:border-slate-700/60 overflow-hidden flex flex-col">
            <div class="absolute inset-0 bg-gradient-to-br from-red-50/50 to-transparent dark:from-red-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="relative z-10 flex-grow">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center mb-6 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-red-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Keputusan Kadis</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">Cetak ringkasan indikator di bawah target dari seluruh divisi beserta rekomendasi keputusan Kepala Dinas.</p>
            </div>
            <button wire:click="openKeputusanKadisModal" class="relative z-10 mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white rounded-xl transition-all duration-300 group/btn">
                <span>Pilih Parameter Cetak</span>
            </button>
        </div>

    </div>
    {{-- MODAL PK, RENCANA AKSI, BULANAN, TAHUNAN, EMONEV DISINI --}}
    
    @if($showPkModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-teal-100 dark:bg-teal-900/30 text-teal-600 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Pilih Dokumen Perjanjian Kinerja</h3>
                </div>
                <button wire:click="closePkModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-800/50">
                @if(count($pkList) > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($pkList as $pk)
                            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 shrink-0 rounded-full bg-blue-50 dark:bg-slate-700 flex items-center justify-center font-bold text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-slate-600">{{ $pk->tahun }}</div>
                                    <div>
                                        <div class="font-bold text-gray-800 dark:text-slate-200 text-lg">{{ $pk->jabatan->nama ?? 'Tidak Diketahui' }}</div>
                                        <div class="text-sm text-gray-600 dark:text-slate-400 flex items-center gap-1 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Penanggung Jawab: <span class="font-semibold">{{ $pk->pegawai->nama ?? 'Belum Diisi' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('perjanjian.kinerja.print', $pk->id) }}" target="_blank" class="shrink-0 w-full sm:w-auto px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 group">
                                    <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak PDF
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12"><div class="text-gray-500">Belum Ada Dokumen.</div></div>
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end">
                <button wire:click="closePkModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Tutup Jendela</button>
            </div>
        </div>
    </div>
    @endif

    @if($showAksiModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Pilih Dokumen Rencana Aksi</h3>
                </div>
                <button wire:click="closeAksiModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-800/50">
                @if(count($pkList) > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($pkList as $pk)
                            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 shrink-0 rounded-full bg-blue-50 dark:bg-slate-700 flex items-center justify-center font-bold text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-slate-600">{{ $pk->tahun }}</div>
                                    <div>
                                        <div class="font-bold text-gray-800 dark:text-slate-200 text-lg">{{ $pk->jabatan->nama ?? 'Tidak Diketahui' }}</div>
                                        <div class="text-sm text-gray-600 dark:text-slate-400 flex items-center gap-1 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Penanggung Jawab: <span class="font-semibold">{{ $pk->pegawai->nama ?? 'Belum Diisi' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('rencana.aksi.print', $pk->id) }}" target="_blank" class="shrink-0 w-full sm:w-auto px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 group">
                                    <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak PDF
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12"><div class="text-gray-500">Belum Ada Dokumen.</div></div>
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end">
                <button wire:click="closeAksiModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Tutup Jendela</button>
            </div>
        </div>
    </div>
    @endif

    @if($showBulananModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white dark:bg-slate-800 shrink-0 gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Pilih Laporan Bulanan</h3>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Bulan:</span>
                    <select wire:model.live="bulanTerpilih" class="border border-gray-300 dark:border-slate-600 rounded-lg bg-gray-50 dark:bg-slate-700 text-gray-800 dark:text-white px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none w-full sm:w-auto">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                    <button wire:click="closeBulananModal" class="ml-2 text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-800/50">
                @if(count($pkList) > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($pkList as $pk)
                            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 shrink-0 rounded-full bg-blue-50 dark:bg-slate-700 flex items-center justify-center font-bold text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-slate-600">{{ $pk->tahun }}</div>
                                    <div>
                                        <div class="font-bold text-gray-800 dark:text-slate-200 text-lg">{{ $pk->jabatan->nama ?? 'Tidak Diketahui' }}</div>
                                        <div class="text-sm text-gray-600 dark:text-slate-400 flex items-center gap-1 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Penanggung Jawab: <span class="font-semibold">{{ $pk->pegawai->nama ?? 'Belum Diisi' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('kinerja.bulanan.print', ['id' => $pk->id, 'bulan' => $bulanTerpilih]) }}" target="_blank" class="shrink-0 w-full sm:w-auto px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 group">
                                    <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak PDF
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12"><div class="text-gray-500">Belum Ada Dokumen.</div></div>
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end">
                <button wire:click="closeBulananModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Tutup Jendela</button>
            </div>
        </div>
    </div>
    @endif

    @if($showTahunanModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Pilih Laporan Realisasi Tahunan</h3>
                </div>
                <button wire:click="closeTahunanModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-800/50">
                @if(count($pkList) > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($pkList as $pk)
                            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 shrink-0 rounded-full bg-blue-50 dark:bg-slate-700 flex items-center justify-center font-bold text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-slate-600">{{ $pk->tahun }}</div>
                                    <div>
                                        <div class="font-bold text-gray-800 dark:text-slate-200 text-lg">{{ $pk->jabatan->nama ?? 'Tidak Diketahui' }}</div>
                                        <div class="text-sm text-gray-600 dark:text-slate-400 flex items-center gap-1 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Penanggung Jawab: <span class="font-semibold">{{ $pk->pegawai->nama ?? 'Belum Diisi' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('kinerja.tahunan.print', $pk->id) }}" target="_blank" class="shrink-0 w-full sm:w-auto px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 group">
                                    <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak PDF
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12"><div class="text-gray-500">Belum Ada Dokumen.</div></div>
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end">
                <button wire:click="closeTahunanModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Tutup Jendela</button>
            </div>
        </div>
    </div>
    @endif

    @if($showEmonevModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Pilih Parameter E-Monev</h3>
                </div>
                <button wire:click="closeEmonevModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tahun Laporan</label>
                    <select wire:model.live="emonevTahun" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-rose-500 outline-none">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Pilih Jabatan (Untuk Full SKPD, Pilih Kepala Dinas)</label>
                    <select wire:model.live="emonevJabatan" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-rose-500 outline-none">
                        <option value="">-- Silakan Pilih Jabatan --</option>
                        @foreach($listJabatan as $jab)
                            <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end gap-3">
                <button wire:click="closeEmonevModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Batal</button>
                @if($emonevJabatan != '')
                    <a href="{{ route('pusat.laporan.emonev.redirect') }}?tahun={{ $emonevTahun }}&jabatan_id={{ $emonevJabatan }}" target="_blank" class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak PDF
                    </a>
                @else
                    <button disabled class="px-6 py-2.5 bg-gray-300 dark:bg-slate-600 text-gray-500 dark:text-gray-400 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak PDF
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL POP-UP PILIH DATA PEGAWAI --}}
    @if($showPegawaiModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-slate-100 dark:bg-slate-900/30 text-slate-700 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Pilih Data Pegawai</h3>
                </div>
                <button wire:click="closePegawaiModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="mb-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Filter Berdasarkan Jabatan</label>
                    <select wire:model.live="pegawaiJabatan" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-slate-500 outline-none">
                        <option value="">Semua Pegawai (Full SKPD)</option>
                        @foreach($listJabatan as $jab)
                            <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end gap-3">
                <button wire:click="closePegawaiModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Batal</button>
                <a href="{{ route('laporan.pegawai.print') }}?jabatan_id={{ $pegawaiJabatan }}" target="_blank" class="px-6 py-2.5 bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PDF
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL TOP PERFORMER (DIPERBARUI) --}}
    @if($showTopPerformerModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Parameter Cetak Top Performer</h3>
                </div>
                <button wire:click="closeTopPerformerModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Pilih Tahun</label>
                    <select wire:model.live="topTahun" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-yellow-500 outline-none">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Filter Evaluasi Pada Jabatan</label>
                    <select wire:model.live="topJabatan" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-yellow-500 outline-none">
                        <option value="">-- Semua Jabatan --</option>
                        @foreach($listJabatan as $jab)
                            <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- PREVIEW TOP PERFORMER (Alasan dan Jabatan) --}}
                <div class="mt-4 p-4 rounded-xl border {{ $topPerformerName ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' }}">
                    @if($topPerformerName)
                        <h4 class="text-lg font-bold text-green-800 dark:text-green-400 mb-2">🎉 Pemenang: {{ $topPerformerName }}</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            <strong>Capaian Kinerja:</strong> {{ $topPerformerScore }}% <br><br>
                            <strong>Alasan:</strong> {{ $alasanTopPerformer }}
                        </p>
                    @else
                        <h4 class="text-lg font-bold text-red-800 dark:text-red-400 mb-2">Belum Ada Pemenang (Top Performer)</h4>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            Saat ini belum ada kegiatan pada filter ini yang mencatatkan realisasi di atas 0%. Silakan update capaian kinerja terlebih dahulu.
                        </p>
                    @endif
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end gap-3">
                <button wire:click="closeTopPerformerModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Batal</button>
                @if($topPerformerName)
                    <a href="{{ route('top.performer.print') }}?tahun={{ $topTahun }}&jabatan_id={{ $topJabatan }}&alasan={{ urlencode($alasanTopPerformer) }}" target="_blank" class="px-6 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Laporan
                    </a>
                @else
                    <button disabled class="px-6 py-2.5 bg-gray-300 dark:bg-slate-600 text-gray-500 dark:text-gray-400 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Laporan
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL BARU: PENILAIAN DIVISI --}}
    @if($showPenilaianDivisiModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Parameter Penilaian Divisi</h3>
                </div>
                <button wire:click="closePenilaianDivisiModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 bg-slate-50/50 dark:bg-slate-800/50">
                @if (session()->has('message'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-200 text-green-700 rounded-xl">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tahun Laporan</label>
                    <select wire:model.live="penilaianTahun" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                    </select>
                    @error('penilaianTahun') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Pilih Jabatan/Divisi</label>
                    <select wire:model.live="penilaianJabatan" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">-- Silakan Pilih Divisi/Jabatan --</option>
                        @foreach($listJabatan as $jab)
                            <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </select>
                    @error('penilaianJabatan') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end gap-3">
                <button wire:click="closePenilaianDivisiModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Batal</button>
                <button wire:click="generatePenilaianDivisi" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan 
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL BARU: KEPUTUSAN KADIS --}}
    @if($showKeputusanKadisModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-slate-100">Parameter Laporan Keputusan</h3>
                </div>
                <button wire:click="closeKeputusanKadisModal" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="mb-5 flex gap-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Pilih Bulan</label>
                        <select wire:model.live="keputusanKadisBulan" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Pilih Tahun</label>
                        <select wire:model.live="keputusanKadisTahun" class="w-full border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-800 dark:text-white px-4 py-3 focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                        </select>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Sistem akan secara otomatis memfilter seluruh indikator dari semua divisi yang berada di bawah target pada bulan dan tahun yang dipilih, lalu men-*generate* draf keputusan pimpinan.</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 flex justify-end gap-3">
                <button wire:click="closeKeputusanKadisModal" class="px-6 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Batal</button>
                <button wire:click="generateKeputusanKadis" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>