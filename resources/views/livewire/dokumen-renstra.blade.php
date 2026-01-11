{{-- SLOT TITLE & BREADCRUMB --}}
<x-slot:title>Dokumen Renstra</x-slot>

<x-slot:breadcrumb>
    {{-- Wrapper scroll agar breadcrumb tidak merusak layout di layar sangat kecil --}}
    <div class="flex items-center text-sm whitespace-nowrap overflow-x-auto hide-scroll pb-1">
        <a href="/" class="hover:text-white transition-colors">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-blue-200">Matrik Renstra</span>
        <span class="mx-2">/</span>
        <span class="font-medium text-white">Dokumen Renstra</span>
    </div>
</x-slot>

{{-- CONTENT UTAMA --}}
<div class="p-4 md:p-6">
    {{-- Main Card --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden relative">
        
        {{-- Decorative Top Gradient Bar --}}
        <div class="h-1.5 md:h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

        {{-- Header Section --}}
        <div class="px-4 py-6 md:px-8 md:py-8 border-b border-gray-100 bg-white relative">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    {{-- Judul dengan Typography Modern & Responsif --}}
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight flex flex-wrap items-center gap-2 md:gap-3">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                            Dokumen Renstra
                        </span>
                        @if($periode)
                            <span class="px-3 py-1 md:px-4 md:py-1.5 rounded-full text-xs md:text-sm font-bold bg-indigo-50 text-indigo-600 border border-indigo-100 shadow-sm whitespace-nowrap">
                                {{ $periode }}
                            </span>
                        @endif
                    </h2>
                    <p class="mt-2 text-gray-500 text-sm md:text-lg">
                        @if($canManage)
                            Pusat pengelolaan dokumen strategis Dinas Kesehatan.
                        @else
                            Arsip digital resmi Rencana Strategis (Renstra).
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="p-4 md:p-8 bg-gray-50/30">
            {{-- Alert Messages --}}
            @if (session()->has('message'))
                <div class="flex items-start md:items-center p-4 mb-6 md:mb-8 text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm animate-fade-in-down" role="alert">
                    <div class="p-1.5 md:p-2 bg-emerald-100 rounded-full mr-3 flex-shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="font-semibold text-sm md:text-base">{{ session('message') }}</span>
                </div>
            @endif

            {{-- AREA UPLOAD (Hanya Admin) --}}
            @if($canManage)
                <div class="mb-8 md:mb-12">
                    <form wire:submit.prevent="save">
                        <div class="relative group">
                            {{-- Area Upload Responsif --}}
                            <label for="file-upload" class="relative flex flex-col items-center justify-center w-full h-56 md:h-72 border-2 border-dashed rounded-2xl md:rounded-3xl cursor-pointer transition-all duration-300 ease-out overflow-hidden
                                {{ $fileRenstra ? 'border-emerald-400 bg-emerald-50/20' : 'border-gray-300 bg-white hover:border-blue-400 hover:bg-blue-50/30 hover:shadow-lg hover:-translate-y-1' }}">
                                
                                <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>

                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center z-10 px-4" wire:loading.remove wire:target="fileRenstra">
                                    @if($fileRenstra)
                                        <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-3 md:mb-4 shadow-md animate-bounce-slow">
                                            <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-lg md:text-xl font-bold text-gray-700">File Siap!</p>
                                        <p class="text-xs md:text-sm text-gray-500 mt-1">Tekan tombol simpan di bawah.</p>
                                    @else
                                        <div class="w-16 h-16 md:w-20 md:h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-3 md:mb-4 shadow-sm group-hover:scale-110 group-hover:bg-blue-100 transition-transform duration-300">
                                            <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        </div>
                                        <p class="mb-2 text-base md:text-xl font-medium text-gray-600 group-hover:text-blue-600 transition-colors">
                                            <span class="hidden md:inline">Seret & Lepas file PDF di sini</span>
                                            <span class="md:hidden">Ketuk untuk pilih PDF</span>
                                        </p>
                                        <p class="text-xs md:text-sm text-gray-400">atau klik untuk menjelajah (Max 20MB)</p>
                                    @endif
                                </div>

                                {{-- Loading Overlay --}}
                                <div wire:loading.flex wire:target="fileRenstra" class="absolute inset-0 bg-white/90 backdrop-blur-sm z-20 flex-col items-center justify-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                                    <p class="text-blue-600 font-bold animate-pulse text-sm md:text-base">Memproses PDF...</p>
                                </div>

                                <input id="file-upload" type="file" wire:model="fileRenstra" accept="application/pdf" class="hidden" />
                            </label>
                        </div>

                        @error('fileRenstra') 
                            <div class="mt-3 flex items-center text-red-500 bg-red-50 px-4 py-2 rounded-lg border border-red-100 text-sm">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-medium">{{ $message }}</span>
                            </div> 
                        @enderror

                        {{-- Action Buttons Area --}}
                        <div class="mt-6 flex flex-col-reverse md:flex-row justify-end gap-3 transition-all duration-300 {{ $fileRenstra ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none' }}">
                            <button type="button" wire:click="$set('fileRenstra', null)" class="w-full md:w-auto px-6 py-3 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 focus:ring-4 focus:ring-gray-200 transition">
                                Batal
                            </button>
                            <button type="submit" 
                                class="w-full md:w-auto flex items-center justify-center px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-blue-500/30 transform hover:-translate-y-1 transition-all duration-200"
                                wire:loading.attr="disabled" 
                                wire:target="save">
                                <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- PDF Preview Section --}}
            <div class="mt-4">
                @if($existingFile)
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 border-b border-gray-200 pb-6 gap-4">
                        <div>
                            <h3 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                                <span class="p-1.5 md:p-2 bg-red-100 rounded-lg text-red-600">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H8z" clip-rule="evenodd"/></svg>
                                </span>
                                Preview Dokumen
                            </h3>
                            <p class="text-xs md:text-sm text-gray-500 mt-1 md:mt-2 ml-1">Dokumen ini dapat dibaca langsung atau diunduh.</p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3">
                            {{-- BUTTON FULLSCREEN --}}
                            <a href="{{ asset('storage/' . $existingFile) }}" target="_blank" 
                               class="group relative inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-300 shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:scale-105">
                                <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                                <svg class="w-5 h-5 mr-2 -ml-1 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span class="relative">Buka Fullscreen</span>
                            </a>

                            @if($canManage)
                                <button wire:click="deleteFile"
                                        wire:confirm="Yakin ingin menghapus dokumen ini? Tindakan ini tidak bisa dibatalkan."
                                        class="inline-flex w-full sm:w-auto items-center justify-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-rose-500 to-red-600 rounded-xl hover:from-rose-600 hover:to-red-700 shadow-lg shadow-red-500/30 transition-all hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Iframe Container --}}
                    <div class="relative w-full h-[500px] md:h-[850px] bg-gray-900 rounded-xl md:rounded-2xl overflow-hidden border border-gray-800 shadow-2xl">
                        <div class="h-8 bg-gray-800 flex items-center px-4 gap-2 border-b border-gray-700">
                            <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-red-500"></div>
                            <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-green-500"></div>
                            <span class="ml-2 md:ml-4 text-[10px] md:text-xs text-gray-400 font-mono truncate">dokumen-renstra.pdf</span>
                        </div>
                        
                        <iframe 
                            src="{{ asset('storage/' . $existingFile) }}?t={{ time() }}" 
                            class="w-full h-full bg-gray-100"
                            frameborder="0"
                        ></iframe>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="flex flex-col items-center justify-center py-16 md:py-24 bg-white border-2 border-dashed border-gray-200 rounded-3xl group hover:border-gray-300 transition-colors">
                        <div class="bg-gray-50 p-5 md:p-6 rounded-full shadow-inner mb-4 md:mb-6 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-16 h-16 md:w-20 md:h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-800 text-center px-4">Dokumen Belum Tersedia</h3>
                        <p class="text-sm md:text-base text-gray-500 mt-2 max-w-md text-center leading-relaxed px-4">
                            Belum ada dokumen Renstra yang diunggah untuk periode ini.
                        </p>
                        @if(!$canManage)
                            <div class="mt-6 px-6 py-3 bg-gray-100 text-gray-600 rounded-xl text-xs md:text-sm font-medium border border-gray-200">
                                Hubungi Admin untuk informasi lebih lanjut
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>