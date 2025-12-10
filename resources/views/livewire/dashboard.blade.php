<div class="min-h-screen bg-gray-100 p-6 font-sans text-gray-600">
    
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mb-8">
        
        <div class="xl:col-span-4 bg-white rounded-xl shadow-sm border border-gray-200 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-yellow-400 via-red-500 to-green-500"></div>

            <div class="mb-6 transform hover:scale-105 transition-transform duration-500">
                <img src="{{ asset('logo-prov-kalsel.png') }}" alt="Logo Kalsel" class="h-36 w-auto object-contain drop-shadow-md" onerror="this.src='https://via.placeholder.com/150x150.png?text=LOGO+KALSEL'; this.classList.add('rounded-full');">
            </div>
            
            <h2 class="text-xl font-black text-gray-800 tracking-[0.3em] uppercase mb-3">
                S E L A M A T  D A T A N G
            </h2>
            
            <div class="space-y-1">
                <p class="text-gray-600 font-medium text-base">Sistem Informasi Kinerja Terintegrasi</p>
                <p class="text-gray-500 font-medium text-sm">Pemerintah Provinsi Kalimantan Selatan</p>
            </div>
        </div>

        <div class="xl:col-span-8 flex flex-col gap-6">
            
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <div class="h-12 w-12 rounded-full bg-gray-50 flex items-center justify-center flex-shrink-0 relative">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div class="absolute bg-blue-600 rounded-full border-2 border-white h-3 w-3 bottom-0 right-0"></div>
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg font-bold text-gray-900">Selamat datang, DINAS KESEHATAN!</h3>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-green-500 text-white">Admin Unker</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-400 mt-1 font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            SKPD: DINAS KESEHATAN
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6 w-full md:w-auto justify-end border-t md:border-t-0 pt-4 md:pt-0 border-gray-100">
                    <a href="#" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-medium text-sm transition-colors group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Dashboard SKPD
                    </a>
                    <a href="#" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-medium text-sm transition-colors group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                        Profil SKPD
                    </a>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Periode</label>
                        <div class="relative">
                            <select class="w-full border border-gray-300 rounded-lg text-sm py-2.5 pl-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white appearance-none cursor-pointer font-medium text-gray-700">
                                <option>RPJMD 2021-2026</option>
                                <option>RENSTRA 2025-2029</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Perangkat Daerah</label>
                        <div class="relative">
                            <select class="w-full border border-gray-300 rounded-lg text-sm py-2.5 pl-3 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white appearance-none cursor-pointer font-medium text-gray-700">
                                <option>Semua Perangkat Daerah</option>
                                <option selected>Dinas Kesehatan</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center justify-center gap-2 w-full md:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Terapkan
                        </button>
                        <button class="px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-600 hover:bg-gray-100 hover:text-gray-800 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-1 w-full md:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Indikator Kinerja Utama</h3>
                            <p class="text-sm text-gray-500">Progress capaian indikator kinerja utama perangkat daerah</p>
                        </div>
                    </div>
                    <div class="relative">
                        <select class="appearance-none bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-medium">
                            <option>Tahun 2025</option>
                            <option>Tahun 2026</option>
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 hover:border-blue-400 transition-colors group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm group-hover:border-blue-200 transition-colors">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">Tercapai</span>
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2">Indeks Pembangunan Manusia (IPM)</h4>
                        <p class="text-xs text-gray-500 mb-4">Satuan: Poin</p>
                        
                        <div class="flex items-end justify-between mb-2">
                            <div>
                                <span class="text-sm text-gray-500">Realisasi</span>
                                <div class="text-2xl font-bold text-gray-900 leading-none">74.56</div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Target</span>
                                <div class="text-sm font-bold text-gray-700">74.50</div>
                            </div>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100.08%"></div>
                        </div>
                        <div class="flex justify-between text-xs font-medium">
                            <span class="text-blue-600">100.08%</span>
                            <span class="text-gray-400">Capaian</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 hover:border-yellow-400 transition-colors group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm group-hover:border-yellow-200 transition-colors">
                                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded-full">Perlu Perhatian</span>
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2">Tingkat Pengangguran Terbuka (TPT)</h4>
                        <p class="text-xs text-gray-500 mb-4">Satuan: Persen (%)</p>
                        
                        <div class="flex items-end justify-between mb-2">
                            <div>
                                <span class="text-sm text-gray-500">Realisasi</span>
                                <div class="text-2xl font-bold text-gray-900 leading-none">4.15</div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Target</span>
                                <div class="text-sm font-bold text-gray-700">4.00</div>
                            </div>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                            <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 96.39%"></div>
                        </div>
                        <div class="flex justify-between text-xs font-medium">
                            <span class="text-yellow-600">96.39%</span>
                            <span class="text-gray-400">Capaian (Invers)</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 hover:border-green-400 transition-colors group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm group-hover:border-green-200 transition-colors">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">Sangat Baik</span>
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2">Pertumbuhan Ekonomi Non Tambang</h4>
                        <p class="text-xs text-gray-500 mb-4">Satuan: Persen (%)</p>
                        
                        <div class="flex items-end justify-between mb-2">
                            <div>
                                <span class="text-sm text-gray-500">Realisasi</span>
                                <div class="text-2xl font-bold text-gray-900 leading-none">5.12</div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Target</span>
                                <div class="text-sm font-bold text-gray-700">4.50</div>
                            </div>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: 100%"></div> </div>
                        <div class="flex justify-between text-xs font-medium">
                            <span class="text-green-600">113.78%</span>
                            <span class="text-gray-400">Capaian</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 hover:border-red-400 transition-colors group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm group-hover:border-red-200 transition-colors">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                            </div>
                            <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full">Belum Tercapai</span>
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2">Indeks Gini (Ketimpangan Pendapatan)</h4>
                        <p class="text-xs text-gray-500 mb-4">Satuan: Koefisien</p>
                        
                        <div class="flex items-end justify-between mb-2">
                            <div>
                                <span class="text-sm text-gray-500">Realisasi</span>
                                <div class="text-2xl font-bold text-gray-900 leading-none">0.315</div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Target</span>
                                <div class="text-sm font-bold text-gray-700">0.310</div>
                            </div>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                            <div class="bg-red-500 h-2.5 rounded-full" style="width: 98.41%"></div>
                        </div>
                        <div class="flex justify-between text-xs font-medium">
                            <span class="text-red-600">98.41%</span>
                            <span class="text-gray-400">Capaian (Invers)</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b border-gray-100 pb-4">
            <div class="flex gap-4 w-full md:w-auto mb-4 md:mb-0">
                <div class="relative">
                    <select class="appearance-none bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-medium w-32">
                        <option>2025</option>
                        <option>2026</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                
                <div class="relative">
                    <select class="appearance-none bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-medium w-64">
                        <option>DINAS KESEHATAN</option>
                        <option>DINAS PENDIDIKAN</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
            
            <div class="text-sm font-medium text-gray-500">
                Capaian Indikator Kinerja Utama
            </div>
        </div>

        <div class="w-full">
            <h3 class="text-center text-gray-500 font-bold uppercase mb-2 text-sm">DINAS KESEHATAN</h3>
            
            <div class="flex flex-wrap justify-center gap-4 mb-6 text-[10px] text-gray-500">
                <div class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-400 inline-block"></span> Angka Kematian Ibu (AKI)</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 bg-pink-300 inline-block"></span> Angka Kematian Bayi (AKB)</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 bg-orange-300 inline-block"></span> Prevalensi Stunting</div>
                <div class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-200 inline-block"></span> Angka Kesakitan</div>
            </div>

            <div class="relative h-64 flex items-end gap-1 sm:gap-2 px-4 border-l border-b border-gray-200 mx-4">
                
                <div class="absolute -left-8 bottom-0 flex flex-col justify-between h-full text-[10px] text-gray-400 py-1">
                    <span>100</span><span>80</span><span>60</span><span>40</span><span>20</span><span>0</span>
                </div>

                <div class="absolute inset-0 flex flex-col justify-between h-full w-full pointer-events-none">
                    <div class="border-t border-gray-100 w-full h-0"></div>
                    <div class="border-t border-gray-100 w-full h-0"></div>
                    <div class="border-t border-gray-100 w-full h-0"></div>
                    <div class="border-t border-gray-100 w-full h-0"></div>
                    <div class="border-t border-gray-100 w-full h-0"></div>
                    <div class="border-t border-gray-100 w-full h-0"></div>
                </div>

                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'July', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                    <div class="flex-1 flex items-end justify-center gap-[1px] group h-full relative">
                        <div class="w-1.5 bg-blue-400 h-[40%] rounded-t-sm opacity-80 hover:opacity-100 transition-all"></div>
                        <div class="w-1.5 bg-pink-300 h-[60%] rounded-t-sm opacity-80 hover:opacity-100 transition-all"></div>
                        <div class="w-1.5 bg-orange-300 h-[30%] rounded-t-sm opacity-80 hover:opacity-100 transition-all"></div>
                        <div class="w-1.5 bg-yellow-200 h-[80%] rounded-t-sm opacity-80 hover:opacity-100 transition-all"></div>
                        
                        <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 -rotate-45 text-[9px] text-gray-500 origin-top-left w-12 text-right">
                            {{ $bulan }}
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="h-12"></div> </div>

    </div>
</div>