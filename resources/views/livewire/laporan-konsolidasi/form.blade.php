<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $isEdit ? 'Edit' : 'Buat' }} Laporan Baru</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Isi informasi dasar laporan sebelum menginput data excel.</p>
    </div>

    {{-- Card Form --}}
    <div class="bg-white dark:bg-slate-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-slate-700">
        <form wire:submit.prevent="save" class="p-8">
            <div class="space-y-6">
                
                {{-- Judul Input --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Laporan</label>
                    <input type="text" wire:model="judul" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-900 dark:text-white transition-shadow" 
                           placeholder="Misal: Laporan Kinerja Triwulan I">
                    @error('judul') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Bulan Select --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Periode Bulan</label>
                        <select wire:model="bulan" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-900 dark:text-white transition-shadow">
                            <option value="">-- Pilih Bulan --</option>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        @error('bulan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tahun Input --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tahun Anggaran</label>
                        <input type="number" wire:model="tahun" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-900 dark:text-white transition-shadow"
                               placeholder="2025">
                        @error('tahun') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-3">
                <a href="{{ route('laporan-konsolidasi.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all shadow-md">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan & Lanjut' }}
                </button>
            </div>
        </form>
    </div>
</div>