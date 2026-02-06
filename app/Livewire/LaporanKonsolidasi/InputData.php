<?php

namespace App\Livewire\LaporanKonsolidasi;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use App\Models\LaporanKonsolidasi;
use App\Models\DetailLaporanKonsolidasi;
use App\Models\LaporanKonsolidasiAnggaran;
use App\Models\Program;
use App\Models\Kegiatan; 
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\DB;

class InputData extends Component
{
    use WithPagination;

    public $laporan;
    
    // Modal Program
    public $isOpenProgram = false;
    public $selectedProgramId;

    // Modal Kegiatan
    public $isOpenKegiatan = false;
    public $selectedKegiatanId;
    public $targetProgramId; 
    
    // Modal Edit Sub Kegiatan (BARU)
    public $isOpenEditSub = false;
    public $editDetailId;
    public $editSubKegiatanId;
    public $editSubOutput;
    public $editSatuan;
    public $subKegiatanOptions = [];

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
        $details = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan.program', 'subKegiatan.indikators'])
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

        // 2. Load Data PROGRAM
        $savedPrograms = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('program_id')
                            ->get();

        $this->programInputs = [];
        foreach($savedPrograms as $data) {
            $this->programInputs[$data->program_id] = [
                'pagu_anggaran' => $data->pagu_anggaran ? number_format($data->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $data->pagu_realisasi ? number_format($data->pagu_realisasi, 0, ',', '.') : ''
            ];
        }

        // 3. Load Data KEGIATAN
        $savedKegiatans = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('kegiatan_id')
                            ->get();

        $this->kegiatanInputs = [];
        foreach($savedKegiatans as $data) {
            $this->kegiatanInputs[$data->kegiatan_id] = [
                'pagu_anggaran' => $data->pagu_anggaran ? number_format($data->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi' => $data->pagu_realisasi ? number_format($data->pagu_realisasi, 0, ',', '.') : ''
            ];
        }
    }

    // --- MODAL PROGRAM ---
    public function openProgramModal() { $this->isOpenProgram = true; }
    public function closeProgramModal() { $this->isOpenProgram = false; $this->selectedProgramId = null; }
    
    public function addProgram()
    {
        $this->validate(['selectedProgramId' => 'required']);

        LaporanKonsolidasiAnggaran::updateOrCreate(
            [
                'laporan_konsolidasi_id' => $this->laporan->id,
                'program_id' => $this->selectedProgramId
            ],
            [
                'pagu_anggaran' => 0, 
                'pagu_realisasi' => 0
            ]
        );

        session()->flash('message', 'Program berhasil ditambahkan.');
        $this->loadData();
        $this->closeProgramModal();
    }

    // --- MODAL KEGIATAN ---
    public function createKegiatan($programId)
    {
        $this->targetProgramId = $programId;
        $this->selectedKegiatanId = null;
        $this->isOpenKegiatan = true;
    }

    public function closeKegiatanModal()
    {
        $this->isOpenKegiatan = false;
        $this->selectedKegiatanId = null;
        $this->targetProgramId = null;
    }

    // --- UPDATE TERBARU: OTOMATIS TARIK SUB KEGIATAN & INDIKATOR ---
    public function addKegiatan()
    {
        $this->validate(['selectedKegiatanId' => 'required']);

        DB::beginTransaction();

        try {
            // 1. Simpan Data KEGIATAN ke tabel Anggaran (Header)
            LaporanKonsolidasiAnggaran::updateOrCreate(
                [
                    'laporan_konsolidasi_id' => $this->laporan->id,
                    'kegiatan_id' => $this->selectedKegiatanId
                ],
                [
                    'pagu_anggaran' => 0,
                    'pagu_realisasi' => 0
                ]
            );

            // 2. TARIK OTOMATIS SUB KEGIATAN DARI MASTER DATA
            // Cari semua Sub Kegiatan milik Kegiatan yang dipilih
            $masterSubKegiatans = SubKegiatan::with(['indikators', 'kegiatan.program'])
                                    ->where('kegiatan_id', $this->selectedKegiatanId)
                                    ->get();

            foreach($masterSubKegiatans as $sub) {
                // Cek dulu apakah sub kegiatan ini sudah ada di laporan agar tidak duplikat
                $exists = DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->where('sub_kegiatan_id', $sub->id)
                            ->exists();

                if (!$exists) {
                    // Ambil Indikator untuk mengisi default Sub Output & Satuan
                    $listIndikator = $sub->indikators;
                    
                    // Gabung keterangan/satuan jika ada lebih dari 1 indikator, dipisah enter (\n)
                    $textOutput = $listIndikator->pluck('keterangan')->join("\n");
                    $textSatuan = $listIndikator->pluck('satuan')->join("\n");

                    // Jika kosong, beri tanda strip
                    $textOutput = $textOutput ?: '-';
                    $textSatuan = $textSatuan ?: '-';

                    // Buat Nama Lengkap untuk history/cache di tabel detail
                    $namaProgram = $sub->kegiatan->program->nama ?? $sub->kegiatan->program->nama_program ?? '-';
                    $namaKegiatan = $sub->kegiatan->nama ?? $sub->kegiatan->nama_kegiatan ?? '-';
                    $namaLengkap = $namaProgram . ' / ' . $namaKegiatan . ' / ' . $sub->nama;

                    DetailLaporanKonsolidasi::create([
                        'laporan_konsolidasi_id' => $this->laporan->id,
                        'sub_kegiatan_id'       => $sub->id,
                        'kode'                  => $sub->kode,
                        'nama_program_kegiatan' => $namaLengkap,
                        
                        // Isi otomatis dari Master Data Indikator
                        'sub_output'            => $textOutput, 
                        'satuan_unit'           => $textSatuan,
                        
                        'pagu_anggaran'         => 0,
                        'pagu_realisasi'        => 0
                    ]);
                }
            }

            DB::commit();
            session()->flash('message', 'Kegiatan dan seluruh Sub Kegiatan berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menambahkan: ' . $e->getMessage());
        }

        $this->loadData();
        $this->closeKegiatanModal();
    }

    // --- AKSI KEGIATAN LAINNYA ---
    public function createSubKegiatan($kegiatanId)
    {
        $this->dispatch('alert', ['type' => 'info', 'title' => 'Info', 'message' => 'Fitur Tambah Sub Kegiatan akan dibuat di tahap selanjutnya. ID Kegiatan: ' . $kegiatanId]);
    }

    public function editKegiatan($kegiatanId)
    {
        $this->dispatch('alert', ['type' => 'info', 'title' => 'Info', 'message' => 'Edit Kegiatan ID: ' . $kegiatanId]);
    }

    public function deleteKegiatan($kegiatanId)
    {
        DB::beginTransaction();
        try {
            LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                ->where('kegiatan_id', $kegiatanId)->delete();

            DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereHas('subKegiatan', function($q) use ($kegiatanId) {
                    $q->where('kegiatan_id', $kegiatanId);
                })->delete();

            DB::commit();
            $this->loadData(); 
            $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus', 'message' => 'Kegiatan berhasil dihapus.']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->getMessage()]);
        }
    }

    // --- LOGIKA EDIT & HAPUS SUB KEGIATAN (BARU) ---

    public function editSubKegiatan($detailId)
    {
        $detail = DetailLaporanKonsolidasi::with('subKegiatan')->find($detailId);
        
        if (!$detail) {
            $this->dispatch('alert', ['type' => 'error', 'title' => 'Error', 'message' => 'Data tidak ditemukan.']);
            return;
        }

        $this->editDetailId = $detail->id;
        $this->editSubKegiatanId = $detail->sub_kegiatan_id;
        $this->editSubOutput = $detail->sub_output;
        $this->editSatuan = $detail->satuan_unit;

        // Ambil daftar Sub Kegiatan lain yang SATU INDUK (Satu Kegiatan) agar user bisa ganti sub kegiatan
        if ($detail->subKegiatan) {
            $this->subKegiatanOptions = SubKegiatan::where('kegiatan_id', $detail->subKegiatan->kegiatan_id)->get();
        } else {
            $this->subKegiatanOptions = [];
        }

        $this->isOpenEditSub = true;
    }

    public function updateSubKegiatan()
    {
        $this->validate([
            'editSubKegiatanId' => 'required',
            'editSubOutput' => 'nullable|string',
            'editSatuan' => 'nullable|string',
        ]);

        $detail = DetailLaporanKonsolidasi::find($this->editDetailId);
        
        if ($detail) {
            $detail->update([
                'sub_kegiatan_id' => $this->editSubKegiatanId,
                'sub_output' => $this->editSubOutput,
                'satuan_unit' => $this->editSatuan
            ]);

            // Jika Sub Kegiatan berubah, kita update juga cache columns
            $newSub = SubKegiatan::find($this->editSubKegiatanId);
            if ($newSub) {
                $namaLengkap = ($newSub->kegiatan->program->nama ?? '-') . ' / ' . ($newSub->kegiatan->nama ?? '-') . ' / ' . $newSub->nama;
                $detail->update([
                    'kode' => $newSub->kode,
                    'nama_program_kegiatan' => $namaLengkap
                ]);
            }

            $this->dispatch('alert', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Sub Kegiatan diperbarui.']);
            $this->loadData(); 
            $this->closeEditSubModal();
        }
    }

    public function closeEditSubModal()
    {
        $this->isOpenEditSub = false;
        $this->editDetailId = null;
        $this->subKegiatanOptions = [];
    }

    public function deleteSubKegiatan($id)
    {
        DetailLaporanKonsolidasi::find($id)?->delete();
        unset($this->inputs[$id]); 
        $this->loadData();
        $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus', 'message' => 'Sub Kegiatan dihapus.']);
    }

    // --- DELETE DETAIL LAMA (Bisa dihapus jika sudah tidak dipakai, tapi saya biarkan untuk kompatibilitas view lama) ---
    public function deleteDetail($id) {
        $this->deleteSubKegiatan($id);
    }

    // --- SIMPAN SEMUA INPUT ---
    public function saveAll()
    {
        DB::beginTransaction();
        try {
            foreach ($this->programInputs as $progId => $data) {
                LaporanKonsolidasiAnggaran::updateOrCreate(
                    ['laporan_konsolidasi_id' => $this->laporan->id, 'program_id' => $progId],
                    ['pagu_anggaran' => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'), 'pagu_realisasi'=> $this->cleanRupiah($data['pagu_realisasi'] ?? '0')]
                );
            }

            foreach ($this->kegiatanInputs as $kegId => $data) {
                LaporanKonsolidasiAnggaran::updateOrCreate(
                    ['laporan_konsolidasi_id' => $this->laporan->id, 'kegiatan_id' => $kegId],
                    ['pagu_anggaran' => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'), 'pagu_realisasi'=> $this->cleanRupiah($data['pagu_realisasi'] ?? '0')]
                );
            }

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
            
            DB::commit();
            $this->loadData(); 
            session()->flash('message', 'Semua data berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function cleanRupiah($val) {
        $clean = preg_replace('/[^0-9]/', '', $val);
        return $clean === '' ? 0 : (float) $clean;
    }

    public function editProgram($programId) {
        $this->dispatch('alert', ['type' => 'info', 'title' => 'Info', 'message' => 'Edit Program ID: ' . $programId]);
    }

    public function deleteProgram($programId) {
        LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
            ->where('program_id', $programId)->delete();
        
        $kegiatanIds = Kegiatan::where('program_id', $programId)->pluck('id');
        LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
            ->whereIn('kegiatan_id', $kegiatanIds)->delete();
        
        $this->loadData();
    }

    public function render()
    {
        $programIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->whereNotNull('program_id')->pluck('program_id');
        $programsInReport = Program::whereIn('id', $programIds)->orderBy('kode', 'asc')->get();

        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan', 'subKegiatan.indikators'])
                        ->where('laporan_konsolidasi_id', $this->laporan->id)->get();

        $groupedData = [];
        foreach($programsInReport as $prog) {
            $groupedData[$prog->id] = ['program' => $prog, 'kegiatans' => []];
            
            $kegiatanIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('kegiatan_id')
                            ->whereHas('kegiatan', function($q) use ($prog) { $q->where('program_id', $prog->id); })
                            ->pluck('kegiatan_id');
            
            $kegiatans = Kegiatan::whereIn('id', $kegiatanIds)->orderBy('kode', 'asc')->get();

            foreach($kegiatans as $keg) {
                $subs = $detailsRaw->filter(function($item) use ($keg) {
                    return $item->subKegiatan?->kegiatan_id == $keg->id;
                });
                $groupedData[$prog->id]['kegiatans'][$keg->id] = ['kegiatan' => $keg, 'details' => $subs];
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = collect($groupedData);
        $currentItems = $col->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
        $paginatedData = new LengthAwarePaginator($currentItems, $col->count(), $this->perPage, $currentPage, ['path' => Request::url()]);

        $anggaranAll = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->get();
        $totalAnggaran = $anggaranAll->sum('pagu_anggaran') + $detailsRaw->sum('pagu_anggaran');
        $totalRealisasi = $anggaranAll->sum('pagu_realisasi') + $detailsRaw->sum('pagu_realisasi');

        $kegiatanOptions = [];
        if($this->targetProgramId) {
            $kegiatanOptions = Kegiatan::where('program_id', $this->targetProgramId)->get();
        }

        return view('livewire.laporan-konsolidasi.input-data', [
            'programs' => Program::all(), 
            'kegiatanOptions' => $kegiatanOptions, 
            'reportData' => $paginatedData,
            'totalAnggaran' => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi
        ]);
    }
}