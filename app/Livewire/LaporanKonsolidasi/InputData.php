<?php

namespace App\Livewire\LaporanKonsolidasi;

use Livewire\Component;
use App\Models\LaporanKonsolidasi;
use App\Models\DetailLaporanKonsolidasi;
use App\Models\Program;
use App\Models\Kegiatan; 
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\DB;

class InputData extends Component
{
    public $laporan;
    public $isOpenProgram = false;
    public $selectedProgramId;
    
    public $inputs = [];         // Untuk Detail (Sub Kegiatan)
    public $programInputs = [];  // Untuk Program (Anggaran & Realisasi)
    public $kegiatanInputs = []; // Untuk Kegiatan (Anggaran & Realisasi)

    public function mount($id)
    {
        $this->laporan = LaporanKonsolidasi::findOrFail($id);
        $this->loadData();
    }

    public function loadData()
    {
        // 1. Ambil data detail
        $details = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan.program'])
                    ->where('laporan_konsolidasi_id', $this->laporan->id)
                    ->get();

        // 2. Load ke $inputs
        $this->inputs = [];
        foreach ($details as $detail) {
            $this->inputs[$detail->id] = [
                'sub_output' => $detail->sub_output,
                'satuan_unit' => $detail->satuan_unit,
                'pagu_anggaran' => $detail->pagu_anggaran ? number_format($detail->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $detail->pagu_realisasi ? number_format($detail->pagu_realisasi, 0, ',', '.') : '',
            ];
        }

        // 3. Load Data PROGRAM (Anggaran + Realisasi)
        $programIds = $details->pluck('subKegiatan.kegiatan.program_id')->unique()->filter();
        $programs = Program::whereIn('id', $programIds)->get();
        
        $this->programInputs = [];
        foreach($programs as $prog) {
            $this->programInputs[$prog->id] = [
                'pagu_anggaran' => $prog->pagu_anggaran ? number_format($prog->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $prog->pagu_realisasi ? number_format($prog->pagu_realisasi, 0, ',', '.') : '' // BARU
            ];
        }

        // 4. Load Data KEGIATAN (Anggaran + Realisasi)
        $kegiatanIds = $details->pluck('subKegiatan.kegiatan_id')->unique()->filter();
        $kegiatans = Kegiatan::whereIn('id', $kegiatanIds)->get();

        $this->kegiatanInputs = [];
        foreach($kegiatans as $keg) {
            $this->kegiatanInputs[$keg->id] = [
                'pagu_anggaran' => $keg->pagu_anggaran ? number_format($keg->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $keg->pagu_realisasi ? number_format($keg->pagu_realisasi, 0, ',', '.') : '' // BARU
            ];
        }
    }

    public function saveAll()
    {
        DB::beginTransaction();
        try {
            // --- 1. Simpan Data Detail ---
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

            // --- 2. Simpan Data PROGRAM ---
            if (!empty($this->programInputs)) {
                foreach ($this->programInputs as $progId => $data) {
                    Program::where('id', $progId)->update([
                        'pagu_anggaran' => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'),
                        'pagu_realisasi'=> $this->cleanRupiah($data['pagu_realisasi'] ?? '0') // BARU
                    ]);
                }
            }

            // --- 3. Simpan Data KEGIATAN ---
            if (!empty($this->kegiatanInputs)) {
                foreach ($this->kegiatanInputs as $kegId => $data) {
                    Kegiatan::where('id', $kegId)->update([
                        'pagu_anggaran' => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'),
                        'pagu_realisasi'=> $this->cleanRupiah($data['pagu_realisasi'] ?? '0') // BARU
                    ]);
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

    // Helper kecil untuk bersihkan rupiah
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
        $programs = Program::all(); 

        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan.program'])
                        ->where('laporan_konsolidasi_id', $this->laporan->id)
                        ->get();

        $programIds = $detailsRaw->pluck('subKegiatan.kegiatan.program_id')->unique()->filter()->toArray();
        $kegiatanIds = $detailsRaw->pluck('subKegiatan.kegiatan_id')->unique()->filter()->toArray();

        // 1. HITUNG TOTAL ANGGARAN (Prog + Keg + Sub)
        $totalAnggaran = Program::whereIn('id', $programIds)->sum('pagu_anggaran')
                       + Kegiatan::whereIn('id', $kegiatanIds)->sum('pagu_anggaran')
                       + $detailsRaw->sum('pagu_anggaran');
        
        // 2. HITUNG TOTAL REALISASI (Prog + Keg + Sub) -- BARU --
        $totalRealisasi = Program::whereIn('id', $programIds)->sum('pagu_realisasi')
                        + Kegiatan::whereIn('id', $kegiatanIds)->sum('pagu_realisasi')
                        + $detailsRaw->sum('pagu_realisasi');

        // Logic Sorting & Grouping
        $detailsSorted = $detailsRaw->sortBy(function($detail) {
            $progKode = $detail->subKegiatan->kegiatan->program->kode ?? '99';
            $kegKode  = $detail->subKegiatan->kegiatan->kode ?? '99';
            $subKode  = $detail->subKegiatan->kode ?? '99';
            return $progKode . '.' . $kegKode . '.' . $subKode;
        });

        $groupedData = $detailsSorted->groupBy(function($item) {
            return $item->subKegiatan->kegiatan->program_id ?? 0;
        })->map(function($programGroup) {
            return $programGroup->groupBy(function($item) {
                return $item->subKegiatan->kegiatan_id ?? 0;
            });
        });

        return view('livewire.laporan-konsolidasi.input-data', [
            'programs' => $programs,
            'groupedData' => $groupedData,
            'totalAnggaran' => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi
        ]);
    }

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
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        $this->closeProgramModal();
        $this->loadData(); 
    }
}