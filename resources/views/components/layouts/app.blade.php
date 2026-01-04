<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinas kesehatan{{ isset($title) && $title ? ' . '.$title : '' }}</title>
    
    {{-- PERUBAHAN DISINI: Mengganti logo favicon --}}
    <link rel="icon" href="{{ asset('Coat_of_arms_of_South_Kalimantan.svg.png') }}" type="image/png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', 
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
    
    {{-- Script Pencegah FOUC --}}
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    @livewireStyles
</head>

<body class="bg-gray-100 dark:bg-slate-900 font-sans antialiased text-gray-600 dark:text-slate-300 transition-colors duration-300"
      x-data="{ 
          openUser: false,
          isDark: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          
          init() {
              this.updateTheme();
          },

          toggleTheme() {
              this.isDark = !this.isDark;
              localStorage.setItem('color-theme', this.isDark ? 'dark' : 'light');
              this.updateTheme();
          },

          updateTheme() {
              if (this.isDark) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }">

    <div class="min-h-screen flex flex-col">
        
        <header class="bg-gradient-to-r from-blue-200 via-blue-50 to-white dark:from-slate-800 dark:to-slate-900 border-b border-blue-200 dark:border-slate-700 sticky top-0 z-50 transition-all duration-300 shadow-lg">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-24">
                    
                    {{-- Logo Container --}}
                    <div class="flex items-center flex-shrink-0 gap-4">
                        <img src="{{ asset('Logo GERMAS (Gerakan Masyarakat Hidup Sehat).png') }}" alt="Logo GERMAS" class="h-24 w-auto object-contain drop-shadow-sm">
                        <img src="{{ asset('logo pemprov.png') }}" alt="Logo Pemprov" class="h-20 w-auto object-contain drop-shadow-sm">
                    </div>

                    <nav class="hidden lg:flex space-x-2 items-center justify-center flex-1 px-4 whitespace-nowrap">
                        
                        @php
                            $dashboardRoute = route('dashboard');
                            if(auth()->user()->role == 'admin') $dashboardRoute = route('admin.dashboard');
                            if(auth()->user()->role == 'pimpinan') $dashboardRoute = route('pimpinan.dashboard');
                        @endphp
                        
                        {{-- HAPUS wire:navigate DISINI --}}
                        <a href="{{ $dashboardRoute }}" class="text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors whitespace-nowrap {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('pimpinan.dashboard') ? 'text-blue-700 dark:text-blue-400' : '' }}">
                            Dashboard
                        </a>

                        @if(auth()->user()->role == 'pimpinan')
                            
                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Pengukuran Kinerja
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    {{-- HAPUS wire:navigate DISINI --}}
                                    <a href="{{ route('pengukuran.bulanan') }}" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400">
                                        Pengukuran Bulanan
                                    </a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Master Data
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute right-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    {{-- HAPUS wire:navigate DISINI --}}
                                    <a href="/struktur-organisasi" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Struktur Organisasi
                                    </a>
                                </div>
                            </div>

                        @else 
                        <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Matrik Renstra
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                   {{-- HAPUS wire:navigate DI BAWAH INI --}}
                                   <a href="{{ route('matrik.dokumen') }}" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 border-b border-gray-50 dark:border-slate-700">Dokumen Renstra</a>
                                    <a href="/matrik-renstra/tujuan" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 border-b border-gray-50 dark:border-slate-700">Tujuan</a>
                                    <a href="/matrik-renstra/sasaran" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 border-b border-gray-50 dark:border-slate-700">Sasaran</a>
                                    <a href="/matrik-renstra/outcome" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 border-b border-gray-50 dark:border-slate-700">Outcome</a>
                                    <a href="/matrik-renstra/program-kegiatan-sub" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400">Program/Kegiatan/Sub</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Perencanaan Kinerja
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    {{-- HAPUS wire:navigate DI BAWAH INI --}}
                                    <a href="{{ route('cascading.renstra') }}" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 border-b border-gray-50 dark:border-slate-700">Cascading Renstra</a>
                                    <a href="{{ route('perjanjian.kinerja') }}" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400">Perjanjian Kinerja</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Pengukuran Kinerja
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    {{-- HAPUS wire:navigate DISINI --}}
                                    <a href="{{ route('pengukuran.bulanan') }}" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400">Pengukuran Bulanan</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Master Data
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute right-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    {{-- HAPUS wire:navigate DISINI --}}
                                    <a href="/struktur-organisasi" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Struktur Organisasi</a>
                                </div>
                            </div>

                        @endif

                    </nav>

                    {{-- BAGIAN USER PROFILE --}}
                    <div class="flex items-center gap-4 flex-shrink-0 relative">
                        <div class="hidden md:flex flex-col text-right cursor-pointer" @click="openUser = !openUser">
                            <span class="text-sm font-bold text-gray-800 dark:text-slate-200">{{ auth()->user()->name ?? 'Administrator' }}</span>
                            <span class="text-xs text-gray-600 dark:text-slate-400 uppercase tracking-wide">
                                {{ auth()->user()->jabatan ?? 'Pegawai' }}
                            </span>
                        </div>
                        
                        <div class="h-12 w-12 rounded-full bg-white/50 dark:bg-slate-700 border-2 border-white dark:border-slate-600 shadow-md overflow-hidden cursor-pointer hover:shadow-lg transition-shadow" @click="openUser = !openUser">
                            <img src="{{ asset('user-icon.png') }}" alt="User" class="h-full w-full object-cover">
                        </div>
                        
                        {{-- DROPDOWN USER & DARK MODE TOGGLE --}}
                        <div x-show="openUser" @click.outside="openUser = false" style="display: none;" class="absolute right-0 top-14 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl py-2 border border-gray-100 dark:border-slate-700 z-50 animate-fade-in-down">
                            
                            {{-- BUTTON TOGGLE DARK MODE --}}
                            <button @click="toggleTheme()" class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 flex items-center justify-between transition-colors">
                                <div class="flex items-center">
                                    <svg x-show="!isDark" class="w-4 h-4 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <svg x-show="isDark" style="display: none;" class="w-4 h-4 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                    <span x-text="isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <div class="w-9 h-5 bg-gray-200 dark:bg-slate-600 rounded-full peer-focus:outline-none transition-colors"></div>
                                    <div class="absolute left-[2px] top-[2px] bg-white border border-gray-300 rounded-full h-4 w-4 transition-transform duration-300 ease-in-out" :class="isDark ? 'translate-x-full border-white bg-blue-500' : 'translate-x-0'"></div>
                                </div>
                            </button>

                            <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                            
                            {{-- LOGOUT BUTTON SECURE --}}
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <div class="bg-blue-600 dark:bg-blue-900 pb-48 pt-10 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center text-blue-100 text-sm mb-6">
                    {{-- HAPUS wire:navigate DISINI --}}
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'pimpinan' ? route('pimpinan.dashboard') : route('dashboard')) }}" class="hover:text-white transition-colors">
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