<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Informasi Kinerja Terintegrasi' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 'dinkes-blue': '#007bff' },
                    keyframes: {
                        'fade-in-down': {
                            '0%': { opacity: '0', transform: 'translateY(-10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
                    animation: {
                        'fade-in-down': 'fade-in-down 0.2s ease-out',
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-600">

    <div class="min-h-screen flex flex-col">
        
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-24">
                    
                    <div class="flex items-center flex-shrink-0">
                        <img src="{{ asset('logo-sakip (1).png') }}" alt="Logo SAKIP" class="h-20 w-auto object-contain">
                    </div>

                    <nav class="hidden lg:flex space-x-2 items-center justify-center flex-1 px-4 whitespace-nowrap">
                        
                        @php
                            $dashboardRoute = route('dashboard');
                            if(auth()->user()->role == 'admin') $dashboardRoute = route('admin.dashboard');
                            if(auth()->user()->role == 'pimpinan') $dashboardRoute = route('pimpinan.dashboard');
                        @endphp
                        
                        <a href="{{ $dashboardRoute }}" wire:navigate class="text-gray-700 hover:text-blue-600 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors whitespace-nowrap {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('pimpinan.dashboard') ? 'text-blue-600' : '' }}">
                            Dashboard
                        </a>

                        @if(auth()->user()->role == 'pimpinan')
                            
                            <div class="relative group">
                                <button class="flex items-center text-gray-700 hover:text-blue-600 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Pengukuran Kinerja
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white border border-gray-100 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    <a href="{{ route('pengukuran.bulanan') }}" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                                        Pengukuran Bulanan
                                    </a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-700 hover:text-blue-600 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Master Data
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute right-0 mt-0 w-64 bg-white border border-gray-100 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    <a href="/struktur-organisasi" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                        Struktur Organisasi
                                    </a>
                                </div>
                            </div>

                        @else 
                        <div class="relative group">
                                <button class="flex items-center text-gray-700 hover:text-blue-600 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Matrik Renstra
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white border border-gray-100 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                   <a href="{{ route('matrik.dokumen') }}" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-50">Dokumen Renstra</a>
                                    <a href="/matrik-renstra/tujuan" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-50">Tujuan</a>
                                    <a href="/matrik-renstra/sasaran" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-50">Sasaran</a>
                                    <a href="/matrik-renstra/outcome" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-50">Outcome</a>
                                    <a href="/matrik-renstra/program-kegiatan-sub" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600">Program/Kegiatan/Sub</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-700 hover:text-blue-600 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Perencanaan Kinerja
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white border border-gray-100 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    <a href="{{ route('pohon.kinerja') }}" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-50">Pohon Kinerja</a>
                                    <a href="{{ route('cascading.renstra') }}" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-50">Cascading Renstra</a>
                                    <a href="{{ route('perjanjian.kinerja') }}" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600">Perjanjian Kinerja</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-700 hover:text-blue-600 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Pengukuran Kinerja
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white border border-gray-100 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    <a href="{{ route('pengukuran.bulanan') }}" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600">Pengukuran Bulanan</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-700 hover:text-blue-600 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Master Data
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute right-0 mt-0 w-64 bg-white border border-gray-100 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    <a href="/struktur-organisasi" wire:navigate class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors">Struktur Organisasi</a>
                                </div>
                            </div>

                        @endif

                    </nav>

                    <div class="flex items-center gap-4 flex-shrink-0 relative" x-data="{ openUser: false }">
                        <div class="hidden md:flex flex-col text-right cursor-pointer" @click="openUser = !openUser">
                            <span class="text-sm font-bold text-gray-800">{{ auth()->user()->name ?? 'Administrator' }}</span>
                            <span class="text-xs text-gray-500 uppercase">{{ auth()->user()->role ?? 'Pegawai' }}</span>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-gray-200 border-2 border-white shadow-md overflow-hidden cursor-pointer hover:shadow-lg transition-shadow" @click="openUser = !openUser">
                            <img src="{{ asset('user-icon.png') }}" alt="User" class="h-full w-full object-cover">
                        </div>
                        <div x-show="openUser" @click.outside="openUser = false" style="display: none;" class="absolute right-0 top-14 w-48 bg-white rounded-lg shadow-xl py-2 border border-gray-100 z-50 animate-fade-in-down">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <div class="bg-blue-600 pb-48 pt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center text-blue-100 text-sm mb-6">
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'pimpinan' ? route('pimpinan.dashboard') : route('dashboard')) }}" wire:navigate class="hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </a>
                    
                    @if (isset($breadcrumb))
                        {{ $breadcrumb }}
                    @else
                        <span class="mx-2">/</span>
                        <span class="font-medium text-white">Dashboard</span>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <svg class="w-10 h-10 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    <h1 class="text-3xl font-bold text-white tracking-wide">
                        @if(isset($title))
                            {{ $title }}
                        @else
                            @if(auth()->user()->role === 'admin') Admin Dashboard
                            @elseif(auth()->user()->role === 'pimpinan') Dashboard Pimpinan
                            @else Dashboard Kinerja @endif
                        @endif
                    </h1>
                </div>
            </div>
        </div>

        <main class="-mt-32 pb-12 z-10 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>
    @livewireScripts
</body>
</html>