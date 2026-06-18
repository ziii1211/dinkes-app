<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// --- 1. LIVEWIRE COMPONENTS ---
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\AturJadwal;
use App\Livewire\Admin\ManajemenUser;
use App\Livewire\Pimpinan\Dashboard as PimpinanDashboard;
use App\Livewire\StrukturOrganisasi;
use App\Livewire\DokumenRenstra;
use App\Livewire\TujuanRenstra;
use App\Livewire\SasaranRenstra;
use App\Livewire\OutcomeRenstra;
use App\Livewire\ProgramKegiatan;
use App\Livewire\KegiatanRenstra;
use App\Livewire\SubKegiatanRenstra;
use App\Livewire\CascadingRenstra;
use App\Livewire\PerjanjianKinerja;
use App\Livewire\PerjanjianKinerjaDetail;
use App\Livewire\PerjanjianKinerjaLihat;
use App\Livewire\PengukuranBulanan;
use App\Livewire\PengukuranKinerja as DetailPengukuranKinerja;
use App\Livewire\PengaturanKinerja;
use App\Livewire\LaporanKonsolidasi\Index as LaporanKonsolidasiIndex;
use App\Livewire\LaporanKonsolidasi\InputData as LaporanKonsolidasiInput;

// --- 2. MODELS ---
use App\Models\PerjanjianKinerja as PkModel; 
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\PohonKinerja;
use App\Models\VisualisasiRenstra;

