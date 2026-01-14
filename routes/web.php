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

// --- 2. MODELS ---
use App\Models\PerjanjianKinerja as PkModel; // Alias biar gak bentrok sama Livewire
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\PohonKinerja;

// --- 3. EXPORT & PDF ---
use App\Exports\DokumenRenstraExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- HALAMAN LOGIN ---
Route::get('/login', Login::class)->name('login');


// --- ROUTES YANG BUTUH LOGIN (MIDDLEWARE AUTH) ---
Route::middleware('auth')->group(function () {

    // 1. LOGOUT (POST METHOD)
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');


    // 2. DASHBOARD
    Route::get('/', Dashboard::class)->name('dashboard');


    // 3. AREA ADMIN (Role: Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/atur-jadwal', AturJadwal::class)->name('admin.atur-jadwal');
        Route::get('/admin/manajemen-user', ManajemenUser::class)->name('admin.manajemen-user');
    });


    // 4. AREA PIMPINAN (Role: Pimpinan)
    Route::middleware('role:pimpinan')->group(function () {
        Route::get('/pimpinan/dashboard', PimpinanDashboard::class)->name('pimpinan.dashboard');
    });


    // 5. FITUR DOWNLOAD AMAN (SECURE FILE DOWNLOAD)
    Route::get('/dokumen/unduh/{folder}/{filename}', function ($folder, $filename) {
        $path = $folder . '/' . $filename;
        if (!Storage::exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }
        return Storage::download($path);
    })->name('dokumen.download');


    // --- MASTER DATA ---
    Route::get('/struktur-organisasi', StrukturOrganisasi::class);
    
    // --- MATRIK RENSTRA ---
    Route::prefix('matrik-renstra')->group(function () {
        
        Route::get('/dokumen', DokumenRenstra::class)->name('matrik.dokumen');
        
        // CETAK PDF RENSTRA
        Route::get('/dokumen/cetak', function () {
            $tujuans = Tujuan::with('pohonKinerja.indikators')->get();
            $sasarans = Sasaran::with('pohonKinerja.indikators')->get();
            $outcomes = Outcome::with(['program', 'pohonKinerja.indikators'])->get();
            $kegiatans = Kegiatan::with('pohonKinerja.indikators')->whereNotNull('output')->get();
            $sub_kegiatans = SubKegiatan::with('pohonKinerja.indikators')->get();

            $header = ['unit_kerja' => 'DINAS KESEHATAN', 'periode' => '2025 - 2029'];
            
            $pdf = Pdf::loadView('cetak.dokumen-renstra', compact(
                'tujuans', 'sasarans', 'outcomes', 'kegiatans', 'sub_kegiatans', 'header'
            ));

            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('Matriks_RENSTRA_Dinas_Kesehatan.pdf');
        })->name('matrik.dokumen.print');

        // EXPORT EXCEL RENSTRA
        Route::get('/dokumen/excel', function () {
            $data = [
                'tujuans' => Tujuan::with('pohonKinerja.indikators')->get(),
                'sasarans' => Sasaran::with('pohonKinerja.indikators')->get(),
                'outcomes' => Outcome::with(['program', 'pohonKinerja.indikators'])->get(),
                'kegiatans' => Kegiatan::with('pohonKinerja.indikators')->whereNotNull('output')->get(),
                'sub_kegiatans' => SubKegiatan::with('pohonKinerja.indikators')->get(),
                'header' => ['unit_kerja' => 'DINAS KESEHATAN', 'periode' => '2025 - 2029']
            ];
            return Excel::download(new DokumenRenstraExport($data), 'Matriks_RENSTRA_Dinas_Kesehatan.xlsx');
        })->name('matrik.dokumen.excel');

        // SUB MENU MATRIK
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
        Route::get('/perjanjian-kinerja', PerjanjianKinerja::class)->name('perjanjian.kinerja');
        Route::get('/perjanjian-kinerja/{id}', PerjanjianKinerjaDetail::class)->name('perjanjian.kinerja.detail');
        Route::get('/perjanjian-kinerja/lihat/{id}', PerjanjianKinerjaLihat::class)->name('perjanjian.kinerja.lihat');
        
        // CETAK PERJANJIAN KINERJA (UPDATED)
        Route::get('/perjanjian-kinerja/cetak/{id}', function ($id) {
            // 1. Ambil Data PK Lengkap
            $pk = PkModel::with(['jabatan', 'pegawai', 'sasarans.indikators', 'anggarans.subKegiatan'])->findOrFail($id);
            $jabatan = $pk->jabatan;
            
            // 2. Tentukan Logic Kepala Dinas / Atasan
            $is_kepala_dinas = is_null($jabatan->parent_id);
            $atasan_pegawai = null;
            $atasan_jabatan = null; // <--- Variabel penting untuk Pihak Pertama

            if ($jabatan->parent_id) {
                $parentJabatan = Jabatan::find($jabatan->parent_id);
                if ($parentJabatan) {
                    $atasan_jabatan = $parentJabatan;
                    $atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
                }
            }
            
            // 3. Load PDF dengan Data Lengkap
            $pdf = Pdf::loadView('cetak.perjanjian-kinerja', [
                'pk' => $pk,
                'jabatan' => $jabatan,
                'pegawai' => $pk->pegawai,
                'is_kepala_dinas' => $is_kepala_dinas,
                'atasan_pegawai' => $atasan_pegawai,
                'atasan_jabatan' => $atasan_jabatan // <--- Pastikan ini terkirim
            ]);

            $pdf->setPaper('a4', 'portrait');
            $namaFile = 'PK_' . $pk->tahun . '_' . str_replace(' ', '_', $jabatan->nama) . '.pdf';

            return $pdf->download($namaFile);
        })->name('perjanjian.kinerja.print');
    });

    // --- PENGUKURAN KINERJA ---
    Route::prefix('pengukuran-kinerja')->group(function () {
        Route::get('/bulanan', PengukuranBulanan::class)->name('pengukuran.bulanan');
        Route::get('/atur-kinerja/{jabatanId}', PengaturanKinerja::class)->name('pengukuran.atur');
        Route::get('/pengukuran/{jabatanId}', DetailPengukuranKinerja::class)->name('pengukuran.detail');
    });
});