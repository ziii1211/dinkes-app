<?php

use Illuminate\Support\Facades\Route;

// --- 1. LIVEWIRE COMPONENTS ---
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Pimpinan\Dashboard as PimpinanDashboard;
use App\Livewire\StrukturOrganisasi;
use App\Livewire\DokumenRenstra;
use App\Livewire\TujuanRenstra;
use App\Livewire\SasaranRenstra;
use App\Livewire\OutcomeRenstra;
use App\Livewire\ProgramKegiatan;
use App\Livewire\KegiatanRenstra;
use App\Livewire\SubKegiatanRenstra;

// Import Class Baru (CascadingRenstra)
use App\Livewire\CascadingRenstra; 

use App\Livewire\PerjanjianKinerja;
use App\Livewire\PerjanjianKinerjaDetail;
use App\Livewire\PerjanjianKinerjaLihat;
use App\Livewire\PengukuranBulanan;
use App\Livewire\PengukuranKinerja as DetailPengukuranKinerja;
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
use App\Models\PohonKinerja as PohonModel;
use App\Exports\DokumenRenstraExport;

// --- 4. FACADES ---
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    
    // DASHBOARD
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/pimpinan/dashboard', PimpinanDashboard::class)->name('pimpinan.dashboard');
    
    // MASTER DATA
    Route::get('/struktur-organisasi', StrukturOrganisasi::class);
    
    // MATRIK RENSTRA
    Route::prefix('matrik-renstra')->group(function () {
        
        Route::get('/dokumen', DokumenRenstra::class)->name('matrik.dokumen');
        
        // --- EXPORT PDF MATRIKS RENSTRA ---
        Route::get('/dokumen/cetak', function () {
            
            // 1. Ambil Data Pohon Kinerja & Indikatornya
            $all_pohons = PohonModel::with('indikators')->get();

            // Helper: Bersihkan string untuk pencocokan nama
            $cleaner = fn($str) => strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $str)));

            // Helper: Cari Node Pohon yang cocok dengan Item Renstra
            $findPohonNode = function($item, $type) use ($all_pohons, $cleaner) {
                // A. Cek ID (Khusus Tujuan)
                if ($type === 'tujuan' && isset($item->id)) {
                    $matchById = $all_pohons->where('tujuan_id', $item->id)->first();
                    if ($matchById) return $matchById;
                }

                // B. Cek Nama (Fuzzy Match)
                $targetName = $cleaner($type == 'tujuan' ? ($item->tujuan ?? $item->sasaran_rpjmd) : 
                             ($type == 'sasaran' ? $item->sasaran : 
                             ($type == 'outcome' ? $item->outcome : 
                             ($type == 'kegiatan' || $type == 'sub_kegiatan' ? $item->nama : ''))));

                foreach ($all_pohons as $pohon) {
                    $pohonName = $cleaner($pohon->nama_pohon);
                    if ($pohonName === $targetName || str_contains($pohonName, $targetName)) {
                        return $pohon;
                    }
                }
                return null;
            };

            // 2. Mapping Data (Inject indikator_pohon ke setiap item)
            
            $tujuans = Tujuan::all()->map(function($item) use ($findPohonNode) {
                $node = $findPohonNode($item, 'tujuan');
                $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
                return $item;
            });

            $sasarans = Sasaran::all()->map(function($item) use ($findPohonNode) {
                $node = $findPohonNode($item, 'sasaran');
                $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
                return $item;
            });

            $outcomes = Outcome::with(['program'])->get()->map(function($item) use ($findPohonNode) {
                $node = $findPohonNode($item, 'outcome');
                $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
                return $item;
            });

            $kegiatans = Kegiatan::whereNotNull('output')->get()->map(function($item) use ($findPohonNode) {
                $node = $findPohonNode($item, 'kegiatan');
                $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
                return $item;
            }); 

            // Sub Kegiatan: Load Indikator Manual + Pohon
            $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) use ($findPohonNode) {
                $node = $findPohonNode($item, 'sub_kegiatan');
                $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
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

        // Export Excel
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

    // PERENCANAAN KINERJA
    Route::prefix('perencanaan-kinerja')->group(function () {
        
        Route::get('/cascading-renstra', CascadingRenstra::class)->name('cascading.renstra');
        
        // Hapus Route lama jika sudah tidak dipakai
        // Route::get('/cascading-renstra-lama', ...); 
        
        Route::get('/perjanjian-kinerja', PerjanjianKinerja::class)->name('perjanjian.kinerja');
        Route::get('/perjanjian-kinerja/{id}', PerjanjianKinerjaDetail::class)->name('perjanjian.kinerja.detail');
        Route::get('/perjanjian-kinerja/lihat/{id}', PerjanjianKinerjaLihat::class)->name('perjanjian.kinerja.lihat');
        
        // --- PERBAIKAN: CETAK PERJANJIAN KINERJA (DOWNLOAD PDF) ---
        Route::get('/perjanjian-kinerja/cetak/{id}', function ($id) {
            
            // 1. Ambil Data
            $pk = PkModel::with(['jabatan', 'pegawai', 'sasarans.indikators', 'anggarans.subKegiatan'])->findOrFail($id);
            $jabatan = $pk->jabatan;
            
            // Logika Penentuan Atasan (Pihak Pertama)
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
            
            // 2. Generate PDF (Load View)
            $pdf = Pdf::loadView('cetak.perjanjian-kinerja', [
                'pk' => $pk,
                'jabatan' => $jabatan,
                'pegawai' => $pk->pegawai,
                'is_kepala_dinas' => $is_kepala_dinas,
                'atasan_pegawai' => $atasan_pegawai,
                'atasan_jabatan' => $atasan_jabatan
            ]);

            // 3. Atur Kertas & Download
            $pdf->setPaper('a4', 'portrait');
            
            // Nama file dinamis agar rapi saat didownload
            $namaFile = 'PK_' . $pk->tahun . '_' . str_replace(' ', '_', $jabatan->nama) . '.pdf';

            return $pdf->download($namaFile);

        })->name('perjanjian.kinerja.print');
    });

    // PENGUKURAN KINERJA
    Route::prefix('pengukuran-kinerja')->group(function () {
        Route::get('/bulanan', PengukuranBulanan::class)->name('pengukuran.bulanan');
        Route::get('/atur-kinerja/{jabatanId}', PengaturanKinerja::class)->name('pengukuran.atur');
        Route::get('/pengukuran/{jabatanId}', DetailPengukuranKinerja::class)->name('pengukuran.detail');
    });
    
    // LOGOUT
    Route::get('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});