// --- 3. EXPORT & PDF ---
use App\Exports\DokumenRenstraExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\LaporanKonsolidasiCetakController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {

    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/', Dashboard::class)->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/atur-jadwal', AturJadwal::class)->name('admin.atur-jadwal');
        Route::get('/admin/manajemen-user', ManajemenUser::class)->name('admin.manajemen-user');
    });

    Route::middleware('role:pimpinan')->group(function () {
        Route::get('/pimpinan/dashboard', PimpinanDashboard::class)->name('pimpinan.dashboard');
    });

    Route::get('/dokumen/unduh/{folder}/{filename}', function ($folder, $filename) {
        $path = $folder . '/' . $filename;
        if (!Storage::exists($path)) { abort(404, 'File tidak ditemukan.'); }
        return Storage::download($path);
    })->name('dokumen.download');

    Route::get('/struktur-organisasi', StrukturOrganisasi::class);

    // --- MATRIK RENSTRA ---
    Route::prefix('matrik-renstra')->group(function () {

        Route::get('/dokumen', DokumenRenstra::class)->name('matrik.dokumen');

        Route::get('/dokumen/cetak', function () {
            $tujuans = Tujuan::with('indikators')->get();
            $sasarans = Sasaran::with('indikators')->get();
            $outcomes = Outcome::with(['program', 'indikators'])->get();
            $kegiatans = Kegiatan::with('outputs.indikators')->get();
            $sub_kegiatans = SubKegiatan::with('indikators')->get();

            $header = ['unit_kerja' => 'DINAS KESEHATAN', 'periode' => '2025 - 2029'];

            $pdf = Pdf::loadView('cetak.dokumen-renstra', compact(
                'tujuans', 'sasarans', 'outcomes', 'kegiatans', 'sub_kegiatans', 'header'
            ));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('Matriks_RENSTRA_Dinas_Kesehatan.pdf');
        })->name('matrik.dokumen.print');

        Route::get('/dokumen/excel', function () {
            $data = [
                'tujuans' => Tujuan::with('indikators')->get(),
                'sasarans' => Sasaran::with('indikators')->get(),
                'outcomes' => Outcome::with(['program', 'indikators'])->get(),
                'kegiatans' => Kegiatan::with('outputs.indikators')->get(),
                'sub_kegiatans' => SubKegiatan::with('indikators')->get(),
                'header' => ['unit_kerja' => 'DINAS KESEHATAN', 'periode' => '2025 - 2029']
            ];
            return Excel::download(new DokumenRenstraExport($data), 'Matriks_RENSTRA_Dinas_Kesehatan.xlsx');
        })->name('matrik.dokumen.excel');

        Route::get('/tujuan', TujuanRenstra::class);
        Route::get('/sasaran', SasaranRenstra::class);
        Route::get('/outcome', OutcomeRenstra::class);
        Route::get('/program-kegiatan-sub', ProgramKegiatan::class)->name('matrik.program');
        Route::get('/program-kegiatan-sub/kegiatan/{id}', KegiatanRenstra::class)->name('matrik.kegiatan');
        Route::get('/renstra/kegiatan/{id}/sub-kegiatan', SubKegiatanRenstra::class)->name('renstra.sub_kegiatan');
    });

    // --- PERENCANAAN KINERJA ---
    Route::prefix('perencanaan-kinerja')->group(function () {
        Route::get('/cascading-renstra', CascadingRenstra::class)->name('cascading.renstra');
        
        Route::get('/cascading-renstra/cetak', function () {
            $allNodes = VisualisasiRenstra::orderBy('id', 'asc')->get();
            $nodes = [];
            
            foreach($allNodes as $dbNode) {
                $items = $dbNode->content_data;
                if(is_string($items)) { $items = json_decode($items, true); }
                if(empty($items) || !is_array($items)) { $items = []; }

                $nodes[] = (object)[
                    'id' => $dbNode->id,
                    'parent_id' => $dbNode->parent_id,
                    'jabatan' => $dbNode->jabatan,
                    'kinerja_items' => $items,
                    'children' => collect([])
                ];
            }

            $nodesDict = collect($nodes)->keyBy('id');
            $tree = collect();
            
            foreach($nodes as $node) {
                if($node->parent_id && isset($nodesDict[$node->parent_id])) {
                    $nodesDict[$node->parent_id]->children->push($node);
                } else {
                    $tree->push($node);
                }
            }

            $header = ['unit_kerja' => 'DINAS KESEHATAN', 'periode' => '2025 - 2029'];

            $pdf = Pdf::loadView('cetak.pohon-kinerja', compact('tree', 'header'));
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('Visualisasi_Pohon_Kinerja.pdf');
        })->name('cascading.renstra.print');

        Route::get('/perjanjian-kinerja', PerjanjianKinerja::class)->name('perjanjian.kinerja');
        Route::get('/perjanjian-kinerja/{id}', PerjanjianKinerjaDetail::class)->name('perjanjian.kinerja.detail');
        Route::get('/perjanjian-kinerja/lihat/{id}', PerjanjianKinerjaLihat::class)->name('perjanjian.kinerja.lihat');

        Route::get('/perjanjian-kinerja/cetak/{id}', function ($id) {
            $pk = PkModel::with(['jabatan', 'pegawai', 'sasarans.indikators', 'anggarans.subKegiatan'])->findOrFail($id);
            $jabatan = $pk->jabatan;

            $is_kepala_dinas = is_null($jabatan->parent_id);
            $atasan_pegawai = null; $atasan_jabatan = null;

            if ($jabatan->parent_id) {
                $parentJabatan = Jabatan::find($jabatan->parent_id);
                if ($parentJabatan) {
                    $atasan_jabatan = $parentJabatan;
                    $atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
                }
            }

            $pdf = Pdf::loadView('cetak.perjanjian-kinerja', [
                'pk' => $pk, 'jabatan' => $jabatan, 'pegawai' => $pk->pegawai,
                'is_kepala_dinas' => $is_kepala_dinas, 'atasan_pegawai' => $atasan_pegawai, 'atasan_jabatan' => $atasan_jabatan
            ]);

            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('PK_' . $pk->tahun . '_' . str_replace(' ', '_', $jabatan->nama) . '.pdf');
        })->name('perjanjian.kinerja.print');

        Route::get('/rencana-aksi/cetak/{id}', function ($id) {
            $pk = PkModel::with(['jabatan', 'pegawai', 'sasarans.indikators', 'anggarans.subKegiatan'])->findOrFail($id);
            $jabatan = $pk->jabatan;

            $is_kepala_dinas = is_null($jabatan->parent_id);
            $atasan_pegawai = null;
            $atasan_jabatan = null;

            if ($jabatan->parent_id) {
                $parentJabatan = Jabatan::find($jabatan->parent_id);
                if ($parentJabatan) {
                    $atasan_jabatan = $parentJabatan;
                    $atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
                }
            }

            $pdf = Pdf::loadView('cetak.rencana-aksi', [
                'pk' => $pk,
                'jabatan' => $jabatan,
                'pegawai' => $pk->pegawai,
                'is_kepala_dinas' => $is_kepala_dinas,
                'atasan_pegawai' => $atasan_pegawai,
                'atasan_jabatan' => $atasan_jabatan
            ]);

            $pdf->setPaper('a4', 'landscape');
            $namaFile = 'Rencana_Aksi_' . $pk->tahun . '_' . str_replace(' ', '_', $jabatan->nama) . '.pdf';

            return $pdf->download($namaFile);
        })->name('rencana.aksi.print');
    });

    // --- PENGUKURAN KINERJA ---
    Route::prefix('pengukuran-kinerja')->group(function () {
        Route::get('/bulanan', PengukuranBulanan::class)->name('pengukuran.bulanan');
        Route::get('/atur-kinerja/{jabatanId}', PengaturanKinerja::class)->name('pengukuran.atur');
        Route::get('/pengukuran/{jabatanId}', DetailPengukuranKinerja::class)->name('pengukuran.detail');

        Route::get('/bulanan/cetak/{id}/{bulan}', function ($id, $bulan) {
            $pk = PkModel::with(['jabatan.pegawai', 'sasarans.indikators'])->findOrFail($id);
            $jabatan = $pk->jabatan;
            $tahun = $pk->tahun;
            
            $namaBulanList = [
                1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 
                5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 
                9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
            ];
            $namaBulan = $namaBulanList[$bulan] ?? 'Bulan';

            $atasan = null;
            if ($jabatan->parent_id) {
                $atasan = Jabatan::with('pegawai')->find($jabatan->parent_id);
            }

            $indikatorIds = [];
            foreach($pk->sasarans as $sasaran) {
                foreach($sasaran->indikators as $ind) {
                    $indikatorIds[] = $ind->id;
                }
            }

            $realisasis = \App\Models\RealisasiKinerja::whereIn('indikator_id', $indikatorIds)
                ->where('bulan', '<=', $bulan)->get();
            $realisasiData = $realisasis->groupBy('indikator_id');

            $rencanaAksis = \App\Models\RencanaAksi::where('jabatan_id', $jabatan->id)
                ->where('tahun', $tahun)->get();
            
            $rencanaAksiIds = $rencanaAksis->pluck('id')->toArray();
            $realisasiAksis = \App\Models\RealisasiRencanaAksi::whereIn('rencana_aksi_id', $rencanaAksiIds)
                ->where('bulan', '<=', $bulan)->get();
            $realisasiAksiData = $realisasiAksis->groupBy('rencana_aksi_id');

            $penjelasans = \App\Models\PenjelasanKinerja::where('jabatan_id', $jabatan->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->get();

            $hariIni = \Carbon\Carbon::now()->timezone('Asia/Makassar')->format('d');

            $pdf = Pdf::loadView('cetak.laporan-kinerja-bulanan', compact(
                'pk', 'jabatan', 'tahun', 'namaBulan', 'atasan',
                'realisasiData', 'rencanaAksis', 'realisasiAksiData', 'penjelasans', 'hariIni'
            ));

            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('Laporan_Kinerja_Bulanan_'.$namaBulan.'_'.$tahun.'.pdf');
        })->name('kinerja.bulanan.print');

        Route::get('/tahunan/cetak/{id}', function ($id) {
            // PERBAIKAN: Menambahkan relasi `anggarans.subKegiatan.indikators` 
            $pk = PkModel::with(['jabatan.pegawai', 'sasarans.indikators', 'anggarans.subKegiatan.indikators'])->findOrFail($id);
            $jabatan = $pk->jabatan;
            $tahun = $pk->tahun;

            $atasan = null;
            if ($jabatan->parent_id) {
                $atasan = Jabatan::with('pegawai')->find($jabatan->parent_id);
            }

            $indikatorIds = [];
            foreach($pk->sasarans as $sasaran) {
                foreach($sasaran->indikators as $ind) {
                    $indikatorIds[] = $ind->id;
                }
            }

            $realisasis = \App\Models\RealisasiKinerja::whereIn('indikator_id', $indikatorIds)
                ->where('bulan', '<=', 12)->get();
            $realisasiData = $realisasis->groupBy('indikator_id');

            // PERBAIKAN TANGGAL: Menggunakan locale 'id' dan format translated
            $hariIni = \Carbon\Carbon::now()->timezone('Asia/Makassar')->locale('id')->translatedFormat('d F Y');

            $pdf = Pdf::loadView('cetak.laporan-realisasi-tahunan', compact(
                'pk', 'jabatan', 'tahun', 'atasan', 'realisasiData', 'hariIni'
            ));

            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('Laporan_Realisasi_Tahunan_'.$tahun.'.pdf');
        })->name('kinerja.tahunan.print');
    });

    // --- LAPORAN KONSOLIDASI ---
    Route::prefix('laporan-konsolidasi')->group(function () {
        Route::get('/master-data', \App\Livewire\Laporan\MasterData::class)->name('laporan.master');
        Route::get('/', LaporanKonsolidasiIndex::class)->name('laporan-konsolidasi.index');
        Route::get('/{id}/input-data', LaporanKonsolidasiInput::class)->name('laporan-konsolidasi.input');

        // RUTE JEMBATAN UNTUK PUSAT LAPORAN (AGAR CONTROLLER ASLI TIDAK ERROR)
        Route::get('/redirect-pusat-laporan', function (\Illuminate\Http\Request $request) {
            $tahun = $request->query('tahun');
            $jabatanId = $request->query('jabatan_id');

            // Cari Laporan yang sesuai dengan tahun
            $laporan = \App\Models\LaporanKonsolidasi::where('tahun', $tahun)->latest()->first();

            // Jika tidak ditemukan, gagalkan dengan ramah
            if (!$laporan) {
                return abort(404, "Data Laporan E-Monev untuk Tahun $tahun belum tersedia. Silakan input data terlebih dahulu.");
            }

            // Arahkan ke Controller asli dengan membawa ID Laporan dan Jabatan
            return redirect()->route('laporan-konsolidasi.cetak', [
                'id' => $laporan->id,
                'jabatan_id' => $jabatanId
            ]);
        })->name('pusat.laporan.emonev.redirect');
    });

    Route::get('/laporan-konsolidasi/cetak/{id}', [LaporanKonsolidasiCetakController::class, 'cetak'])
        ->name('laporan-konsolidasi.cetak');

    Route::get('/cetak-top-performer', [LaporanKonsolidasiCetakController::class, 'cetakTopPerformer'])
        ->name('top.performer.print');
    Route::get('/cetak/penilaian-divisi', [\App\Http\Controllers\CetakPenilaianDivisiController::class, 'cetak'])->name('cetak.penilaian-divisi');
    // Route Cetak Laporan Keputusan Kepala Dinas
    Route::get('/cetak/keputusan-kadis', [\App\Http\Controllers\CetakKeputusanKadisController::class, 'cetak'])->name('cetak.keputusan-kadis');
    // Route Cetak Grafik Dashboard
    Route::post('/cetak/grafik-capaian', [\App\Http\Controllers\CetakGrafikController::class, 'cetak'])->name('cetak.grafik');
    // Route Cetak Laporan Grafik
    Route::get('/cetak/grafik-capaian', [\App\Http\Controllers\CetakGrafikController::class, 'cetak'])->name('cetak.grafik');
    // --- PUSAT LAPORAN ---
    Route::prefix('pusat-laporan')->name('laporan.')->group(function () {
        Route::get('/', \App\Livewire\PusatLaporan::class)->name('index'); 

        // RUTE CETAK PDF DATA PEGAWAI
        Route::get('/cetak-pegawai', function (\Illuminate\Http\Request $request) {
            $jabatanId = $request->query('jabatan_id');
            
            $query = \App\Models\Pegawai::with('jabatan');
            $filterJabatan = null;

            if ($jabatanId) {
                $query->where('jabatan_id', $jabatanId);
                $filterJabatan = \App\Models\Jabatan::find($jabatanId);
            }

            // Menggabungkan dengan tabel jabatan agar bisa diurutkan secara hierarki/ID
            $pegawais = $query->join('jabatans', 'pegawais.jabatan_id', '=', 'jabatans.id')
                              ->orderBy('jabatans.id', 'asc')
                              ->select('pegawais.*')
                              ->get();

            // Ambil data Kepala Dinas untuk tanda tangan di bawah
            $kepalaDinas = \App\Models\Jabatan::whereNull('parent_id')->with('pegawai')->first();
            $hariIni = \Carbon\Carbon::now()->timezone('Asia/Makassar')->format('d F Y');

            $pdf = Pdf::loadView('cetak.laporan-data-pegawai', compact('pegawais', 'filterJabatan', 'kepalaDinas', 'hariIni'));
            $pdf->setPaper('a4', 'portrait');

            $namaFile = $filterJabatan ? 'Data_Pegawai_' . str_replace(' ', '_', $filterJabatan->nama) : 'Data_Pegawai_Full_SKPD';
            return $pdf->download($namaFile . '.pdf');
        })->name('pegawai.print');
    });
});