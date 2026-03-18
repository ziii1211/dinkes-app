<div>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-slate-200">Laporan Rencana Strategis (Renstra)</h2>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Pilih Periode Tahun</label>
                <select wire:model="tahun" class="w-full rounded-md border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Semua Tahun --</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button wire:click="tampilkanData" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Tampilkan Preview
                </button>
                
                <a href="{{ route('matrik.dokumen.print') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PDF
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto p-4">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 border border-gray-200 dark:border-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider border-r">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r">Tujuan Renstra (Preview)</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Indikator</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    
                    @if($isPreview)
                        @forelse($dataTujuan as $index => $tujuan)
                            <tr>
                                <td class="px-4 py-3 text-sm text-center border-r">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium border-r">{{ $tujuan->nama ?? $tujuan->tujuan ?? 'Tujuan...' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($tujuan->pohonKinerja && $tujuan->pohonKinerja->indikators->count() > 0)
                                        <ul class="list-disc ml-4">
                                            @foreach($tujuan->pohonKinerja->indikators as $indikator)
                                                <li>{{ $indikator->nama_indikator }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400 italic">Belum ada indikator</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-red-500">
                                    Data Renstra tidak ditemukan untuk filter tersebut.
                                </td>
                            </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-slate-400">
                                Silakan klik <b>"Tampilkan Preview"</b> untuk mengecek data sebelum mencetak PDF.
                            </td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>
</div>