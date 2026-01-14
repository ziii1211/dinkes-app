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
use App\Models\OutputKegiatan; 
use Barryvdh\DomPDF\Facade\Pdf;

class PerjanjianKinerjaLihat extends Component
{
    public $pkId;
    public $pk; // Variabel utama untuk data PerjanjianKinerja
    
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

        // LOGIKA CARI ATASAN (Untuk Pihak Pertama di PDF)
        if ($this->jabatan->parent_id) {
            $parentJabatan = Jabatan::find($this->jabatan->parent_id);
            if ($parentJabatan) {
                $this->atasan_jabatan = $parentJabatan;
                $this->atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
            }
        }
    }

    // --- FUNGSI CETAK PDF ---
    public function cetak()
    {
        $data = [
            'pk' => $this->pk,
            'jabatan' => $this->jabatan,
            'pegawai' => $this->pegawai,
            'atasan_jabatan' => $this->atasan_jabatan,
            'atasan_pegawai' => $this->atasan_pegawai,
            'is_kepala_dinas' => $this->is_kepala_dinas
        ];

        $pdf = Pdf::loadView('cetak.perjanjian-kinerja', $data);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Perjanjian_Kinerja_' . $this->jabatan->nama . '_' . $this->pk->tahun . '.pdf');
    }

    public function canEdit()
    {
        return Auth::user()->hasRole('admin');
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

        } elseif ($tipeSumber == 'output') { 
            // --- PERBAIKAN LOGIC: LANGSUNG CARI OUTPUT BERDASARKAN ID UNIKNYA ---
            $sumber = OutputKegiatan::with('indikators')->find($idSumber);
            
            if (!$sumber) return;
            
            // Ambil deskripsi output sebagai sasaran
            $textSasaran = $sumber->deskripsi;
            // Ambil indikator yang melekat pada output tersebut
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
        
        return redirect(request()->header('Referer'));
    }

    public function deleteKinerjaUtama($id) {
        if (!$this->canEdit()) return;
        $sasaran = PkSasaran::find($id);
        if($sasaran) { $sasaran->delete(); }
        
        return redirect(request()->header('Referer'));
    }

    public function deleteIndikator($id) {
        if (!$this->canEdit()) return;
        $ind = PkIndikator::find($id);
        if($ind) { $ind->delete(); }
        
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

        if ($tipe == 'program') {
            $prog = Program::find($id);
            if($prog) {
                $nama_program_kegiatan = $prog->kode . ' ' . $prog->nama;
                $sub_kegiatan_id = null;
            }
        } elseif ($tipe == 'kegiatan') {
            $keg = Kegiatan::find($id);
            if($keg) {
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
        
        return redirect(request()->header('Referer'));
    }

    public function deleteAnggaran($id) {
        if (!$this->canEdit()) return;
        $ang = PkAnggaran::find($id);
        if($ang) { $ang->delete(); }

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
        
        return redirect()->route('perjanjian.kinerja.detail', $jabatanId);
    }

    public function render()
    {
        $jabatanId = $this->pk->jabatan_id;

        $kegiatans = Kegiatan::with(['outputs.indikators', 'outputs.jabatan'])
            ->whereHas('outputs', function ($query) use ($jabatanId) {
                $query->where('jabatan_id', $jabatanId);
            })
            ->orderBy('kode', 'asc')
            ->get();

        $list_sumber = collect();
        
        // A. SASARAN STRATEGIS
        if ($this->is_kepala_dinas) {
            Sasaran::all()->each(function($s) use ($list_sumber) {
                $list_sumber->push(['id' => 'sasaran:'.$s->id, 'label' => '[SASARAN] '.$s->sasaran]);
            });
        }

        // B. OUTCOME PROGRAM
        Outcome::with('program')
            ->where('jabatan_id', $jabatanId) 
            ->get()
            ->each(function($o) use ($list_sumber) {
                $prog = $o->program ? $o->program->nama : 'Non-Program';
                $list_sumber->push(['id' => 'outcome:'.$o->id, 'label' => '[OUTCOME] '.$prog.' - '.$o->outcome]);
            });

        // C. KEGIATAN / OUTPUT (PERBAIKAN DI SINI)
        // Ambil semua output milik jabatan ini
        $outputs = OutputKegiatan::with('kegiatan')
            ->where('jabatan_id', $jabatanId)
            ->get();

        foreach($outputs as $out) {
            if($out->kegiatan) {
                // Label tetap informatif ada nama kegiatannya
                $label = '[KEGIATAN] ' . $out->kegiatan->nama . ' - (Output: ' . \Illuminate\Support\Str::limit($out->deskripsi, 40) . ')';
                
                // PERBAIKAN: Gunakan ID Output langsung (DNA Unik)
                $list_sumber->push([
                    'id' => 'output:' . $out->id, // <--- INI KUNCINYA
                    'label' => $label
                ]);
            }
        }

        $programs_dropdown = Program::all();
        $kegiatans_dropdown = Kegiatan::all();
        $sub_kegiatans_dropdown = SubKegiatan::all();

        return view('livewire.perjanjian-kinerja-lihat', [
            'kegiatans' => $kegiatans,
            'list_sumber' => $list_sumber,
            'programs_dropdown' => $programs_dropdown,
            'kegiatans_dropdown' => $kegiatans_dropdown,
            'sub_kegiatans_dropdown' => $sub_kegiatans_dropdown,
        ]);
    }
}