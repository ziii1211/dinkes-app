<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PerjanjianKinerja;
use App\Models\PkSasaran;
use App\Models\PkIndikator;
use App\Models\PkAnggaran;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Sasaran; // Model Sasaran Renstra
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\Auth;

class PerjanjianKinerjaLihat extends Component
{
    public $pkId;
    public $pk;
    
    // Data Pendukung Tampilan
    public $jabatan;
    public $pegawai;
    public $atasan_pegawai;
    public $atasan_jabatan;
    public $is_kepala_dinas = false;
    
    public $gubernur_nama = 'H. MUHIDIN'; 
    public $gubernur_jabatan = 'GUBERNUR KALIMANTAN SELATAN';
    // public $gubernur_foto = 'muhidin.png'; 

    // --- STATES MODAL ---
    public $isOpenKinerjaUtama = false;
    public $isOpenAnggaran = false;
    
    // --- FORM PROPERTIES ---
    public $sasaran_id; 
    
    public $anggaran_sub_kegiatan_id;
    public $anggaran_nilai;

    public function mount($id)
    {
        $this->pkId = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $this->pk = PerjanjianKinerja::with([
            'jabatan', 
            'pegawai', 
            'sasarans.indikators', 
            'anggarans.subKegiatan'
        ])->findOrFail($this->pkId);

        $this->jabatan = $this->pk->jabatan;
        $this->pegawai = $this->pk->pegawai;
        $this->is_kepala_dinas = is_null($this->jabatan->parent_id);

        if ($this->jabatan->parent_id) {
            $parentJabatan = Jabatan::find($this->jabatan->parent_id);
            if ($parentJabatan) {
                $this->atasan_jabatan = $parentJabatan;
                $this->atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
            }
        }
    }

    public function canEdit()
    {
        if (Auth::user()->role == 'admin') return true;
        if ($this->pk->status_verifikasi == 'draft') return true;
        return false;
    }

    public function ajukan()
    {
        if (!$this->canEdit()) return;

        if ($this->pk->sasarans->count() == 0) {
            session()->flash('error', 'Gagal mempublikasikan. Harap isi minimal satu Kinerja Utama.');
            return;
        }

        $this->pk->update([
            'status_verifikasi' => 'disetujui', 
            'tanggal_verifikasi' => now()
        ]);

        session()->flash('message', 'Perjanjian Kinerja BERHASIL DIPUBLIKASIKAN.');
        $this->loadData();
    }

    // =================================================================
    // FITUR CRUD KINERJA UTAMA
    // =================================================================

    public function openModalKinerjaUtama() { 
        if (!$this->canEdit()) return; 
        $this->reset(['sasaran_id']); 
        $this->isOpenKinerjaUtama = true; 
    }

    public function storeKinerjaUtama() {
        if (!$this->canEdit()) return;

        $this->validate([
            'sasaran_id' => 'required', 
        ]);

        // Ambil Data Sasaran Renstra
        $sumberSasaran = Sasaran::with('indikators')->find($this->sasaran_id);

        if (!$sumberSasaran) {
            session()->flash('error', 'Data Sasaran tidak ditemukan.');
            return;
        }

        // Simpan Sasaran ke PK
        $pkSasaran = PkSasaran::create([
            'perjanjian_kinerja_id' => $this->pk->id, 
            'sasaran' => $sumberSasaran->sasaran 
        ]);

        // Salin Indikator dari Renstra ke PK
        foreach ($sumberSasaran->indikators as $indAsli) {
            PkIndikator::create([
                'pk_sasaran_id' => $pkSasaran->id,
                // Pastikan mengambil nama indikator dengan benar (dari 'keterangan')
                'nama_indikator' => $indAsli->keterangan ?? $indAsli->indikator ?? 'Indikator Tanpa Nama',
                'satuan' => $indAsli->satuan,
                'arah' => $indAsli->arah ?? 'Naik', 
                
                // Salin Target Tahunan agar otomatis tampil
                'target_2025' => $indAsli->target_2025, 
                'target_2026' => $indAsli->target_2026,
                'target_2027' => $indAsli->target_2027, 
                'target_2028' => $indAsli->target_2028, 
                'target_2029' => $indAsli->target_2029, 
                'target_2030' => $indAsli->target_2030,
            ]);
        }

        $this->loadData(); 
        $this->closeModal();
        session()->flash('message', 'Kinerja Utama berhasil ditambahkan.');
    }

    public function deleteKinerjaUtama($id) {
        if (!$this->canEdit()) return;
        $sasaran = PkSasaran::find($id);
        if($sasaran) { 
            $sasaran->delete(); 
            $this->loadData(); 
        }
    }

    public function deleteIndikator($id) {
        if (!$this->canEdit()) return;
        $ind = PkIndikator::find($id);
        if($ind) { 
            $ind->delete(); 
            $this->loadData(); 
        }
    }

    // =================================================================
    // FITUR ANGGARAN
    // =================================================================

    public function openModalAnggaran() {
        if (!$this->canEdit()) return;
        $this->reset(['anggaran_sub_kegiatan_id', 'anggaran_nilai']);
        $this->isOpenAnggaran = true;
    }

    public function storeAnggaran() {
        if (!$this->canEdit()) return;
        $this->validate([
            'anggaran_sub_kegiatan_id' => 'required', 
            'anggaran_nilai' => 'required|numeric|min:0'
        ]);

        PkAnggaran::create([
            'perjanjian_kinerja_id' => $this->pk->id,
            'sub_kegiatan_id' => $this->anggaran_sub_kegiatan_id,
            'anggaran' => $this->anggaran_nilai
        ]);

        $this->loadData();
        $this->closeModal();
    }

    public function deleteAnggaran($id) {
        if (!$this->canEdit()) return;
        $ang = PkAnggaran::find($id);
        if($ang) { 
            $ang->delete(); 
            $this->loadData(); 
        }
    }

    // =================================================================
    // UTILITIES
    // =================================================================

    public function closeModal() {
        $this->isOpenKinerjaUtama = false;
        $this->isOpenAnggaran = false;
        $this->resetValidation();
    }

    public function deletePk() {
        if (!$this->canEdit()) return;
        $jabatanId = $this->pk->jabatan_id;
        $this->pk->delete();
        return redirect()->route('perjanjian.kinerja.detail', $jabatanId);
    }

    public function render()
    {
        // LOGIKA TAMPILAN SASARAN:
        // Jika Kepala Dinas, ambil SEMUA Sasaran Renstra
        if ($this->is_kepala_dinas) {
            $sasarans = Sasaran::with('indikators')
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            $sasarans = collect(); 
        }

        $sub_kegiatans = SubKegiatan::orderBy('created_at', 'desc')->get();
        
        return view('livewire.perjanjian-kinerja-lihat', [
            'sasarans' => $sasarans,
            'sub_kegiatans' => $sub_kegiatans
        ]);
    }
}