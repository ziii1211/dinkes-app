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
use App\Models\Kegiatan; 
use App\Models\SubKegiatan;
use App\Models\Program; 
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
    public $isOpenEditTarget = false; 
    
    // --- FORM PROPERTIES ---
    public $sumber_kinerja_id; 
    
    // --- FORM EDIT TARGET ---
    public $edit_indikator_id;
    public $edit_target_nilai;
    
    // --- FORM ANGGARAN ---
    public $anggaran_pilihan_id; 
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
        
        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
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

        session()->flash('message', 'Kinerja Utama berhasil ditambahkan.');
        
        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function editTarget($id) {
        if (!$this->canEdit()) return;
        $ind = PkIndikator::find($id);
        if ($ind) {
            $this->edit_indikator_id = $id;
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
            $col = 'target_' . $this->pk->tahun;
            $ind->update([$col => $this->edit_target_nilai]);
        }
        session()->flash('message', 'Target berhasil diperbarui.');
        
        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function deleteKinerjaUtama($id) {
        if (!$this->canEdit()) return;
        $sasaran = PkSasaran::find($id);
        if($sasaran) { $sasaran->delete(); }
        
        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function deleteIndikator($id) {
        if (!$this->canEdit()) return;
        $ind = PkIndikator::find($id);
        if($ind) { $ind->delete(); }
        
        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // =================================================================
    // FITUR ANGGARAN
    // =================================================================

    public function openModalAnggaran() {
        if (!$this->canEdit()) return;
        $this->reset(['anggaran_pilihan_id', 'anggaran_nilai']);
        $this->isOpenAnggaran = true;
    }

    public function storeAnggaran() {
        if (!$this->canEdit()) return;
        
        $this->validate([
            'anggaran_pilihan_id' => 'required', 
            'anggaran_nilai' => 'required|numeric|min:0'
        ]);

        $parts = explode(':', $this->anggaran_pilihan_id);
        $tipe = $parts[0];
        $id = $parts[1];

        $sub_kegiatan_id = null;
        $nama_program_kegiatan = '-';

        // [MODIFIKASI] Mengambil Kode dan Nama untuk disimpan
        if ($tipe == 'program') {
            $prog = Program::find($id);
            if($prog) {
                // Simpan format: "1.01.01 Nama Program"
                $nama_program_kegiatan = $prog->kode . ' ' . $prog->nama;
                $sub_kegiatan_id = null;
            }
        } elseif ($tipe == 'kegiatan') {
            $keg = Kegiatan::find($id);
            if($keg) {
                // Simpan format: "1.01.01.2.01 Nama Kegiatan"
                $nama_program_kegiatan = $keg->kode . ' ' . $keg->nama;
                $sub_kegiatan_id = null;
            }
        } elseif ($tipe == 'sub') {
            $sub = SubKegiatan::find($id);
            if($sub) {
                $nama_program_kegiatan = $sub->nama;
                $sub_kegiatan_id = $sub->id;
            }
        }

        PkAnggaran::create([
            'perjanjian_kinerja_id' => $this->pk->id,
            'sub_kegiatan_id' => $sub_kegiatan_id,
            'nama_program_kegiatan' => $nama_program_kegiatan,
            'anggaran' => $this->anggaran_nilai
        ]);

        session()->flash('message', 'Anggaran berhasil ditambahkan.');
        
        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function deleteAnggaran($id) {
        if (!$this->canEdit()) return;
        $ang = PkAnggaran::find($id);
        if($ang) { $ang->delete(); }

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function closeModal() {
        $this->isOpenKinerjaUtama = false;
        $this->isOpenAnggaran = false;
        $this->isOpenEditTarget = false; 
        $this->resetValidation();
        $this->reset(['edit_indikator_id', 'edit_target_nilai']); 
    }

    public function deletePk() {
        if (!$this->canEdit()) return;
        $jabatanId = $this->pk->jabatan_id;
        $this->pk->delete();
        
        // Redirect ke halaman detail (list PK)
        return redirect()->route('perjanjian.kinerja.detail', $jabatanId);
    }

    public function render()
    {
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
            $outcomes = Outcome::with('indikators')
                        ->where('jabatan_id', $this->pk->jabatan_id)
                        ->orderBy('created_at', 'desc')->get();
            foreach($outcomes as $o) {
                $list_sumber->push([
                    'value' => 'outcome:' . $o->id,
                    'label' => '[Outcome Program] ' . $o->outcome
                ]);
            }

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

        $programs = Program::orderBy('kode', 'asc')->get();
        $kegiatans_dropdown = Kegiatan::orderBy('kode', 'asc')->get();
        $sub_kegiatans_dropdown = SubKegiatan::orderBy('kode', 'asc')->get();
        
        return view('livewire.perjanjian-kinerja-lihat', [
            'list_sumber' => $list_sumber, 
            'programs_dropdown' => $programs,
            'kegiatans_dropdown' => $kegiatans_dropdown,
            'sub_kegiatans_dropdown' => $sub_kegiatans_dropdown
        ]);
    }
}