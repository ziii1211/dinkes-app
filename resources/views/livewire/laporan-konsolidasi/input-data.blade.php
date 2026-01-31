<div class="space-y-6 pb-24">
    
    {{-- HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('laporan-konsolidasi.index') }}" class="hover:text-blue-600 transition-colors">&larr; Kembali</a>
                <span>/</span>
                <span>Input Data</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $laporan->judul }}</h2>
            <div class="flex items-center gap-3 mt-1">
                <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">{{ $laporan->bulan }}</span>
                <span class="px-2.5 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs font-bold border border-gray-200">{{ $laporan->tahun }}</span>
            </div>
        </div>

        <div class="flex gap-6 items-center bg-gray-50 px-5 py-3 rounded-xl border border-gray-200 shadow-inner">
            <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Total Anggaran</p>
                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
            </div>
            <div class="w-px h-10 bg-gray-300"></div>
            <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Total Realisasi</p>
                <p class="text-lg font-bold text-green-600">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- MESSAGE --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- TOOLBAR --}}
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-700 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            Rincian Matrik Renstra
        </h3>
        
        <button wire:click="openProgramModal" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Program
        </button>
    </div>

    {{-- TABEL INPUT DATA --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden relative">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-20 text-center border-r border-slate-200">Kode</th>
                        <th class="px-4 py-3 min-w-[250px] border-r border-slate-200">Uraian</th>
                        {{-- Kolom Input dilebarkan agar nyaman --}}
                        <th class="px-4 py-3 w-64 border-r border-slate-200">Sub Output</th>
                        <th class="px-4 py-3 w-32 border-r border-slate-200">Satuan</th>
                        <th class="px-4 py-3 w-44 text-right border-r border-slate-200 bg-blue-50/20">Pagu Anggaran</th>
                        <th class="px-4 py-3 w-44 text-right border-r border-slate-200 bg-green-50/20">Realisasi</th>
                        <th class="px-2 py-3 w-10 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    @forelse($groupedData as $programId => $kegiatanGroups)
                        
                        {{-- PROGRAM --}}
                        @php 
                            $firstDetail = $kegiatanGroups->first()->first();
                            $program = $firstDetail->subKegiatan->kegiatan->program;
                            $namaProgram = $program->nama ?? $program->nama_program ?? 'Program';
                        @endphp
                        
                        <tr class="bg-blue-100/70 border-t-2 border-blue-200">
                            <td class="px-4 py-3 font-bold text-blue-900 border-r border-blue-200 font-mono text-center align-top">
                                {{ $program->kode ?? '-' }}
                            </td>
                            <td class="px-4 py-3 font-bold text-blue-900 border-r border-blue-200">
                                {{ $namaProgram }}
                            </td>
                            <td colspan="5" class="bg-gray-50/50"></td>
                        </tr>

                        @foreach($kegiatanGroups as $kegiatanId => $details)
                            @php 
                                $kegiatan = $details->first()->subKegiatan->kegiatan;
                                $namaKegiatan = $kegiatan->nama ?? $kegiatan->nama_kegiatan ?? 'Kegiatan';
                            @endphp

                            {{-- KEGIATAN --}}
                            <tr class="bg-gray-50 border-t border-gray-200">
                                <td class="px-4 py-2 font-semibold text-gray-600 border-r border-gray-200 font-mono text-center align-top">
                                    {{ $kegiatan->kode ?? '-' }}
                                </td>
                                <td class="px-4 py-2 font-semibold text-gray-700 border-r border-gray-200 pl-8">
                                    {{ $namaKegiatan }}
                                </td>
                                <td colspan="5"></td>
                            </tr>

                            {{-- SUB KEGIATAN (INPUT ROW) --}}
                            @foreach($details as $detail)
                                <tr class="hover:bg-yellow-50 transition-colors group border-b border-gray-100 bg-white">
                                    {{-- Kode --}}
                                    <td class="px-4 py-2.5 text-center font-mono text-xs text-gray-500 border-r border-gray-200 align-top pt-4">
                                        {{ $detail->kode }}
                                    </td>

                                    {{-- Nama Sub --}}
                                    <td class="px-4 py-2.5 border-r border-gray-200 pl-12 align-top pt-4">
                                        <div class="text-gray-600 leading-snug text-sm">
                                            {{ $detail->subKegiatan->nama ?? $detail->subKegiatan->nama_sub_kegiatan ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- INPUT MANUAL: Sub Output (Textarea) --}}
                                    <td class="p-2 border-r border-gray-200 align-top">
                                        <textarea 
                                            wire:model.defer="inputs.{{ $detail->id }}.sub_output" 
                                            rows="2"
                                            class="w-full text-xs border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-md px-2 py-1.5 resize-none transition-all placeholder-gray-300"
                                            placeholder="Ketik sub output..."
                                        ></textarea>
                                    </td>

                                    {{-- INPUT MANUAL: Satuan --}}
                                    <td class="p-2 border-r border-gray-200 align-top">
                                        <input type="text" 
                                               wire:model.defer="inputs.{{ $detail->id }}.satuan_unit" 
                                               class="w-full text-xs border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-md px-2 py-2 text-center transition-all placeholder-gray-300"
                                               placeholder="Contoh: 3 Dokumen"
                                        >
                                    </td>

                                    {{-- INPUT MANUAL: Anggaran (AlpineJS) --}}
                                    <td class="p-2 border-r border-gray-200 align-top bg-blue-50/5" 
                                        x-data="rupiahInput(@entangle('inputs.'.$detail->id.'.pagu_anggaran').defer)">
                                        <input type="text" 
                                               x-model="displayValue"
                                               @input="formatCurrency($event)"
                                               class="w-full text-xs text-right font-medium text-gray-800 border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-md px-2 py-2 transition-all placeholder-gray-300"
                                               placeholder="Rp 0"
                                        >
                                    </td>

                                    {{-- INPUT MANUAL: Realisasi (AlpineJS) --}}
                                    <td class="p-2 border-r border-gray-200 align-top bg-green-50/5" 
                                        x-data="rupiahInput(@entangle('inputs.'.$detail->id.'.pagu_realisasi').defer)">
                                        <input type="text" 
                                               x-model="displayValue"
                                               @input="formatCurrency($event)"
                                               class="w-full text-xs text-right font-bold text-green-700 border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 rounded-md px-2 py-2 transition-all placeholder-gray-300"
                                               placeholder="Rp 0"
                                        >
                                    </td>

                                    {{-- Delete --}}
                                    <td class="px-2 py-2 text-center align-top pt-3">
                                        <button wire:click="deleteDetail({{ $detail->id }})" 
                                                wire:confirm="Yakin ingin menghapus data ini?"
                                                class="text-gray-300 hover:text-red-500 transition-colors p-1.5 rounded hover:bg-red-50" title="Hapus Baris Ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400 bg-gray-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                    </div>
                                    <span class="font-medium text-gray-600">Belum ada program ditambahkan.</span>
                                    <span class="text-sm text-gray-400 mt-1">Silakan klik tombol "Tambah Program" di atas.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FIXED BOTTOM BAR (SAVE BUTTON) --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 z-40 md:pl-64">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4">
            <div class="text-xs text-gray-500 flex items-center gap-1">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Jangan lupa klik simpan setelah mengubah data.</span>
            </div>
            
            <button wire:click="saveAll" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <svg wire:loading class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Simpan Perubahan
            </button>
        </div>
    </div>

    {{-- SCRIPT ALPINE JS --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('rupiahInput', (entangledModel) => ({
                value: entangledModel, 
                displayValue: '', 

                init() {
                    // Isi nilai awal dari backend
                    this.formatInitial(this.value);
                    
                    // Pantau jika backend mengupdate nilai (misal setelah saveAll)
                    this.$watch('value', (newValue) => {
                        this.formatInitial(newValue);
                    });
                },

                formatCurrency(e) {
                    let inputVal = e.target.value;
                    let numberString = inputVal.replace(/[^,\d]/g, '').toString();
                    let split = numberString.split(',');
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    this.displayValue = rupiah ? 'Rp ' + rupiah : '';
                    this.value = this.displayValue; // Update ke Livewire (defer)
                },

                formatInitial(val) {
                    if(!val) { this.displayValue = ''; return; }
                    // Hapus karakter non-digit untuk diproses
                    let stringVal = val.toString().replace(/[^0-9]/g, '');
                    if(!stringVal) { this.displayValue = ''; return; }

                    let sisa = stringVal.length % 3;
                    let rupiah = stringVal.substr(0, sisa);
                    let ribuan = stringVal.substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    this.displayValue = 'Rp ' + rupiah;
                }
            }));
        });
    </script>

    {{-- MODAL PROGRAM (Tetap Sama) --}}
    @if($isOpenProgram)
    <div class="fixed inset-0 z-[99] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" wire:click="closeProgramModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Pilih Program</h3>
                    <p class="text-sm text-gray-500 mt-1">Data akan ditambahkan secara otomatis.</p>
                </div>

                <div class="p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Program Renstra</label>
                    <select wire:model="selectedProgramId" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                        <option value="">-- Pilih Program --</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->kode }} - {{ $prog->nama ?? $prog->nama_program }}</option>
                        @endforeach
                    </select>
                    @error('selectedProgramId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button wire:click="addProgram" class="inline-flex justify-center items-center rounded-lg shadow-sm px-4 py-2 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none transition-all">Tambahkan</button>
                    <button wire:click="closeProgramModal" class="inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>