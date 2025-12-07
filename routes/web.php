<?php

use Illuminate\Support\Facades\Route;

// --- 1. LIVEWIRE COMPONENTS (DASHBOARD & AUTH) ---
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard; // Dashboard Pegawai
use App\Livewire\Admin\Dashboard as AdminDashboard; // Dashboard Admin
use App\Livewire\Pimpinan\Dashboard as PimpinanDashboard; // Dashboard Pimpinan (Baru)

// --- 2. LIVEWIRE COMPONENTS (FITUR UTAMA) ---
// Master Data
use App\Livewire\StrukturOrganisasi;

// Matrik Renstra
use App\Livewire\DokumenRenstra;
use App\Livewire\TujuanRenstra;
use App\Livewire\SasaranRenstra;
use App\Livewire\OutcomeRenstra;
use App\Livewire\ProgramKegiatan;
use App\Livewire\KegiatanRenstra;
use App\Livewire\SubKegiatanRenstra;

// Perencanaan Kinerja
use App\Livewire\PohonKinerja;
use App\Livewire\CascadingRenstra;
use App\Livewire\PerjanjianKinerja;
use App\Livewire\PerjanjianKinerjaDetail;
use App\Livewire\PerjanjianKinerjaLihat;

// Pengukuran Kinerja
use App\Livewire\PengukuranBulanan;
use App\Livewire\PengukuranKinerja as DetailPengukuranKinerja; // Alias biar tidak bentrok
use App\Livewire\PengaturanKinerja; 

// --- 3. MODELS & EXPORTS ---
use App\Models\PerjanjianKinerja as PkModel;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\PohonKinerja as PohonModel; // Import Model Pohon Kinerja untuk PDF
use App\Exports\DokumenRenstraExport;

// --- 4. FACADES ---
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- HALAMAN LOGIN ---
Route::get('/login', Login::class)->name('login');

