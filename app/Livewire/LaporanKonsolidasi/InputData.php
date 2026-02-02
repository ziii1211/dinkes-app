<?php

namespace App\Livewire\LaporanKonsolidasi;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use App\Models\LaporanKonsolidasi;
use App\Models\DetailLaporanKonsolidasi;
use App\Models\LaporanKonsolidasiAnggaran; // Load Model Baru
use App\Models\Program;
use App\Models\Kegiatan; 
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\DB;

class InputData extends Component
{
    use WithPagination;

    public $laporan;
    public $isOpenProgram = false;
    public $selectedProgramId;
    
    public $perPage = 10;
    
    public $inputs = [];         
    public $programInputs = [];  
    public $kegiatanInputs = []; 

    public function mount($id)
    {
        $this->laporan = LaporanKonsolidasi::findOrFail($id);
        $this->loadData();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function loadData()
    {
        // 1. Load Detail (Sub Kegiatan)
        $details = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan.program'])
                    ->where('laporan_konsolidasi_id', $this->laporan->id)
                    ->get();

        $this->inputs = [];
        foreach ($details as $detail) {
            $this->inputs[$detail->id] = [
                'sub_output' => $detail->sub_output,
                'satuan_unit' => $detail->satuan_unit,
                'pagu_anggaran' => $detail->pagu_anggaran ? number_format($detail->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $detail->pagu_realisasi ? number_format($detail->pagu_realisasi, 0, ',', '.') : '',
            ];
        }

        // 2. Load Data PROGRAM (Dari Tabel LaporanKonsolidasiAnggaran, BUKAN Master Program)
        $programIds = $details->pluck('subKegiatan.kegiatan.program_id')->unique()->filter();
        
        // Ambil data anggaran yang tersimpan KHUSUS untuk laporan ini
        $savedPrograms = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('program_id')
                            ->whereIn('program_id', $programIds)
                            ->get()
                            ->keyBy('program_id');

        $this->programInputs = [];
        foreach($programIds as $progId) {
            $saved = $savedPrograms[$progId] ?? null;
            $this->programInputs[$progId] = [
                'pagu_anggaran' => $saved ? number_format($saved->pagu_anggaran, 0, ',', '.') : '', // Default Kosong jika laporan baru
                'pagu_realisasi' => $saved ? number_format($saved->pagu_realisasi, 0, ',', '.') : ''
            ];
        }

        // 3. Load Data KEGIATAN (Dari Tabel LaporanKonsolidasiAnggaran, BUKAN Master Kegiatan)
        $kegiatanIds = $details->pluck('subKegiatan.kegiatan_id')->unique()->filter();
        
        $savedKegiatans = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('kegiatan_id')
                            ->whereIn('kegiatan_id', $kegiatanIds)
                            ->get()
                            ->keyBy('kegiatan_id');

        $this->kegiatanInputs = [];
        foreach($kegiatanIds as $kegId) {
            $saved = $savedKegiatans[$kegId] ?? null;
            $this->kegiatanInputs[$kegId] = [
                'pagu_anggaran' => $saved ? number_format($saved->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $saved ? number_format($saved->pagu_realisasi, 0, ',', '.') : ''
            ];
        }
    }

    public function saveAll()
    {
        DB::beginTransaction();
        try {
            // --- 1. Simpan Detail Sub Kegiatan ---
            foreach ($this->inputs as $detailId => $data) {
                $detail = DetailLaporanKonsolidasi::find($detailId);
                if ($detail) {
                    $detail->update([
                        'sub_output'    => $data['sub_output'] ?? null,
                        'satuan_unit'   => $data['satuan_unit'] ?? null,
                        'pagu_anggaran' => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'),
                        'pagu_realisasi'=> $this->cleanRupiah($data['pagu_realisasi'] ?? '0'),
                    ]);
                }
            }

            // --- 2. Simpan Anggaran PROGRAM ke Tabel Baru ---
            if (!empty($this->programInputs)) {
                foreach ($this->programInputs as $progId => $data) {
                    LaporanKonsolidasiAnggaran::updateOrCreate(
                        [
                            'laporan_konsolidasi_id' => $this->laporan->id,
                            'program_id' => $progId
                        ],
                        [
                            'pagu_anggaran' => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'),
                            'pagu_realisasi'=> $this->cleanRupiah($data['pagu_realisasi'] ?? '0')
                        ]
                    );
                }
            }

            // --- 3. Simpan Anggaran KEGIATAN ke Tabel Baru ---
            if (!empty($this->kegiatanInputs)) {
                foreach ($this->kegiatanInputs as $kegId => $data) {
                    LaporanKonsolidasiAnggaran::updateOrCreate(
                        [
                            'laporan_konsolidasi_id' => $this->laporan->id,
                            'kegiatan_id' => $kegId
                        ],
                        [
                            'pagu_anggaran' => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'),
                            'pagu_realisasi'=> $this->cleanRupiah($data['pagu_realisasi'] ?? '0')
                        ]
                    );
                }
            }
            
            DB::commit();
            $this->loadData(); 
            session()->flash('message', 'Semua data berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function cleanRupiah($val)
    {
        $clean = preg_replace('/[^0-9]/', '', $val);
        return $clean === '' ? 0 : (float) $clean;
    }

    public function deleteDetail($id)
    {
        DetailLaporanKonsolidasi::find($id)->delete();
        unset($this->inputs[$id]);
        $this->loadData(); 
    }

    public function render()
    {
        // 1. Ambil Data Detail
        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan.program'])
                        ->where('laporan_konsolidasi_id', $this->laporan->id)
                        ->get();

        // 2. Ambil Data Anggaran Program & Kegiatan dari Tabel KHUSUS Laporan ini
        $anggaranKhusus = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->get();

        // 3. HITUNG TOTAL (Gabungan Sub Kegiatan + Program + Kegiatan)
        
        // Total Sub Kegiatan
        $subAnggaran = $detailsRaw->sum('pagu_anggaran');
        $subRealisasi = $detailsRaw->sum('pagu_realisasi');

        // Total Program & Kegiatan (Ambil dari tabel baru)
        $progKegAnggaran = $anggaranKhusus->sum('pagu_anggaran');
        $progKegRealisasi = $anggaranKhusus->sum('pagu_realisasi');

        $totalAnggaran = $subAnggaran + $progKegAnggaran;
        $totalRealisasi = $subRealisasi + $progKegRealisasi;

        // 4. Sorting & Pagination Logic
        $detailsSorted = $detailsRaw->sortBy(function($detail) {
            $progKode = $detail->subKegiatan->kegiatan->program->kode ?? '99';
            $kegKode  = $detail->subKegiatan->kegiatan->kode ?? '99';
            $subKode  = $detail->subKegiatan->kode ?? '99';
            return $progKode . '.' . $kegKode . '.' . $subKode;
        });

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $detailsSorted->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
        
        $paginatedDetails = new LengthAwarePaginator(
            $currentItems,
            $detailsSorted->count(),
            $this->perPage,
            $currentPage,
            ['path' => Request::url(), 'query' => Request::query()]
        );

        $groupedData = collect($currentItems)->groupBy(function($item) {
            return $item->subKegiatan->kegiatan->program_id ?? 0;
        })->map(function($programGroup) {
            return $programGroup->groupBy(function($item) {
                return $item->subKegiatan->kegiatan_id ?? 0;
            });
        });

        return view('livewire.laporan-konsolidasi.input-data', [
            'programs' => Program::all(),
            'groupedData' => $groupedData,
            'paginatedDetails' => $paginatedDetails, 
            'totalAnggaran' => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi
        ]);
    }

    // Modal Logic (Tetap)
    public function openProgramModal() { $this->isOpenProgram = true; }
    public function closeProgramModal() { $this->isOpenProgram = false; $this->selectedProgramId = null; }
    
    public function addProgram()
    {
        $this->validate(['selectedProgramId' => 'required']);
        $subKegiatans = SubKegiatan::whereHas('kegiatan', function($q) {
            $q->where('program_id', $this->selectedProgramId);
        })->get();

        if ($subKegiatans->isEmpty()) {
            session()->flash('error', 'Program ini tidak memiliki Sub Kegiatan.');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($subKegiatans as $sub) {
                $exists = DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->where('sub_kegiatan_id', $sub->id)->exists();
                if (!$exists) {
                    $namaLengkap = ($sub->kegiatan->program->nama ?? '-') . ' / ' . ($sub->kegiatan->nama ?? '-') . ' / ' . ($sub->nama ?? '-');
                    DetailLaporanKonsolidasi::create([
                        'laporan_konsolidasi_id' => $this->laporan->id,
                        'sub_kegiatan_id' => $sub->id,
                        'kode' => $sub->kode ?? '00',
                        'nama_program_kegiatan' => $namaLengkap,
                        'pagu_anggaran' => $sub->pagu ?? 0,
                        'pagu_realisasi' => 0
                    ]);
                }
            }
            DB::commit();
            session()->flash('message', 'Program berhasil ditambahkan.');
            
            return redirect()->route('laporan-konsolidasi.input', $this->laporan->id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        $this->closeProgramModal();
        $this->loadData(); 
    }
}