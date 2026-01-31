<?php

namespace App\Livewire\LaporanKonsolidasi;

use Livewire\Component;
use App\Models\LaporanKonsolidasi;
use App\Models\DetailLaporanKonsolidasi;
use App\Models\Program;
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\DB;

class InputData extends Component
{
    public $laporan;
    public $isOpenProgram = false;
    public $selectedProgramId;
    public $inputs = []; 

    public function mount($id)
    {
        $this->laporan = LaporanKonsolidasi::findOrFail($id);
        $this->loadData();
    }

    public function loadData()
    {
        // Ambil data detail dengan relasi lengkap
        $details = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan.program'])
                    ->where('laporan_konsolidasi_id', $this->laporan->id)
                    ->get();

        // Reset array inputs
        $this->inputs = [];

        foreach ($details as $detail) {
            $this->inputs[$detail->id] = [
                'sub_output' => $detail->sub_output,
                'satuan_unit' => $detail->satuan_unit,
                // Format angka dari DB ke tampilan "1.000.000" (String)
                'pagu_anggaran' => $detail->pagu_anggaran ? number_format($detail->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $detail->pagu_realisasi ? number_format($detail->pagu_realisasi, 0, ',', '.') : '',
            ];
        }
    }

    public function saveAll()
    {
        DB::beginTransaction();
        try {
            // Pastikan array inputs tidak kosong atau error
            if (empty($this->inputs)) {
                // Jika kosong, mungkin reload halaman diperlukan atau tidak ada data yang diubah
                session()->flash('error', 'Tidak ada data untuk disimpan.');
                return;
            }

            foreach ($this->inputs as $detailId => $data) {
                $detail = DetailLaporanKonsolidasi::find($detailId);
                
                if ($detail) {
                    // Ambil data, default ke '0' jika kosong
                    $rawAnggaran = isset($data['pagu_anggaran']) && $data['pagu_anggaran'] !== '' ? $data['pagu_anggaran'] : '0';
                    $rawRealisasi = isset($data['pagu_realisasi']) && $data['pagu_realisasi'] !== '' ? $data['pagu_realisasi'] : '0';

                    // Bersihkan format Rupiah (hanya ambil angka)
                    // Contoh: "Rp 1.000.000" -> "1000000"
                    // Contoh: "1000000" -> "1000000"
                    $anggaranClean = preg_replace('/[^0-9]/', '', $rawAnggaran);
                    $realisasiClean = preg_replace('/[^0-9]/', '', $rawRealisasi);

                    // Konversi ke float, pastikan valid
                    $anggaranVal = $anggaranClean === '' ? 0 : (float) $anggaranClean;
                    $realisasiVal = $realisasiClean === '' ? 0 : (float) $realisasiClean;

                    $detail->update([
                        'sub_output'    => $data['sub_output'] ?? null,
                        'satuan_unit'   => $data['satuan_unit'] ?? null,
                        'pagu_anggaran' => $anggaranVal,
                        'pagu_realisasi'=> $realisasiVal,
                    ]);
                }
            }
            
            DB::commit();
            
            $this->loadData(); 
            
            session()->flash('message', 'Data berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function deleteDetail($id)
    {
        DetailLaporanKonsolidasi::find($id)->delete();
        // Jangan lupa hapus key dari array inputs agar tidak error di view
        unset($this->inputs[$id]);
        
        // Panggil loadData agar grouping ulang dan total terupdate
        $this->loadData(); 
    }

    public function render()
    {
        $programs = Program::all(); 

        // Query data untuk ditampilkan (perlu di-query ulang agar total update real-time)
        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan.program'])
                        ->where('laporan_konsolidasi_id', $this->laporan->id)
                        ->get();

        // Hitung total (ambil data mentah dari DB)
        $totalAnggaran = $detailsRaw->sum('pagu_anggaran');
        $totalRealisasi = $detailsRaw->sum('pagu_realisasi');

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

    // --- MODAL LOGIC (TETAP SAMA) ---
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