// --- HALAMAN TERPROTEKSI (HARUS LOGIN) ---
Route::middleware('auth')->group(function () {
    
    // ====================================================
    // DASHBOARD AREA (SESUAI ROLE)
    // ====================================================
    
    // 1. Dashboard Pegawai (Default)
    Route::get('/', Dashboard::class)->name('dashboard');

    // 2. Dashboard Admin
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');

    // 3. Dashboard Pimpinan
    Route::get('/pimpinan/dashboard', PimpinanDashboard::class)->name('pimpinan.dashboard');
    
    // ====================================================
    // MASTER DATA
    // ====================================================
    Route::get('/struktur-organisasi', StrukturOrganisasi::class);
    
    // ====================================================
    // GROUP 1: MATRIK RENSTRA
    // ====================================================
    Route::prefix('matrik-renstra')->group(function () {
        
        Route::get('/dokumen', DokumenRenstra::class)->name('matrik.dokumen');
        
        // Export PDF Dokumen Renstra (PERBAIKAN LOGIKA)
        Route::get('/dokumen/cetak', function () {
            
            // 1. AMBIL DATA POHON KINERJA (SUMBER INDIKATOR OTOMATIS)
            $all_pohons = PohonModel::with('indikators')->get();

            // Helper untuk mengambil indikator dari node pohon
            $getIndicatorsForNode = fn($node) => $node->indikators ?? collect([]);

            // Helper untuk mencocokkan Item Renstra dengan Node Pohon Kinerja
            $findPohonNode = function($item, $type) use ($all_pohons) {
                // Bersihkan string agar pencocokan lebih akurat (lowercase, alphanumeric only)
                $cleaner = fn($str) => strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $str)));
                
                $targetName = $cleaner($type == 'tujuan' ? ($item->tujuan ?? $item->sasaran_rpjmd) : 
                             ($type == 'sasaran' ? $item->sasaran : 
                             ($type == 'outcome' ? $item->outcome : $item->nama)));

                foreach ($all_pohons as $pohon) {
                    $pohonName = $cleaner($pohon->nama_pohon);
                    // Cek kesamaan nama (contains)
                    if ($pohonName === $targetName || str_contains($pohonName, $targetName)) {
                        return $pohon;
                    }
                }
                return null;
            };

            // 2. MAPPING DATA (INJECT INDIKATOR POHON KE SETIAP ITEM)
            
            // Tujuan
            $tujuans = Tujuan::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
                $node = $findPohonNode($item, 'tujuan');
                $item->indikators_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
                return $item;
            });

            // Sasaran
            $sasarans = Sasaran::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
                $node = $findPohonNode($item, 'sasaran');
                $item->indikators_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
                return $item;
            });

            // Outcome
            $outcomes = Outcome::with(['program'])->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
                $node = $findPohonNode($item, 'outcome');
                $item->indikators_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
                return $item;
            });

            // Kegiatan (Output)
            $kegiatans = Kegiatan::whereNotNull('output')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
                $node = $findPohonNode($item, 'kegiatan');
                $item->indikators_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
                return $item;
            }); 

            // Sub Kegiatan (Load Indikator Manual + Pohon)
            $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
                // Cari node pohon yang cocok (sebagai fallback jika indikator manual kosong)
                $node = $findPohonNode($item, 'sub_kegiatan');
                $item->indikators_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
                return $item;
            });

            $header = [
                'unit_kerja' => 'DINAS KESEHATAN',
                'periode' => '2025 - 2029'
            ];
            
            $pdf = Pdf::loadView('cetak.dokumen-renstra', compact(
                'tujuans', 'sasarans', 'outcomes', 'kegiatans', 'sub_kegiatans', 'header'
            ));

            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('Matriks_RENSTRA_Dinas_Kesehatan.pdf');
            
        })->name('matrik.dokumen.print');

        // Export Excel Dokumen Renstra
        Route::get('/dokumen/excel', function () {
            return Excel::download(new DokumenRenstraExport, 'Matriks_RENSTRA_Dinas_Kesehatan.xlsx');
        })->name('matrik.dokumen.excel');

        // Sub Menu Matrik
        Route::get('/tujuan', TujuanRenstra::class);
        Route::get('/sasaran', SasaranRenstra::class);
        Route::get('/outcome', OutcomeRenstra::class);
        Route::get('/program-kegiatan-sub', ProgramKegiatan::class);
        Route::get('/program-kegiatan-sub/kegiatan/{id}', KegiatanRenstra::class)->name('matrik.kegiatan');
        Route::get('/renstra/kegiatan/{id}/sub-kegiatan', SubKegiatanRenstra::class)->name('renstra.sub_kegiatan');
    });

    // ====================================================
    // GROUP 2: PERENCANAAN KINERJA
    // ====================================================
    Route::prefix('perencanaan-kinerja')->group(function () {
        Route::get('/pohon-kinerja', PohonKinerja::class)->name('pohon.kinerja');
        Route::get('/cascading-renstra', CascadingRenstra::class)->name('cascading.renstra');
        
        // Perjanjian Kinerja (Halaman Daftar Jabatan/PK)
        Route::get('/perjanjian-kinerja', PerjanjianKinerja::class)->name('perjanjian.kinerja');
        
        // Detail PK (Input Data untuk Pegawai)
        Route::get('/perjanjian-kinerja/{id}', PerjanjianKinerjaDetail::class)->name('perjanjian.kinerja.detail');
        
        // Lihat PK (Halaman Verifikasi & Publikasi)
        Route::get('/perjanjian-kinerja/lihat/{id}', PerjanjianKinerjaLihat::class)->name('perjanjian.kinerja.lihat');

        // Cetak Perjanjian Kinerja
        Route::get('/perjanjian-kinerja/cetak/{id}', function ($id) {
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
            
            return view('cetak.perjanjian-kinerja', [
                'pk' => $pk,
                'jabatan' => $jabatan,
                'pegawai' => $pk->pegawai,
                'is_kepala_dinas' => $is_kepala_dinas,
                'atasan_pegawai' => $atasan_pegawai,
                'atasan_jabatan' => $atasan_jabatan
            ]);
        })->name('perjanjian.kinerja.print');
    });

    // ====================================================
    // GROUP 3: PENGUKURAN KINERJA
    // ====================================================
    Route::prefix('pengukuran-kinerja')->group(function () {
        
        // 1. Halaman Daftar Pengukuran (Tabel Jabatan)
        Route::get('/bulanan', PengukuranBulanan::class)->name('pengukuran.bulanan');
        
        // 2. Halaman Atur Kinerja (Detail per Jabatan)
        Route::get('/atur-kinerja/{jabatanId}', PengaturanKinerja::class)->name('pengukuran.atur');

        // 3. Halaman Pengukuran Kinerja (Isi Realisasi)
        Route::get('/pengukuran/{jabatanId}', DetailPengukuranKinerja::class)->name('pengukuran.detail');
    });
    
    // --- LOGOUT ---
    Route::get('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
    
});