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
    public $isOpenProgram = false;
    public $selectedProgramId;
    public $isOpenKegiatan = false;
    public $selectedKegiatanId;
    public $targetProgramId; 
    
    // Modal Edit Sub Kegiatan
    public $isOpenEditSub = false;
    public $editDetailId;
    public $editSubKegiatanId;
    public $editSubOutput;
    public $editSatuan;
    public $subKegiatanOptions = [];

    public $perPage = 10;
    
    // Array untuk menampung inputan
    public $inputs = [];         // Untuk Sub Kegiatan
    public $programInputs = [];  // Untuk Program
    public $kegiatanInputs = []; // Untuk Kegiatan

    public function mount($id)
    {
        $this->laporan = LaporanKonsolidasi::findOrFail($id);
        $this->loadData();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // --- FUNGSI LOAD DATA (Agar data muncul kembali setelah simpan) ---
    public function loadData()
    {
        // 1. Load Detail (Sub Kegiatan)
        $details = DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)->get();

        $this->inputs = [];
        foreach ($details as $detail) {
            $this->inputs[$detail->id] = [
                'sub_output'      => $detail->sub_output,
                'satuan_unit'     => $detail->satuan_unit,
                'target'          => $this->formatNumberDisplay($detail->target),
                'realisasi_fisik' => $this->formatNumberDisplay($detail->realisasi_fisik),
                'pagu_anggaran'   => $detail->pagu_anggaran ? number_format($detail->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi'  => $detail->pagu_realisasi ? number_format($detail->pagu_realisasi, 0, ',', '.') : '',
            ];
        }

        // 2. Load Data PROGRAM & KEGIATAN dari tabel Anggaran
        $anggarans = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->get();

        $this->programInputs = [];
        $this->kegiatanInputs = [];

        foreach($anggarans as $data) {
            $formattedData = [
                'target'          => $this->formatNumberDisplay($data->target),
                'realisasi_fisik' => $this->formatNumberDisplay($data->realisasi_fisik),
                'pagu_anggaran'   => $data->pagu_anggaran ? number_format($data->pagu_anggaran, 0, ',', '.') : '',
                'pagu_realisasi'  => $data->pagu_realisasi ? number_format($data->pagu_realisasi, 0, ',', '.') : ''
            ];

            if ($data->program_id) {
                $this->programInputs[$data->program_id] = $formattedData;
            } elseif ($data->kegiatan_id) {
                $this->kegiatanInputs[$data->kegiatan_id] = $formattedData;
            }
        }
    }

    // Helper untuk tampilan (menghilangkan .00 jika bulat, dan kosong jika 0)
    private function formatNumberDisplay($val) {
        if (!$val || $val == 0) return '';
        return (float)$val == (int)$val ? (int)$val : str_replace('.', ',', $val);
    }

    // --- FUNGSI PEMBERSIH FORMAT RUPIAH ---
    private function cleanRupiah($val) {
        if (is_null($val) || $val === '') return 0;
        $clean = preg_replace('/[^0-9]/', '', $val);
        return (float) $clean;
    }

    // --- FUNGSI PEMBERSIH ANGKA BIASA (TARGET/FISIK) ---
    private function cleanNumber($val) {
        if (is_null($val) || $val === '') return 0;
        $val = str_replace('.', '', $val);
        $val = str_replace(',', '.', $val);
        return (float) $val; 
    }

    public function toggleVerification($id, $type)
    {
        // Nanti di sini kita tambahkan pengecekan Role User Keuangan
        // if (auth()->user()->role !== 'keuangan') { abort(403); }

        if ($type === 'program' || $type === 'kegiatan') {
            // Cari data di tabel Anggaran berdasarkan Program/Kegiatan ID
            // Catatan: Karena struktur tabel anggaran pakai program_id/kegiatan_id, kita cari record spesifiknya
            
            $column = $type === 'program' ? 'program_id' : 'kegiatan_id';
            
            $data = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                        ->where($column, $id)
                        ->first();
            
            if ($data) {
                $data->update(['is_verified' => !$data->is_verified]);
            }

        } elseif ($type === 'sub_kegiatan') {
            // Cari data di tabel Detail
            $data = DetailLaporanKonsolidasi::find($id);
            if ($data) {
                $data->update(['is_verified' => !$data->is_verified]);
            }
        }
        
        $this->loadData(); // Refresh tampilan
        $this->dispatch('alert', ['type' => 'success', 'title' => 'Sukses', 'message' => 'Status verifikasi diperbarui.']);
    }

    // --- FITUR SINKRONISASI DATA (BARU & UPDATE LOGIC) ---
    public function syncData()
    {
        DB::beginTransaction();
        try {
            // 1. Ambil semua ID Program yang SUDAH ADA di laporan ini
            $existingProgramIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereNotNull('program_id')
                ->pluck('program_id');

            if ($existingProgramIds->isEmpty()) {
                $this->dispatch('alert', ['type' => 'warning', 'title' => 'Perhatian', 'message' => 'Belum ada program yang ditambahkan. Silakan tambah program dulu.']);
                return;
            }

            // 2. Ambil Data Master terbaru berdasarkan Program yg ada
            $masterPrograms = Program::with(['kegiatans.subKegiatans.indikators'])
                ->whereIn('id', $existingProgramIds)
                ->get();

            $countAdded = 0;

            foreach ($masterPrograms as $prog) {
                // Update Pagu/Target Program jika di Master berubah (Opsional, tapi direkomendasikan agar sinkron)
                /* LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                    ->where('program_id', $prog->id)
                    ->update([
                        'pagu_anggaran' => $prog->pagu ?? 0,
                        'target' => $prog->target ?? 0
                    ]); 
                */

                // Loop Kegiatan di Master
                foreach ($prog->kegiatans as $keg) {
                    
                    // Cek & Update/Create Kegiatan
                    LaporanKonsolidasiAnggaran::updateOrCreate(
                        [
                            'laporan_konsolidasi_id' => $this->laporan->id,
                            'kegiatan_id' => $keg->id
                        ],
                        [
                            // Jika updateOrCreate: data lama akan tertimpa dengan data Master
                            // Kita ingin Pagu & Target sinkron, tapi Realisasi tetap aman (0 jika baru)
                            'pagu_anggaran' => $keg->pagu ?? 0, 
                            'target' => $keg->target ?? 0,
                            // 'pagu_realisasi' => 0, // JANGAN DI-RESET jika sudah ada isinya! Biarkan database default
                        ]
                    );

                    // Loop Sub Kegiatan di Master
                    foreach ($keg->subKegiatans as $sub) {
                        $detail = DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                                    ->where('sub_kegiatan_id', $sub->id)
                                    ->first();

                        if (!$detail) {
                            // JIKA BELUM ADA (Data baru di Master), MAKA BUAT
                            $listIndikator = $sub->indikators;
                            $textOutput = $listIndikator->pluck('keterangan')->join("\n") ?: '-';
                            $textSatuan = $listIndikator->pluck('satuan')->join("\n") ?: '-';
                            
                            $namaProgram = $prog->nama ?? $prog->nama_program ?? '-';
                            $namaKegiatan = $keg->nama ?? $keg->nama_kegiatan ?? '-';
                            $namaLengkap = $namaProgram . ' / ' . $namaKegiatan . ' / ' . $sub->nama;

                            DetailLaporanKonsolidasi::create([
                                'laporan_konsolidasi_id' => $this->laporan->id,
                                'sub_kegiatan_id'       => $sub->id,
                                'kode'                  => $sub->kode,
                                'nama_program_kegiatan' => $namaLengkap,
                                'sub_output'            => $textOutput, 
                                'satuan_unit'           => $textSatuan,
                                
                                // AMBIL DARI MASTER
                                'pagu_anggaran'         => $sub->pagu ?? 0,
                                'target'                => $sub->target ?? 0,
                                
                                'realisasi_fisik'       => 0,
                                'pagu_realisasi'        => 0
                            ]);
                            $countAdded++;
                        } else {
                            // JIKA SUDAH ADA, UPDATE PAGU & TARGET DARI MASTER (Agar Sinkron)
                            $detail->update([
                                'pagu_anggaran' => $sub->pagu ?? 0,
                                'target'        => $sub->target ?? 0
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            $this->loadData(); // Reload data agar tampilan update
            
            if ($countAdded > 0) {
                $this->dispatch('alert', ['type' => 'success', 'title' => 'Sukses', 'message' => "Sinkronisasi selesai. $countAdded data baru ditambahkan."]);
            } else {
                $this->dispatch('alert', ['type' => 'info', 'title' => 'Info', 'message' => 'Data berhasil disinkronkan dengan Master.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->getMessage()]);
        }
    }

    // --- LOGIKA SIMPAN SEMUA ---
    public function saveAll()
    {
        DB::beginTransaction();
        try {
            // 1. Simpan Program Inputs
            foreach ($this->programInputs as $progId => $data) {
                LaporanKonsolidasiAnggaran::updateOrCreate(
                    [
                        'laporan_konsolidasi_id' => $this->laporan->id, 
                        'program_id' => $progId
                    ],
                    [
                        'target'          => $this->cleanNumber($data['target'] ?? 0),
                        'realisasi_fisik' => $this->cleanNumber($data['realisasi_fisik'] ?? 0),
                        'pagu_anggaran'   => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'), 
                        'pagu_realisasi'  => $this->cleanRupiah($data['pagu_realisasi'] ?? '0')
                    ]
                );
            }

            // 2. Simpan Kegiatan Inputs
            foreach ($this->kegiatanInputs as $kegId => $data) {
                LaporanKonsolidasiAnggaran::updateOrCreate(
                    [
                        'laporan_konsolidasi_id' => $this->laporan->id, 
                        'kegiatan_id' => $kegId
                    ],
                    [
                        'target'          => $this->cleanNumber($data['target'] ?? 0),
                        'realisasi_fisik' => $this->cleanNumber($data['realisasi_fisik'] ?? 0),
                        'pagu_anggaran'   => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'), 
                        'pagu_realisasi'  => $this->cleanRupiah($data['pagu_realisasi'] ?? '0')
                    ]
                );
            }

            // 3. Simpan Detail Sub Kegiatan Inputs
            foreach ($this->inputs as $detailId => $data) {
                $detail = DetailLaporanKonsolidasi::find($detailId);
                if ($detail) {
                    $detail->update([
                        'sub_output'     => $data['sub_output'] ?? null,
                        'satuan_unit'    => $data['satuan_unit'] ?? null,
                        'target'         => $this->cleanNumber($data['target'] ?? 0),
                        'realisasi_fisik'=> $this->cleanNumber($data['realisasi_fisik'] ?? 0),
                        'pagu_anggaran'  => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'),
                        'pagu_realisasi' => $this->cleanRupiah($data['pagu_realisasi'] ?? '0'),
                    ]);
                }
            }
            
            DB::commit();
            
            // Reload data agar inputan menampilkan format angka yang benar kembali
            $this->loadData(); 
            
            session()->flash('message', 'Semua data berhasil disimpan dan dikalkulasi.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // --- MODAL & AKSI LAINNYA ---
    public function openProgramModal() { $this->isOpenProgram = true; }
    public function closeProgramModal() { $this->isOpenProgram = false; $this->selectedProgramId = null; }
    
    // === UPDATE: ADD PROGRAM (AMBIL DARI MASTER) ===
    public function addProgram()
    {
        $this->validate(['selectedProgramId' => 'required']);
        DB::beginTransaction();
        try {
            // Ambil Master Program
            $progMaster = Program::find($this->selectedProgramId);

            LaporanKonsolidasiAnggaran::updateOrCreate(
                ['laporan_konsolidasi_id' => $this->laporan->id, 'program_id' => $this->selectedProgramId],
                [
                    'pagu_anggaran' => $progMaster->pagu ?? 0,   // AMBIL MASTER
                    'target'        => $progMaster->target ?? 0, // AMBIL MASTER
                    'pagu_realisasi' => 0, 
                    'realisasi_fisik' => 0
                ]
            );

            $kegiatans = Kegiatan::with(['program'])->where('program_id', $this->selectedProgramId)->get();
            foreach ($kegiatans as $keg) {
                LaporanKonsolidasiAnggaran::updateOrCreate(
                    ['laporan_konsolidasi_id' => $this->laporan->id, 'kegiatan_id' => $keg->id],
                    [
                        'pagu_anggaran' => $keg->pagu ?? 0,   // AMBIL MASTER
                        'target'        => $keg->target ?? 0, // AMBIL MASTER
                        'pagu_realisasi' => 0, 
                        'realisasi_fisik' => 0
                    ]
                );

                $subKegiatans = SubKegiatan::with(['indikators'])->where('kegiatan_id', $keg->id)->get();
                foreach ($subKegiatans as $sub) {
                    $exists = DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                                ->where('sub_kegiatan_id', $sub->id)->exists();
                    if (!$exists) {
                        $listIndikator = $sub->indikators;
                        $textOutput = $listIndikator->pluck('keterangan')->join("\n") ?: '-';
                        $textSatuan = $listIndikator->pluck('satuan')->join("\n") ?: '-';
                        $namaProgram = $keg->program->nama ?? $keg->program->nama_program ?? '-';
                        $namaKegiatan = $keg->nama ?? $keg->nama_kegiatan ?? '-';
                        $namaLengkap = $namaProgram . ' / ' . $namaKegiatan . ' / ' . $sub->nama;

                        DetailLaporanKonsolidasi::create([
                            'laporan_konsolidasi_id' => $this->laporan->id,
                            'sub_kegiatan_id'       => $sub->id,
                            'kode'                  => $sub->kode,
                            'nama_program_kegiatan' => $namaLengkap,
                            'sub_output'            => $textOutput, 
                            'satuan_unit'           => $textSatuan,
                            
                            'pagu_anggaran'         => $sub->pagu ?? 0,   // AMBIL MASTER
                            'target'                => $sub->target ?? 0, // AMBIL MASTER
                            
                            'realisasi_fisik'       => 0,
                            'pagu_realisasi'        => 0
                        ]);
                    }
                }
            }
            DB::commit();
            session()->flash('message', 'Program ditambahkan (Data Pagu & Target diambil dari Master).');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
        $this->loadData();
        $this->closeProgramModal();
    }

    public function deleteProgram($programId) {
        DB::beginTransaction();
        try {
            LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->where('program_id', $programId)->delete();
            $kegiatanIds = Kegiatan::where('program_id', $programId)->pluck('id');
            LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->whereIn('kegiatan_id', $kegiatanIds)->delete();
            DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereHas('subKegiatan', function($q) use ($kegiatanIds) { $q->whereIn('kegiatan_id', $kegiatanIds); })->delete();
            DB::commit();
            $this->loadData();
            $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus', 'message' => 'Program dihapus.']);
        } catch(\Exception $e) {
            DB::rollBack();
        }
    }

    public function deleteKegiatan($kegiatanId) {
        DB::beginTransaction();
        try {
            LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->where('kegiatan_id', $kegiatanId)->delete();
            DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereHas('subKegiatan', function($q) use ($kegiatanId) { $q->where('kegiatan_id', $kegiatanId); })->delete();
            DB::commit();
            $this->loadData(); 
            $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus', 'message' => 'Kegiatan dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function deleteSubKegiatan($id) {
        DetailLaporanKonsolidasi::find($id)?->delete();
        unset($this->inputs[$id]); 
        $this->loadData();
        $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus', 'message' => 'Sub Kegiatan dihapus.']);
    }

    // Modal dummy methods
    public function editProgram($id) {}
    public function createKegiatan($id) {}
    public function editKegiatan($id) {}
    public function editSubKegiatan($id) {}
    public function closeEditSubModal() {}
    public function updateSubKegiatan() {}
    public function createSubKegiatan($id) {}
    public function addKegiatan() {}
    public function closeKegiatanModal() {}

    public function render()
    {
        $programIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                        ->whereNotNull('program_id')
                        ->pluck('program_id');
        
        $programsInReport = Program::whereIn('id', $programIds)->orderBy('kode', 'asc')->get();

        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan', 'subKegiatan.indikators'])
                        ->where('laporan_konsolidasi_id', $this->laporan->id)->get();

        $groupedData = [];
        foreach($programsInReport as $prog) {
            $groupedData[$prog->id] = ['program' => $prog, 'kegiatans' => []];
            
            $kegiatanIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('kegiatan_id')
                            ->whereHas('kegiatan', function($q) use ($prog) { 
                                $q->where('program_id', $prog->id); 
                            })
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

        // HITUNG TOTAL ANGGARAN & REALISASI (FIX TRIPLE COUNTING: HANYA PROGRAM)
        // Kita hanya menjumlahkan row yang merupakan PROGRAM agar tidak dobel hitung
        $totalAnggaran = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('program_id')
                            ->sum('pagu_anggaran');

        $totalRealisasi = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->whereNotNull('program_id')
                            ->sum('pagu_realisasi');

        return view('livewire.laporan-konsolidasi.input-data', [
            'programs' => Program::all(), 
            'kegiatanOptions' => [], 
            'reportData' => $paginatedData,
            'totalAnggaran' => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi
        ]);
    }
}