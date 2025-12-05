<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PerjanjianKinerja;
use App\Models\PkSasaran;
use App\Models\PkIndikator;
use App\Models\PkAnggaran;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\IndikatorSubKegiatan;
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
    public $gubernur_foto = 'muhidin.png'; 

    // --- STATES MODAL ---
    public $isOpenKinerjaUtama = false;
    public $isOpenAnggaran = false;
    public $isOpenAturTarget = false;
    // Modal Verifikasi (Baru)
    public $isOpenVerifikasi = false;
    public $verifikasiStatusTemp = ''; 

    // --- FORM PROPERTIES ---
    public $sub_kegiatan_id;
    public $selected_output;
    
    public $anggaran_sub_kegiatan_id;
    public $anggaran_nilai;

    public $editingIndikatorId = null; 
    public $editTargetValue;

    // Properti untuk Pimpinan
    public $catatan_pimpinan;

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
        $this->catatan_pimpinan = $this->pk->catatan_pimpinan; // Load catatan lama jika ada

        if ($this->jabatan->parent_id) {
            $parentJabatan = Jabatan::find($this->jabatan->parent_id);
            if ($parentJabatan) {
                $this->atasan_jabatan = $parentJabatan;
                $this->atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
            }
        }
    }

    // =================================================================
    // 1. FITUR VERIFIKASI (KHUSUS PIMPINAN) - BARU
    // =================================================================

    public function openModalVerifikasi($status)
    {
        $this->verifikasiStatusTemp = $status;
        $this->isOpenVerifikasi = true;
    }

    public function simpanVerifikasi()
    {
        // Pastikan yang eksekusi adalah Pimpinan
        if (Auth::user()->role !== 'pimpinan') {
            session()->flash('error', 'Anda tidak memiliki akses.');
            return;
        }

        $this->pk->update([
            'status_verifikasi' => $this->verifikasiStatusTemp,
            'catatan_pimpinan' => $this->catatan_pimpinan,
            'tanggal_verifikasi' => now()
        ]);

        $statusText = $this->verifikasiStatusTemp == 'disetujui' ? 'DISETUJUI' : 'DITOLAK';
        session()->flash('message', "Dokumen berhasil $statusText.");
        
        $this->isOpenVerifikasi = false;
        $this->loadData();
    }

    // =================================================================
    // 2. FITUR PUBLIKASI (PEGAWAI)
    // =================================================================
    
    public function ajukan()
    {
        if ($this->pk->sasarans->count() == 0) {
            session()->flash('error', 'Gagal mempublikasikan. Harap isi minimal satu Kinerja Utama.');
            return;
        }

        $this->pk->update(['status_verifikasi' => 'pending']);
        session()->flash('message', 'Perjanjian Kinerja BERHASIL DIKIRIM ke Pimpinan.');
        $this->loadData();
    }

    public function batalkan()
    {
        if ($this->pk->status_verifikasi == 'pending') {
            $this->pk->update(['status_verifikasi' => 'draft']);
            session()->flash('message', 'Pengajuan dibatalkan. Status kembali menjadi Draft.');
            $this->loadData();
        }
    }

    // =================================================================
    // 3. FITUR INPUT DATA (SAMA SEPERTI SEBELUMNYA)
    // =================================================================

    public function openModalKinerjaUtama() { 
        $this->reset(['sub_kegiatan_id', 'selected_output']); 
        $this->isOpenKinerjaUtama = true; 
    }

    public function updatedSubKegiatanId($value) {
        $sub = SubKegiatan::find($value);
        $this->selected_output = $sub ? $sub->output : '';
    }

    public function storeKinerjaUtama() {
        $this->validate(['sub_kegiatan_id' => 'required', 'selected_output' => 'required']);

        $pkSasaran = PkSasaran::create([
            'perjanjian_kinerja_id' => $this->pk->id, 
            'sasaran' => $this->selected_output
        ]);

        $indikatorsAsli = IndikatorSubKegiatan::where('sub_kegiatan_id', $this->sub_kegiatan_id)->get();
        
        foreach ($indikatorsAsli as $indAsli) {
            PkIndikator::create([
                'pk_sasaran_id' => $pkSasaran->id,
                'nama_indikator' => $indAsli->keterangan ?? $indAsli->indikator,
                'satuan' => $indAsli->satuan,
                'arah' => 'Naik',
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
        $sasaran = PkSasaran::find($id);
        if($sasaran) { $sasaran->delete(); $this->loadData(); }
    }

    public function startEdit($id) {
        $this->editingIndikatorId = $id;
        $ind = PkIndikator::find($id);
        $colTarget = 'target_' . $this->pk->tahun;
        $this->editTargetValue = $ind->$colTarget;
    }

    public function saveEdit() {
        $this->validate(['editTargetValue' => 'required']);
        $ind = PkIndikator::find($this->editingIndikatorId);
        if ($ind) {
            $colTarget = 'target_' . $this->pk->tahun;
            $ind->forceFill([$colTarget => $this->editTargetValue])->save();
        }
        $this->editingIndikatorId = null;
        $this->loadData();
    }

    public function cancelEdit() { $this->editingIndikatorId = null; }

    public function deleteIndikator($id) {
        $ind = PkIndikator::find($id);
        if($ind) { $ind->delete(); $this->loadData(); }
    }

    public function openModalAnggaran() {
        $this->reset(['anggaran_sub_kegiatan_id', 'anggaran_nilai']);
        $this->isOpenAnggaran = true;
    }

    public function storeAnggaran() {
        $this->validate(['anggaran_sub_kegiatan_id' => 'required', 'anggaran_nilai' => 'required|numeric|min:0']);
        PkAnggaran::create([
            'perjanjian_kinerja_id' => $this->pk->id,
            'sub_kegiatan_id' => $this->anggaran_sub_kegiatan_id,
            'anggaran' => $this->anggaran_nilai
        ]);
        $this->loadData();
        $this->closeModal();
    }

    public function deleteAnggaran($id) {
        $ang = PkAnggaran::find($id);
        if($ang) { $ang->delete(); $this->loadData(); }
    }

    public function closeModal() {
        $this->isOpenKinerjaUtama = false;
        $this->isOpenAnggaran = false;
        $this->isOpenAturTarget = false;
        $this->isOpenVerifikasi = false;
    }

    public function deletePk() {
        if ($this->pk->status_verifikasi == 'disetujui') return;
        $jabatanId = $this->pk->jabatan_id;
        $this->pk->delete();
        return redirect()->route('perjanjian.kinerja.detail', $jabatanId);
    }

    public function render()
    {
        $sub_kegiatans = SubKegiatan::orderBy('created_at', 'desc')->get();
        return view('livewire.perjanjian-kinerja-lihat', ['sub_kegiatans' => $sub_kegiatans]);
    }
}