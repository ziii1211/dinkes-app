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
use App\Models\Jabatan; // Tambahan Baru
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

    // --- TAMBAHAN BARU: MODAL CETAK & TOTAL ---
    public $totalAnggaran = 0;
    public $totalRealisasi = 0;

    public $isOpenPrintModal = false;
    public $selectedJabatanPrint = '';

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

        foreach ($anggarans as $data) {
            $formattedData = [
                'target'          => $this->formatNumberDisplay($data->target),
                // Tampilkan rata-rata fisik yang sudah tersimpan
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
    private function formatNumberDisplay($val)
    {
        if (!$val && $val !== 0) return '';
        if ($val == 0) return '0';
        // Tampilkan desimal maksimal 2 digit jika ada koma
        return (float)$val == (int)$val ? (int)$val : str_replace('.', ',', round($val, 2));
    }

    // --- FUNGSI PEMBERSIH FORMAT RUPIAH ---
    private function cleanRupiah($val)
    {
        if (is_null($val) || $val === '') return 0;
        $clean = preg_replace('/[^0-9]/', '', $val);
        return (float) $clean;
    }

    // --- FUNGSI PEMBERSIH ANGKA BIASA (TARGET/FISIK) ---
    private function cleanNumber($val)
    {
        if (is_null($val) || $val === '') return 0;
        $val = str_replace('.', '', $val);
        $val = str_replace(',', '.', $val);
        return (float) $val;
    }

    // --- FUNGSI HITUNG OTOMATIS (UPDATED: FISIK RATA-RATA) ---
    private function recalculateProgramKegiatanAnggaran()
    {
        // Ambil semua detail sub kegiatan pada laporan ini
        $details = DetailLaporanKonsolidasi::with('subKegiatan.kegiatan')
            ->where('laporan_konsolidasi_id', $this->laporan->id)
            ->get();

        $kegiatanStats = [];
        $programStats = [];

        // Loop untuk menghitung total dan rata-rata
        foreach ($details as $detail) {
            $sub = $detail->subKegiatan;
            if ($sub && $sub->kegiatan) {
                $kegId = $sub->kegiatan_id;
                $progId = $sub->kegiatan->program_id;

                // Inisialisasi Array jika belum ada
                if (!isset($kegiatanStats[$kegId])) {
                    $kegiatanStats[$kegId] = [
                        'pagu' => 0, 
                        'realisasi' => 0,
                        'total_persen_fisik' => 0,
                        'count_sub' => 0
                    ];
                }
                if (!isset($programStats[$progId])) {
                    $programStats[$progId] = [
                        'pagu' => 0, 
                        'realisasi' => 0,
                        'total_persen_fisik' => 0,
                        'count_sub' => 0
                    ];
                }

                // 1. Hitung Keuangan (SUM)
                $kegiatanStats[$kegId]['pagu'] += $detail->pagu_anggaran;
                $kegiatanStats[$kegId]['realisasi'] += $detail->pagu_realisasi;

                $programStats[$progId]['pagu'] += $detail->pagu_anggaran;
                $programStats[$progId]['realisasi'] += $detail->pagu_realisasi;

                // 2. Hitung Persentase Fisik Sub Kegiatan Ini
                $targetSub = $detail->target > 0 ? $detail->target : 0;
                $fisikSub = $detail->realisasi_fisik > 0 ? $detail->realisasi_fisik : 0;
                
                $persenFisik = 0;
                if ($targetSub > 0) {
                    $persenFisik = ($fisikSub / $targetSub) * 100;
                    // Batasi maksimal 100% (opsional, jika ingin > 100 hapus baris ini)
                    $persenFisik = min($persenFisik, 100); 
                }

                // Akumulasi Persen Fisik untuk Rata-rata
                $kegiatanStats[$kegId]['total_persen_fisik'] += $persenFisik;
                $kegiatanStats[$kegId]['count_sub']++;

                $programStats[$progId]['total_persen_fisik'] += $persenFisik;
                $programStats[$progId]['count_sub']++;
            }
        }

        // Update otomatis ke tabel Anggaran untuk Kegiatan
        foreach ($kegiatanStats as $kegId => $stats) {
            // Hitung Rata-rata Fisik
            $avgFisik = $stats['count_sub'] > 0 ? ($stats['total_persen_fisik'] / $stats['count_sub']) : 0;

            LaporanKonsolidasiAnggaran::updateOrCreate(
                ['laporan_konsolidasi_id' => $this->laporan->id, 'kegiatan_id' => $kegId],
                [
                    'pagu_anggaran' => $stats['pagu'],
                    'pagu_realisasi' => $stats['realisasi'],
                    'realisasi_fisik' => $avgFisik, // Simpan Rata-rata %
                    'target' => 100 // Target parent dianggap 100% untuk referensi
                ]
            );
        }

        // Update otomatis ke tabel Anggaran untuk Program
        foreach ($programStats as $progId => $stats) {
            // Hitung Rata-rata Fisik
            $avgFisik = $stats['count_sub'] > 0 ? ($stats['total_persen_fisik'] / $stats['count_sub']) : 0;

            LaporanKonsolidasiAnggaran::updateOrCreate(
                ['laporan_konsolidasi_id' => $this->laporan->id, 'program_id' => $progId],
                [
                    'pagu_anggaran' => $stats['pagu'],
                    'pagu_realisasi' => $stats['realisasi'],
                    'realisasi_fisik' => $avgFisik, // Simpan Rata-rata %
                    'target' => 100 // Target parent dianggap 100% untuk referensi
                ]
            );
        }
    }

    public function toggleVerification($id, $type)
    {
        if ($type === 'program' || $type === 'kegiatan') {
            $column = $type === 'program' ? 'program_id' : 'kegiatan_id';

            $data = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                ->where($column, $id)
                ->first();

            if ($data) {
                $data->update(['is_verified' => !$data->is_verified]);
            }
        } elseif ($type === 'sub_kegiatan') {
            $data = DetailLaporanKonsolidasi::find($id);
            if ($data) {
                $data->update(['is_verified' => !$data->is_verified]);
            }
        }

        $this->loadData();
        $this->dispatch('alert', ['type' => 'success', 'title' => 'Sukses', 'message' => 'Status verifikasi diperbarui.']);
    }

    // --- FITUR SINKRONISASI DATA (UPDATED) ---
    public function syncData()
    {
        DB::beginTransaction();
        try {
            $existingProgramIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereNotNull('program_id')
                ->pluck('program_id');

            if ($existingProgramIds->isEmpty()) {
                $this->dispatch('alert', ['type' => 'warning', 'title' => 'Perhatian', 'message' => 'Belum ada program yang ditambahkan. Silakan tambah program dulu.']);
                return;
            }

            $masterPrograms = Program::with(['kegiatans.subKegiatans.indikators'])
                ->whereIn('id', $existingProgramIds)
                ->get();

            $countAdded = 0;
            $countUpdated = 0;

            foreach ($masterPrograms as $prog) {
                // Buat/Update Program (Placeholder)
                LaporanKonsolidasiAnggaran::firstOrCreate(
                    ['laporan_konsolidasi_id' => $this->laporan->id, 'program_id' => $prog->id]
                );

                foreach ($prog->kegiatans as $keg) {
                    // Buat/Update Kegiatan (Placeholder)
                    LaporanKonsolidasiAnggaran::firstOrCreate(
                        ['laporan_konsolidasi_id' => $this->laporan->id, 'kegiatan_id' => $keg->id]
                    );

                    foreach ($keg->subKegiatans as $sub) {
                        $listIndikator = $sub->indikators;
                        $textOutput = $listIndikator->pluck('keterangan')->join("\n") ?: '-';
                        $textSatuan = $listIndikator->pluck('satuan')->join("\n") ?: '-';

                        $namaProgram = $prog->nama ?? $prog->nama_program ?? '-';
                        $namaKegiatan = $keg->nama ?? $keg->nama_kegiatan ?? '-';
                        $namaLengkap = $namaProgram . ' / ' . $namaKegiatan . ' / ' . $sub->nama;

                        $detail = DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                            ->where('sub_kegiatan_id', $sub->id)
                            ->first();

                        if (!$detail) {
                            DetailLaporanKonsolidasi::create([
                                'laporan_konsolidasi_id' => $this->laporan->id,
                                'sub_kegiatan_id'       => $sub->id,
                                'kode'                  => $sub->kode,
                                'nama_program_kegiatan' => $namaLengkap,
                                'sub_output'            => $textOutput,
                                'satuan_unit'           => $textSatuan,
                                'pagu_anggaran'         => $sub->pagu ?? 0,
                                'target'                => $sub->target ?? 0,
                                'realisasi_fisik'       => 0,
                                'pagu_realisasi'        => 0
                            ]);
                            $countAdded++;
                        } else {
                            // Hanya update data master, jangan timpa realisasi inputan user
                            $detail->update([
                                'kode'                  => $sub->kode,
                                'nama_program_kegiatan' => $namaLengkap,
                                'sub_output'            => $textOutput,
                                'satuan_unit'           => $textSatuan,
                                'pagu_anggaran'         => $sub->pagu ?? 0,
                                'target'                => $sub->target ?? 0
                            ]);
                            $countUpdated++;
                        }
                    }
                }
            }

            // Hitung otomatis semua (termasuk fisik) sebelum commit
            $this->recalculateProgramKegiatanAnggaran();

            DB::commit();
            $this->loadData();

            $message = "Sinkronisasi selesai. ";
            if ($countAdded > 0) $message .= "$countAdded data baru. ";
            if ($countUpdated > 0) $message .= "$countUpdated data diperbarui.";

            if ($countAdded == 0 && $countUpdated == 0) {
                $this->dispatch('alert', ['type' => 'info', 'title' => 'Info', 'message' => 'Data sudah sinkron dengan Master.']);
            } else {
                $this->dispatch('alert', ['type' => 'success', 'title' => 'Sukses', 'message' => $message]);
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
            // 1. Simpan Detail Sub Kegiatan Inputs TERLEBIH DAHULU
            foreach ($this->inputs as $detailId => $data) {
                $detail = DetailLaporanKonsolidasi::find($detailId);
                if ($detail) {
                    $detail->update([
                        'sub_output'     => $data['sub_output'] ?? null,
                        'satuan_unit'    => $data['satuan_unit'] ?? null,
                        'target'         => $this->cleanNumber($data['target'] ?? 0),
                        'realisasi_fisik' => $this->cleanNumber($data['realisasi_fisik'] ?? 0),
                        'pagu_anggaran'  => $this->cleanRupiah($data['pagu_anggaran'] ?? '0'),
                        'pagu_realisasi' => $this->cleanRupiah($data['pagu_realisasi'] ?? '0'),
                    ]);
                }
            }

            // 2. HITUNG OTOMATIS PAGU & FISIK PROGRAM DAN KEGIATAN
            $this->recalculateProgramKegiatanAnggaran();

            // 3. Simpan Program/Kegiatan (Optional, karena sudah dihandle di recalculate, tapi kita simpan targetnya saja)
            foreach ($this->programInputs as $progId => $data) {
                // Hanya update target manual jika ada, sisanya otomatis
                LaporanKonsolidasiAnggaran::where([
                    'laporan_konsolidasi_id' => $this->laporan->id,
                    'program_id' => $progId
                ])->update([
                    'target' => $this->cleanNumber($data['target'] ?? 0)
                ]);
            }

            foreach ($this->kegiatanInputs as $kegId => $data) {
                LaporanKonsolidasiAnggaran::where([
                    'laporan_konsolidasi_id' => $this->laporan->id,
                    'kegiatan_id' => $kegId
                ])->update([
                    'target' => $this->cleanNumber($data['target'] ?? 0)
                ]);
            }

            DB::commit();

            // Reload data agar inputan menampilkan format angka yang benar kembali
            $this->loadData();

            session()->flash('message', 'Semua data berhasil disimpan dan dikalkulasi secara otomatis.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // --- MODAL & AKSI LAINNYA ---
    public function openProgramModal()
    {
        $this->isOpenProgram = true;
    }
    public function closeProgramModal()
    {
        $this->isOpenProgram = false;
        $this->selectedProgramId = null;
    }

    // --- TAMBAHAN BARU: ACTION MODAL CETAK ---
    public function openPrintModal()
    {
        $this->isOpenPrintModal = true;
        $this->selectedJabatanPrint = '';
    }

    public function closePrintModal()
    {
        $this->isOpenPrintModal = false;
    }

    public function printLaporan()
    {
        // 1. SIMPAN DULU DATA KE DATABASE
        $this->saveAll();

        // 2. Siapkan Parameter
        $params = ['id' => $this->laporan->id];

        if ($this->selectedJabatanPrint) {
            $params['jabatan_id'] = $this->selectedJabatanPrint;
        }

        $this->closePrintModal();

        // 3. Redirect ke PDF (Sekarang data di DB sudah terbaru)
        return redirect()->route('laporan-konsolidasi.cetak', $params);
    }

    // === UPDATE: ADD PROGRAM (AMBIL DARI MASTER) ===
    public function addProgram()
    {
        $this->validate(['selectedProgramId' => 'required']);
        DB::beginTransaction();
        try {
            // Ambil Master Program
            $progMaster = Program::find($this->selectedProgramId);

            // Create Program Record
            LaporanKonsolidasiAnggaran::firstOrCreate(
                ['laporan_konsolidasi_id' => $this->laporan->id, 'program_id' => $this->selectedProgramId],
                // Default values jika baru dibuat
                ['pagu_anggaran' => 0, 'pagu_realisasi' => 0, 'realisasi_fisik' => 0]
            );

            $kegiatans = Kegiatan::with(['program'])->where('program_id', $this->selectedProgramId)->get();
            foreach ($kegiatans as $keg) {
                // Create Kegiatan Record
                LaporanKonsolidasiAnggaran::firstOrCreate(
                    ['laporan_konsolidasi_id' => $this->laporan->id, 'kegiatan_id' => $keg->id],
                    ['pagu_anggaran' => 0, 'pagu_realisasi' => 0, 'realisasi_fisik' => 0]
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
            
            // Hitung otomatis sebelum commit
            $this->recalculateProgramKegiatanAnggaran();
            
            DB::commit();
            session()->flash('message', 'Program ditambahkan (Pagu & Fisik otomatis dijumlahkan dari Sub Kegiatan).');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
        $this->loadData();
        $this->closeProgramModal();
    }

    public function deleteProgram($programId)
    {
        DB::beginTransaction();
        try {
            LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->where('program_id', $programId)->delete();
            $kegiatanIds = Kegiatan::where('program_id', $programId)->pluck('id');
            LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->whereIn('kegiatan_id', $kegiatanIds)->delete();
            DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereHas('subKegiatan', function ($q) use ($kegiatanIds) {
                    $q->whereIn('kegiatan_id', $kegiatanIds);
                })->delete();
            DB::commit();
            $this->loadData();
            $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus', 'message' => 'Program dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function deleteKegiatan($kegiatanId)
    {
        DB::beginTransaction();
        try {
            LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)->where('kegiatan_id', $kegiatanId)->delete();
            DetailLaporanKonsolidasi::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereHas('subKegiatan', function ($q) use ($kegiatanId) {
                    $q->where('kegiatan_id', $kegiatanId);
                })->delete();
            DB::commit();
            $this->loadData();
            $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus', 'message' => 'Kegiatan dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function deleteSubKegiatan($id)
    {
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

        // --- LOGIKA PENGURUTAN (FIX VIP CODE X) ---
        $programsInReport = Program::whereIn('id', $programIds)
            ->orderByRaw("CASE WHEN kode LIKE 'X%' OR kode LIKE 'x%' THEN 0 ELSE 1 END ASC")
            ->orderBy('kode', 'asc')
            ->get();
        // ------------------------------------------

        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan', 'subKegiatan.indikators'])
            ->where('laporan_konsolidasi_id', $this->laporan->id)->get();

        $groupedData = [];
        foreach ($programsInReport as $prog) {
            $groupedData[$prog->id] = ['program' => $prog, 'kegiatans' => []];

            $kegiatanIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
                ->whereNotNull('kegiatan_id')
                ->whereHas('kegiatan', function ($q) use ($prog) {
                    $q->where('program_id', $prog->id);
                })
                ->pluck('kegiatan_id');

            $kegiatans = Kegiatan::whereIn('id', $kegiatanIds)->orderBy('kode', 'asc')->get();

            foreach ($kegiatans as $keg) {
                $subs = $detailsRaw->filter(function ($item) use ($keg) {
                    return $item->subKegiatan?->kegiatan_id == $keg->id;
                });
                $groupedData[$prog->id]['kegiatans'][$keg->id] = ['kegiatan' => $keg, 'details' => $subs];
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = collect($groupedData);
        $currentItems = $col->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
        $paginatedData = new LengthAwarePaginator($currentItems, $col->count(), $this->perPage, $currentPage, ['path' => Request::url()]);

        // HITUNG TOTAL ANGGARAN & REALISASI (SIMPAN KE PUBLIC PROPERTY)
        // INI PERBAIKANNYA: Pakai $this->... agar tersimpan di Livewire state
        $this->totalAnggaran = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
            ->whereNotNull('program_id')
            ->sum('pagu_anggaran');

        $this->totalRealisasi = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $this->laporan->id)
            ->whereNotNull('program_id')
            ->sum('pagu_realisasi');

        // --- TAMBAHAN BARU: AMBIL DATA JABATAN UNTUK DROPDOWN ---
        $jabatans = Jabatan::orderBy('nama', 'asc')->get();

        return view('livewire.laporan-konsolidasi.input-data', [
            'programs' => Program::all(),
            'jabatans' => $jabatans,
            'kegiatanOptions' => [],
            'reportData' => $paginatedData,
            // Total tidak perlu dipassing di sini lagi karena sudah ada public property
        ]);
    }
}