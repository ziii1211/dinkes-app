<div>
    <!-- HEADER DIHAPUS (Karena Judul sudah ada di Layout Utama) -->

    <!-- MAIN CONTENT CONTAINER (PUTIH ROUNDED BESAR) -->
    <div class="bg-white rounded-3xl p-8 shadow-xl relative z-10 min-h-[500px]">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- KOLOM KIRI: SELAMAT DATANG & LOGO (Lebar 4/12) -->
            <div class="lg:col-span-4 flex flex-col justify-center items-center text-center p-6 border-r border-gray-100">
                
                <!-- LOGO PROVINSI (UPDATED) -->
                <img src="{{ asset('logo-prov-kalsel.png') }}" 
                     alt="Logo Provinsi Kalimantan Selatan" 
                     class="w-32 h-auto mb-6 drop-shadow-md hover:scale-105 transition-transform duration-300">
                
                <h3 class="text-2xl font-extrabold text-gray-800 tracking-widest uppercase mb-1">
                    S E L A M A T <br> D A T A N G
                </h3>
                
                <div class="h-1 w-20 bg-blue-500 my-4 rounded-full"></div>

                <p class="text-gray-600 font-medium text-lg leading-relaxed">
                    Sistem Informasi Kinerja Terintegrasi<br>
                    <span class="font-bold text-blue-700">Pemerintah Provinsi Kalimantan Selatan</span>
                </p>
            </div>

            <!-- KOLOM KANAN: KARTU INDIKATOR (Lebar 8/12) -->
            <div class="lg:col-span-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- KARTU 1: INDIKATOR TUJUAN -->
                    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="flex flex-col relative z-10">
                            <div class="flex items-center text-gray-500 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">INDIKATOR TUJUAN</span>
                            </div>
                            <div class="text-5xl font-bold text-gray-800 my-2">{{ $ind_tujuan }}</div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>
                                    +{{ $ind_tujuan }}
                                </span>
                            </div>
                        </div>
                        <!-- Dekorasi Background Tipis -->
                        <div class="absolute right-0 top-0 h-full w-2 bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <!-- KARTU 2: INDIKATOR SASARAN -->
                    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="flex flex-col relative z-10">
                            <div class="flex items-center text-gray-500 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">INDIKATOR SASARAN</span>
                            </div>
                            <div class="text-5xl font-bold text-gray-800 my-2">{{ $ind_sasaran }}</div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>
                                    +{{ $ind_sasaran }}
                                </span>
                            </div>
                        </div>
                         <div class="absolute right-0 top-0 h-full w-2 bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <!-- KARTU 3: INDIKATOR PROGRAM -->
                    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="flex flex-col relative z-10">
                            <div class="flex items-center text-gray-500 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">INDIKATOR PROGRAM</span>
                            </div>
                            <div class="text-5xl font-bold text-gray-800 my-2">{{ $ind_program }}</div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>
                                    Data Renstra
                                </span>
                            </div>
                        </div>
                        <div class="absolute right-0 top-0 h-full w-2 bg-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                    <!-- KARTU 4: INDIKATOR KEGIATAN -->
                    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="flex flex-col relative z-10">
                            <div class="flex items-center text-gray-500 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">INDIKATOR KEGIATAN</span>
                            </div>
                            <div class="text-5xl font-bold text-gray-800 my-2">{{ $ind_kegiatan }}</div>
                             <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>
                                    Data Renstra
                                </span>
                            </div>
                        </div>
                        <div class="absolute right-0 top-0 h-full w-2 bg-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>

                     <!-- KARTU 5: INDIKATOR SUB KEGIATAN (FULL WIDTH) -->
                     <div class="md:col-span-2 bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="flex flex-col relative z-10">
                            <div class="flex items-center text-gray-500 mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">INDIKATOR SUB KEGIATAN</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <div class="text-5xl font-bold text-gray-800 my-2">{{ $ind_sub_kegiatan }}</div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mb-2">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>
                                    Total Data
                                </span>
                            </div>
                        </div>
                        <div class="absolute right-0 top-0 h-full w-2 bg-pink-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>