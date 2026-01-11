<div>
    {{-- CUSTOM STYLES --}}
    <style>
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-5px); } 100% { transform: translateY(0px); } }
        .animate-enter { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .animate-float { animation: float 4s ease-in-out infinite; }
    </style>

    <x-slot:title>Manajemen User</x-slot>
    <x-slot:breadcrumb>
        {{-- BREADCRUMB RESPONSIF --}}
        <div class="overflow-x-auto whitespace-nowrap pb-2">
            <a href="/" class="hover:text-blue-200 transition-colors duration-200">Dashboard</a>
            <span class="mx-2 text-blue-300">/</span>
            <span class="font-medium text-white">Manajemen User</span>
        </div>
    </x-slot>

    <div class="space-y-8 pb-10 mt-6 md:mt-0">
        
        {{-- FLASH MESSAGE SECTION --}}
        @if (session()->has('message'))
            <div class="animate-enter bg-emerald-50 dark:bg-emerald-900/30 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-800 rounded-full text-emerald-600 dark:text-emerald-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-100">Berhasil!</h4>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300">{{ session('message') }}</p>
                    </div>
                </div>
                <button wire:click="$refresh" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="animate-enter bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-800 rounded-full text-red-600 dark:text-red-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-red-800 dark:text-red-100">Gagal!</h4>
                        <p class="text-xs text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
                <button wire:click="$refresh" class="text-red-500 hover:text-red-700 dark:hover:text-red-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        {{-- 1. HEADER: ACTION & STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Tombol Tambah --}}
            <div class="md:col-span-1 animate-enter" style="animation-delay: 0.1s">
                <button wire:click="create" class="w-full h-full min-h-[140px] relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-xl shadow-blue-900/20 group hover:shadow-2xl hover:shadow-blue-600/40 transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full p-6">
                        <div class="p-3 bg-white/10 rounded-2xl mb-3 backdrop-blur-sm border border-white/10 group-hover:bg-white/20 transition-all duration-300 shadow-inner group-hover:rotate-12 group-hover:scale-110">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <span class="font-bold text-lg tracking-wide">Tambah User</span>
                    </div>
                </button>
            </div>

            {{-- Stats Cards --}}
            @foreach(['pimpinan' => ['orange', 'Pimpinan', '21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'], 'pegawai' => ['emerald', 'Pegawai', '17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'], 'admin' => ['purple', 'Admin', '12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z']] as $key => $data)
            <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-lg shadow-gray-200/50 dark:shadow-none relative overflow-hidden group animate-enter hover:-translate-y-1 transition-transform duration-300" style="animation-delay: 0.{{ $loop->iteration + 1 }}s">
                <div class="absolute right-0 top-0 w-24 h-24 bg-{{ $data[0] }}-50 dark:bg-{{ $data[0] }}-900/10 rounded-bl-[80px] -mr-4 -mt-4 transition-all duration-500 group-hover:bg-{{ $data[0] }}-100 dark:group-hover:bg-{{ $data[0] }}-900/20"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-{{ $data[0] }}-100/80 text-{{ $data[0] }}-600 rounded-xl shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $data[2] }}"></path></svg>
                        </div>
                        <h3 class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest">{{ $data[1] }}</h3>
                    </div>
                    <p class="text-3xl font-black text-gray-800 dark:text-white">{{ $stats[$key] }} <span class="text-xs font-normal text-gray-400">Org</span></p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- 2. MAIN CONTENT TABLE --}}
        <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl shadow-gray-200/40 dark:shadow-none border border-gray-100 dark:border-slate-700 overflow-hidden animate-enter" style="animation-delay: 0.5s">
            {{-- Toolbar with SEARCH BUTTON --}}
            <div class="p-6 md:p-8 border-b border-gray-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col md:flex-row gap-5 justify-between items-center sticky top-0 z-20">
                <div class="relative w-full md:w-[550px] group flex flex-col sm:flex-row gap-2">
                    {{-- Input Pencarian --}}
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input wire:model="search" wire:keydown.enter="$refresh" type="text" class="block w-full pl-12 pr-5 py-4 border-none bg-gray-50 dark:bg-slate-900/50 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500/30 focus:bg-white dark:focus:bg-slate-800 transition-all text-sm font-medium shadow-sm group-focus-within:shadow-md" placeholder="Cari Nama User, NIP, atau Jabatan...">
                    </div>
                    
                    {{-- Tombol Search --}}
                    <button wire:click="$refresh" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
                        <span>Cari</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

                <div class="w-full md:w-56 relative group">
                    <select wire:model.live="filterRole" class="block w-full py-4 pl-5 pr-10 border-none bg-gray-50 dark:bg-slate-900/50 rounded-2xl text-gray-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/30 focus:bg-white dark:focus:bg-slate-800 transition-all text-sm font-bold cursor-pointer shadow-sm appearance-none">
                        <option value="">Semua Role</option>
                        <option value="pimpinan">👔 Pimpinan</option>
                        <option value="pegawai">👨‍💼 Pegawai</option>
                        <option value="admin">🛠️ Admin</option>
                    </select>
                </div>
            </div>

            {{-- Table Wrapper --}}
            <div class="overflow-x-auto min-h-[400px]">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-slate-900/30 text-[11px] uppercase tracking-widest text-gray-400 dark:text-slate-500 font-bold border-b border-gray-100 dark:border-slate-700">
                            <th class="px-8 py-5 min-w-[300px]">Profile Pegawai</th>
                            <th class="px-6 py-5 min-w-[200px]">Role & Akses</th>
                            <th class="px-6 py-5 min-w-[250px]">Jabatan Struktural</th>
                            <th class="px-6 py-5 text-center min-w-[150px]">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                        @forelse($users as $index => $user)
                        <tr wire:key="user-{{ $user->id }}" class="group hover:bg-blue-50/40 dark:hover:bg-slate-700/30 transition-all duration-300 animate-enter" style="animation-delay: {{ $index * 50 }}ms">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-5">
                                    <div class="relative group-hover:scale-105 transition-transform duration-300 flex-shrink-0">
                                        @if($user->pegawai && $user->pegawai->foto)
                                            <img src="{{ asset('storage/' . $user->pegawai->foto) }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-2xl object-cover shadow-md ring-2 ring-white dark:ring-slate-800">
                                        @else
                                            @php
                                                $avatarBg = match($user->role) {
                                                    'admin' => 'bg-gradient-to-br from-purple-500 to-purple-700',
                                                    'pimpinan' => 'bg-gradient-to-br from-orange-400 to-orange-600',
                                                    default => 'bg-gradient-to-br from-emerald-400 to-teal-600',
                                                };
                                                $avatarInitial = strtoupper(substr($user->name, 0, 1));
                                            @endphp
                                            <div class="h-12 w-12 rounded-2xl {{ $avatarBg }} flex items-center justify-center text-white text-lg font-bold shadow-md ring-2 ring-white dark:ring-slate-800">
                                                {{ $avatarInitial }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-sm group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors whitespace-normal">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400 font-mono mt-1">
                                            @if($user->role == 'admin') @ {{ $user->username }} @else NIP. {{ $user->nip }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($user->role == 'pimpinan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-orange-100 text-orange-700 border border-orange-200 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-800">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> PIMPINAN
                                    </span>
                                @elseif($user->role == 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-purple-100 text-purple-700 border border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> ADMIN
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> PEGAWAI
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-normal">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-200">{{ $user->jabatan ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit({{ $user->id }})" class="p-2 text-blue-600 bg-blue-50/50 hover:bg-blue-100 rounded-lg transition-colors shadow-sm" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    
                                    @if($user->id !== auth()->id())
                                        <button wire:click="delete({{ $user->id }})" wire:confirm="Apakah Anda yakin ingin menghapus user '{{ $user->name }}'? Tindakan ini tidak dapat dibatalkan." class="p-2 text-red-600 bg-red-50/50 hover:bg-red-100 rounded-lg transition-colors shadow-sm" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-16 text-center text-gray-500">Belum ada data user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 border-t border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">{{ $users->links() }}</div>
        </div>

        {{-- 3. MODAL FORM --}}
        @if($isModalOpen)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity animate-enter" wire:click="closeModal"></div>

            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div class="relative bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-gray-100 dark:border-slate-700 animate-enter" style="animation-duration: 0.3s">
                    
                    <div class="px-4 py-4 sm:px-8 sm:py-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white dark:from-slate-800 dark:to-slate-800">
                        <div><h3 class="text-lg sm:text-xl font-black text-gray-800 dark:text-white tracking-tight">{{ $userId ? 'Edit User' : 'Tambah User' }}</h3></div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    
                    <div class="px-4 py-6 sm:px-8 sm:py-6 space-y-5">
                        
                        {{-- Pilih Role --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Pilih Role Akses</label>
                            <div class="grid grid-cols-3 gap-3"> 
                                @foreach(['pegawai' => '👨‍💼', 'pimpinan' => '👔', 'admin' => '🛠️'] as $val => $icon)
                                <label class="cursor-pointer relative">
                                    <input type="radio" wire:model.live="role" value="{{ $val }}" class="peer sr-only">
                                    <div class="rounded-xl border border-gray-200 dark:border-slate-600 p-3 text-center hover:bg-gray-50 dark:hover:bg-slate-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all">
                                        <div class="text-xl mb-1">{{ $icon }}</div>
                                        <span class="text-xs font-bold text-gray-600 dark:text-slate-300 uppercase block">{{ $val }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-gray-50/50 dark:bg-slate-700/30 p-5 rounded-xl border border-dashed border-gray-200 dark:border-slate-600">
                            
                            {{-- LOGIC FORM --}}
                            @if($role === 'admin')
                                {{-- Form Admin --}}
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Nama Admin</label>
                                        <input wire:model="name" type="text" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 py-3 px-4 focus:ring-2 focus:ring-blue-500 dark:text-white" placeholder="Nama Lengkap Admin">
                                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Username</label>
                                        <input wire:model="username" type="text" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 py-3 px-4 focus:ring-2 focus:ring-blue-500 dark:text-white font-mono" placeholder="username_login">
                                        @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @else
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Cari Pegawai (Sesuai Struktur Organisasi)</label>
                                        <select wire:model.live="selectedPegawaiId" class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 py-3 px-4 focus:ring-2 focus:ring-blue-500 dark:text-white appearance-none cursor-pointer">
                                            <option value="">-- Pilih Nama Pegawai --</option>
                                            @foreach($daftarPegawai as $p)
                                                <option value="{{ $p->id }}" class="py-2">
                                                    {{ $p->nama }} — {{ $p->nama_jabatan }} (NIP. {{ $p->nip }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-[10px] text-gray-500 mt-1">
                                            *Data otomatis terisi: Nama, NIP, dan Jabatan.
                                        </p>
                                        @error('selectedPegawaiId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        @error('nip') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    @if($name)
                                        <div class="grid grid-cols-2 gap-4 bg-white dark:bg-slate-800 p-3 rounded-lg border border-gray-200 dark:border-slate-600">
                                            <div>
                                                <span class="text-[10px] text-gray-400 uppercase font-bold">Nama</span>
                                                <div class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $name }}</div>
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-gray-400 uppercase font-bold">Jabatan</span>
                                                <div class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $jabatan }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Password Input with Toggle --}}
                            <div class="mt-4" x-data="{ show: false }">
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">
                                    Password {{ $userId ? '(Opsional, isi jika ingin mengubah)' : '' }}
                                </label>
                                <div class="relative">
                                    <input wire:model="password" :type="show ? 'text' : 'password'" 
                                           class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700/50 py-3 pl-4 pr-10 focus:ring-2 focus:ring-blue-500 dark:text-white" 
                                           placeholder="••••••••">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                        <template x-if="!show">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.575-3.107m5.858.858a3 3 0 114.243 4.243m-9.965-9.966l16.1 16.1"></path></svg>
                                        </template>
                                        <template x-if="show">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </template>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 dark:bg-slate-700/50 px-4 py-4 sm:px-8 sm:py-5 flex flex-row-reverse gap-3 border-t border-gray-100 dark:border-slate-700">
                        <button wire:click="{{ $userId ? 'update' : 'store' }}" wire:loading.attr="disabled" type="button" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl shadow-lg shadow-blue-500/30 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-sm font-bold text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none transition-all transform hover:-translate-y-0.5">
                            <span wire:loading.remove>{{ $userId ? 'Simpan Perubahan' : 'Buat User' }}</span>
                            <span wire:loading class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Proses...</span>
                        </button>
                        <button wire:click="closeModal" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-xl border-2 border-transparent hover:bg-gray-200 dark:hover:bg-slate-600 px-5 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-300 transition-all">Batal</button>
                    </div>

                </div>
            </div>
        </div>
        @endif
    </div>
</div>