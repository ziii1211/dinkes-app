<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinas kesehatan{{ isset($title) && $title ? ' . '.$title : '' }}</title>
    
    {{-- Logo Browser (Favicon) --}}
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
                        },
                        'slide-in-left': {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        }
                    },
                    animation: {
                        'fade-in-down': 'fade-in-down 0.2s ease-out',
                        'slide-in-left': 'slide-in-left 0.3s ease-out',
                    }
                }
            }
        }
    </script>
    
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
          mobileMenuOpen: false, 
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
                <div class="flex justify-between items-center h-20 lg:h-24">
                    
                    {{-- 1. Logo Container (Responsive) --}}
                    <div class="flex items-center flex-shrink-0 gap-2 sm:gap-4">
                        {{-- Hamburger Button (Mobile Only) --}}
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <span class="sr-only">Open menu</span>
                            {{-- Icon Menu --}}
                            <svg x-show="!mobileMenuOpen" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            {{-- Icon Close --}}
                            <svg x-show="mobileMenuOpen" style="display: none;" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <img src="{{ asset('Logo GERMAS (Gerakan Masyarakat Hidup Sehat).png') }}" alt="Logo GERMAS" class="h-12 sm:h-16 lg:h-24 w-auto object-contain drop-shadow-sm">
                        <img src="{{ asset('logo pemprov.png') }}" alt="Logo Pemprov" class="h-10 sm:h-14 lg:h-20 w-auto object-contain drop-shadow-sm">
                    </div>

                    {{-- 2. Desktop Navigation (Hidden on Mobile) --}}
                    <nav class="hidden lg:flex space-x-2 items-center justify-center flex-1 px-4 whitespace-nowrap">
                        
                        @php
                            $dashboardRoute = route('dashboard');
                            if(auth()->user()->role == 'admin') $dashboardRoute = route('admin.dashboard');
                            if(auth()->user()->role == 'pimpinan') $dashboardRoute = route('pimpinan.dashboard');
                        @endphp
                        
                        <a href="{{ $dashboardRoute }}" class="text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors whitespace-nowrap {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('pimpinan.dashboard') ? 'text-blue-700 dark:text-blue-400' : '' }}">
                            Dashboard
                        </a>

                        @if(auth()->user()->role == 'pimpinan')
                            
                            {{-- Pimpinan Menu Desktop --}}
                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Pengukuran Kinerja
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
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
                                    <a href="/struktur-organisasi" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Struktur Organisasi
                                    </a>
                                </div>
                            </div>

                        @else 
                            
                            {{-- Admin & Pegawai Menu Desktop --}}
                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Matrik Renstra
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute left-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
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
                                    <a href="{{ route('pengukuran.bulanan') }}" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400">Pengukuran Bulanan</a>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="flex items-center text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors focus:outline-none whitespace-nowrap">
                                    Master Data
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="absolute right-0 mt-0 w-64 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-xl rounded-b-lg hidden group-hover:block z-50 animate-fade-in-down">
                                    <a href="/struktur-organisasi" class="block px-4 py-3 text-sm text-gray-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Struktur Organisasi</a>
                                </div>
                            </div>
                            
                            @if(auth()->user()->role == 'admin')
                                <a href="{{ route('admin.manajemen-user') }}" class="text-gray-800 dark:text-slate-200 hover:text-blue-700 dark:hover:text-blue-400 font-bold px-3 py-2 text-sm uppercase tracking-wide transition-colors whitespace-nowrap {{ request()->routeIs('admin.manajemen-user') ? 'text-blue-700 dark:text-blue-400' : '' }}">
                                    Manajemen User
                                </a>
                            @endif

                        @endif

                    </nav>

                    {{-- 3. Bagian User Profile --}}
                    <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0 relative">
                        <div class="hidden md:flex flex-col text-right cursor-pointer" @click="openUser = !openUser">
                            <span class="text-sm font-bold text-gray-800 dark:text-slate-200">{{ auth()->user()->name ?? 'Administrator' }}</span>
                            <span class="text-xs text-gray-600 dark:text-slate-400 uppercase tracking-wide">
                                {{ auth()->user()->jabatan ?? 'Pegawai' }}
                            </span>
                        </div>
                        
                        <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-white/50 dark:bg-slate-700 border-2 border-white dark:border-slate-600 shadow-md overflow-hidden cursor-pointer hover:shadow-lg transition-shadow" @click="openUser = !openUser">
                            <img src="{{ asset('user-icon.png') }}" alt="User" class="h-full w-full object-cover">
                        </div>
                        
                        {{-- Dropdown User --}}
                        <div x-show="openUser" @click.outside="openUser = false" style="display: none;" class="absolute right-0 top-14 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl py-2 border border-gray-100 dark:border-slate-700 z-50 animate-fade-in-down">
                            {{-- Info User Mobile --}}
                            <div class="md:hidden px-4 py-2 border-b border-gray-100 dark:border-slate-700 mb-1">
                                <div class="font-bold text-gray-800 dark:text-slate-200">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ auth()->user()->jabatan }}</div>
                            </div>

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

            {{-- 4. Mobile Navigation Menu (Drawer/Accordion) --}}
            <div x-show="mobileMenuOpen" class="lg:hidden border-t border-blue-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-inner animate-fade-in-down">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    @php
                        $dashboardRoute = route('dashboard');
                        if(auth()->user()->role == 'admin') $dashboardRoute = route('admin.dashboard');
                        if(auth()->user()->role == 'pimpinan') $dashboardRoute = route('pimpinan.dashboard');
                    @endphp

                    <a href="{{ $dashboardRoute }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('pimpinan.dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-slate-800 dark:text-blue-400' : '' }}">
                        Dashboard
                    </a>

                    @if(auth()->user()->role == 'pimpinan')
                        {{-- Pimpinan Menu Mobile --}}
                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800">
                                <span>Pengukuran Kinerja</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" class="pl-4 space-y-1">
                                <a href="{{ route('pengukuran.bulanan') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Pengukuran Bulanan</a>
                            </div>
                        </div>

                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800">
                                <span>Master Data</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" class="pl-4 space-y-1">
                                <a href="/struktur-organisasi" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Struktur Organisasi</a>
                            </div>
                        </div>

                    @else
                        {{-- Admin & Pegawai Menu Mobile --}}
                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800">
                                <span>Matrik Renstra</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" class="pl-4 space-y-1">
                                <a href="{{ route('matrik.dokumen') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Dokumen Renstra</a>
                                <a href="/matrik-renstra/tujuan" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Tujuan</a>
                                <a href="/matrik-renstra/sasaran" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Sasaran</a>
                                <a href="/matrik-renstra/outcome" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Outcome</a>
                                <a href="/matrik-renstra/program-kegiatan-sub" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Program/Kegiatan/Sub</a>
                            </div>
                        </div>

                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800">
                                <span>Perencanaan Kinerja</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" class="pl-4 space-y-1">
                                <a href="{{ route('cascading.renstra') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Cascading Renstra</a>
                                <a href="{{ route('perjanjian.kinerja') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Perjanjian Kinerja</a>
                            </div>
                        </div>

                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800">
                                <span>Pengukuran Kinerja</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" class="pl-4 space-y-1">
                                <a href="{{ route('pengukuran.bulanan') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Pengukuran Bulanan</a>
                            </div>
                        </div>

                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800">
                                <span>Master Data</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="expanded" class="pl-4 space-y-1">
                                <a href="/struktur-organisasi" class="block px-3 py-2 rounded-md text-sm text-gray-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-300">Struktur Organisasi</a>
                            </div>
                        </div>

                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('admin.manajemen-user') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-slate-200 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-slate-800">
                                Manajemen User
                            </a>
                        @endif

                    @endif
                </div>
            </div>
        </header>

        {{-- Hero Header (Dashboard Title) --}}
        <div class="bg-blue-600 dark:bg-blue-900 pb-32 sm:pb-48 pt-6 sm:pt-10 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center text-blue-100 text-sm mb-4 sm:mb-6">
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
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-wide">
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

        {{-- Main Content --}}
        <main class="-mt-24 sm:-mt-32 pb-12 z-10 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>

    {{-- SWEETALERT2 CDN & SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            
            // Konfigurasi Toast Modern
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1f2937',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Listener Global untuk Event 'alert' dari Livewire
            Livewire.on('alert', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                Toast.fire({
                    icon: data.type,   
                    title: data.title, 
                    text: data.message 
                });
            });

            // Listener Global untuk Konfirmasi Hapus
            Livewire.on('confirmDelete', (data) => {
                let id = (typeof data === 'object' && data !== null && 'id' in data) ? data.id : data;

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1f2937'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteConfirmed', { id: id }); 
                    }
                })
            });
        });
    </script>

    @livewireScripts
</body>
</html>