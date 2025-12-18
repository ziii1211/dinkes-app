<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PerjanjianKinerja;
use App\Models\PkSasaran;
use App\Models\PkIndikator;
use App\Models\PkAnggaran;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan; // IMPORT MODEL KEGIATAN
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

    // --- STATES MODAL ---
    public $isOpenKinerjaUtama = false;
    public $isOpenAnggaran = false;
    public $isOpenEditTarget = false; // [BARU] Modal Edit Target
    
    // --- FORM PROPERTIES ---
    public $sumber_kinerja_id; 
    
    // --- FORM EDIT TARGET [BARU] ---
    public $edit_indikator_id;
    public $edit_target_nilai;
    
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
        $this->reset(['sumber_kinerja_id']); 
        $this->isOpenKinerjaUtama = true; 
    }

    public function storeKinerjaUtama() {
        if (!$this->canEdit()) return;

        $this->validate([
            'sumber_kinerja_id' => 'required', 
        ]);

        // [LOGIKA BARU] Memecah string "tipe:id"
        $parts = explode(':', $this->sumber_kinerja_id);
        $tipeSumber = $parts[0]; 
        $idSumber = $parts[1];

        $textSasaran = '';
        $indikators = [];

        if ($tipeSumber == 'sasaran') {
            $sumber = Sasaran::with('indikators')->find($idSumber);
            if (!$sumber) return;
            $textSasaran = $sumber->sasaran;
            $indikators = $sumber->indikators;

        } elseif ($tipeSumber == 'outcome') {
            $sumber = Outcome::with('indikators')->find($idSumber);
            if (!$sumber) return;
            $textSasaran = $sumber->outcome;
            $indikators = $sumber->indikators;

        } elseif ($tipeSumber == 'kegiatan') {
            $sumber = Kegiatan::with('indikators')->find($idSumber);
            if (!$sumber) return;
            $textSasaran = $sumber->output; 
            $indikators = $sumber->indikators; 
        }

        $pkSasaran = PkSasaran::create([
            'perjanjian_kinerja_id' => $this->pk->id, 
            'sasaran' => $textSasaran 
        ]);

        foreach ($indikators as $indAsli) {
            PkIndikator::create([
                'pk_sasaran_id' => $pkSasaran->id,
                'nama_indikator' => $indAsli->keterangan ?? $indAsli->nama_indikator ?? '-',
                'satuan' => $indAsli->satuan,
                'arah' => $indAsli->arah ?? 'Naik', 
                
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

    // --- [BARU] EDIT TARGET ---
    public function editTarget($id) {
        if (!$this->canEdit()) return;
        
        $ind = PkIndikator::find($id);
        if ($ind) {
            $this->edit_indikator_id = $id;
            
            // Ambil target spesifik tahun PK ini
            $col = 'target_' . $this->pk->tahun;
            $this->edit_target_nilai = $ind->$col;
            
            $this->isOpenEditTarget = true;
        }
    }

    public function updateTarget() {
        if (!$this->canEdit()) return;
        
        $this->validate(['edit_target_nilai' => 'required']);

        $ind = PkIndikator::find($this->edit_indikator_id);
        if ($ind) {
            // Update kolom tahun yang sesuai
            $col = 'target_' . $this->pk->tahun;
            
            $ind->update([
                $col => $this->edit_target_nilai
            ]);
        }
        
        $this->loadData();
        $this->closeModal();
        session()->flash('message', 'Target berhasil diperbarui.');
    }

    public function deleteKinerjaUtama($id) {
        if (!$this->canEdit()) return;
        $sasaran = PkSasaran::find($id);
        if($sasaran) { $sasaran->delete(); $this->loadData(); }
    }

    public function deleteIndikator($id) {
        if (!$this->canEdit()) return;
        $ind = PkIndikator::find($id);
        if($ind) { $ind->delete(); $this->loadData(); }
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
        if($ang) { $ang->delete(); $this->loadData(); }
    }

    public function closeModal() {
        $this->isOpenKinerjaUtama = false;
        $this->isOpenAnggaran = false;
        $this->isOpenEditTarget = false; // Reset Modal Target
        $this->resetValidation();
        $this->reset(['edit_indikator_id', 'edit_target_nilai']); // Reset Form Target
    }

    public function deletePk() {
        if (!$this->canEdit()) return;
        $jabatanId = $this->pk->jabatan_id;
        $this->pk->delete();
        return redirect()->route('perjanjian.kinerja.detail', $jabatanId);
    }

    public function render()
    {
        // [LOGIKA BARU] MENGGABUNGKAN DATA UTK DROPDOWN
        $list_sumber = collect();

        if ($this->is_kepala_dinas) {
            $data = Sasaran::with('indikators')->orderBy('created_at', 'desc')->get();
            foreach($data as $item) {
                $list_sumber->push([
                    'value' => 'sasaran:' . $item->id, 
                    'label' => '[Sasaran Strategis] ' . $item->sasaran
                ]);
            }
        } else {
            // 1. OUTCOME
            $outcomes = Outcome::with('indikators')
                        ->where('jabatan_id', $this->pk->jabatan_id)
                        ->orderBy('created_at', 'desc')->get();
            foreach($outcomes as $o) {
                $list_sumber->push([
                    'value' => 'outcome:' . $o->id,
                    'label' => '[Outcome Program] ' . $o->outcome
                ]);
            }

            // 2. KEGIATAN (OUTPUT)
            $kegiatans = Kegiatan::with('indikators')
                        ->where('jabatan_id', $this->pk->jabatan_id)
                        ->whereNotNull('output') 
                        ->orderBy('kode', 'asc')->get();
            foreach($kegiatans as $k) {
                $list_sumber->push([
                    'value' => 'kegiatan:' . $k->id,
                    'label' => '[Output Kegiatan] ' . $k->output . ' (' . $k->kode . ')'
                ]);
            }
        }

        $sub_kegiatans = SubKegiatan::orderBy('created_at', 'desc')->get();
        
        return view('livewire.perjanjian-kinerja-lihat', [
            'list_sumber' => $list_sumber, 
            'sub_kegiatans' => $sub_kegiatans
        ]);
    }
}