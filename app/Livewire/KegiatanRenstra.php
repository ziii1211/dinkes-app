<?php

namespace App\Livewire;

use App\Models\IndikatorKegiatan;
use App\Models\Jabatan;
use App\Models\Kegiatan;
use App\Models\Program;
use Livewire\Component;

class KegiatanRenstra extends Component
{
    public $program;

    // --- STATES MODAL ---
    public $isOpen = false;

    public $isOpenOutput = false;

    public $isOpenIndikator = false;

    public $isOpenTarget = false;

    public $isOpenPJ = false;

    public $isEditMode = false;

    // --- FORM VARIABLES ---
    public $kegiatan_id;

    public $kode;

    public $nama;

    public $output;

    public $pj_kegiatan_text;

    public $pj_jabatan_id;

    public $indikator_id;

    public $selected_kegiatan_id;

    public $ind_keterangan;

    public $ind_satuan;

    public $target_2025;

    public $target_2026;

    public $target_2027;

    public $target_2028;

    public $target_2029;

    public $target_2030;

    public $target_satuan;

    public function mount($id)
    {
        $this->program = Program::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.kegiatan-renstra', [
            // --- PERBAIKAN DI SINI ---
            // Mengubah sorting dari 'kode' menjadi 'id' 'asc'.
            // Ini memastikan data yang baru diinput (ID lebih besar) akan selalu berada di paling bawah.
            'kegiatans' => Kegiatan::with(['indikators', 'jabatan.pegawai'])
                ->where('program_id', $this->program->id)
                ->orderBy('id', 'asc') // UBAH KE 'id' AGAR URUT BERDASARKAN INPUT
                ->get(),
            'jabatans' => Jabatan::all(),
        ]);
    }

    // --- FUNGSI NAVIGASI KE SUB KEGIATAN (PENTING) ---
    public function openSubKegiatan($kegiatanId)
    {
        return redirect()->route('matrik.subkegiatan', [
            'programId' => $this->program->id,
            'kegiatanId' => $kegiatanId,
        ]);
    }

    // --- CRUD FUNCTIONS ---

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isOpenOutput = false;
        $this->isOpenIndikator = false;
        $this->isOpenTarget = false;
        $this->isOpenPJ = false;
        $this->resetValidation();
        $this->reset([
            'kegiatan_id', 'kode', 'nama', 'output', 'isEditMode', 
            'ind_keterangan', 'ind_satuan', 'indikator_id', 'selected_kegiatan_id', 
            'target_2025', 'target_2026', 'target_2027', 'target_2028', 'target_2029', 'target_2030', 
            'target_satuan', 'pj_kegiatan_text', 'pj_jabatan_id'
        ]);
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function create()
    {
        $this->reset(['kode', 'nama', 'output', 'isEditMode']);
        $this->openModal();
    }

    public function store()
    {
        $this->validate(['kode' => 'required', 'nama' => 'required']);
        if ($this->isEditMode) {
            Kegiatan::find($this->kegiatan_id)->update([
                'kode' => $this->kode, 
                'nama' => $this->nama, 
                'output' => $this->output
            ]);
        } else {
            Kegiatan::create([
                'program_id' => $this->program->id, 
                'kode' => $this->kode, 
                'nama' => $this->nama, 
                'output' => $this->output
            ]);
        } 
        $this->closeModal();
    }

    public function edit($id)
    {
        $data = Kegiatan::find($id);
        if ($data) {
            $this->kegiatan_id = $id;
            $this->kode = $data->kode;
            $this->nama = $data->nama;
            $this->output = $data->output;
            $this->isEditMode = true;
            $this->openModal();
        }
    }

    public function delete($id)
    {
        $data = Kegiatan::find($id);
        if ($data) {
            $data->delete();
        }
    }

    public function tambahOutput($id)
    {
        $data = Kegiatan::find($id);
        if ($data) {
            $this->kegiatan_id = $id;
            $this->output = $data->output;
            $this->isOpenOutput = true;
        }
    }

    public function editOutput($id)
    {
        $this->tambahOutput($id);
    }

    public function storeOutput()
    {
        $this->validate(['output' => 'required']);
        $kegiatan = Kegiatan::find($this->kegiatan_id);
        if ($kegiatan) {
            $kegiatan->update(['output' => $this->output]);
        } 
        $this->closeModal();
    }

    public function hapusOutput($id)
    {
        $kegiatan = Kegiatan::find($id);
        if ($kegiatan) {
            $kegiatan->update(['output' => null]);
        }
    }

    public function pilihPenanggungJawab($id)
    {
        $data = Kegiatan::find($id);
        if ($data) {
            $this->kegiatan_id = $id;
            $this->pj_kegiatan_text = $data->output ?? $data->nama;
            $this->pj_jabatan_id = $data->jabatan_id;
            $this->isOpenPJ = true;
        }
    }

    public function simpanPenanggungJawab()
    {
        $data = Kegiatan::find($this->kegiatan_id);
        if ($data) {
            $data->update(['jabatan_id' => $this->pj_jabatan_id ?: null]);
        } 
        $this->closeModal();
    }

    public function tambahIndikator($kegiatanId)
    {
        $this->reset(['ind_keterangan', 'ind_satuan', 'isEditMode']);
        $this->selected_kegiatan_id = $kegiatanId;
        $this->isOpenIndikator = true;
    }

    public function editIndikator($id)
    {
        $ind = IndikatorKegiatan::find($id);
        if ($ind) {
            $this->indikator_id = $id;
            $this->selected_kegiatan_id = $ind->kegiatan_id;
            $this->ind_keterangan = $ind->keterangan;
            $this->ind_satuan = $ind->satuan;
            $this->isEditMode = true;
            $this->isOpenIndikator = true;
        }
    }

    public function storeIndikator()
    {
        $this->validate(['ind_keterangan' => 'required', 'ind_satuan' => 'required']);
        $data = [
            'kegiatan_id' => $this->selected_kegiatan_id, 
            'keterangan' => $this->ind_keterangan, 
            'satuan' => $this->ind_satuan
        ];
        
        if ($this->isEditMode) {
            IndikatorKegiatan::find($this->indikator_id)->update($data);
        } else {
            IndikatorKegiatan::create($data);
        } 
        $this->closeModal();
    }

    public function deleteIndikator($id)
    {
        $ind = IndikatorKegiatan::find($id);
        if ($ind) {
            $ind->delete();
        }
    }

    public function aturTarget($id)
    {
        $ind = IndikatorKegiatan::find($id);
        if ($ind) {
            $this->indikator_id = $id;
            $this->target_2025 = $ind->target_2025;
            $this->target_2026 = $ind->target_2026;
            $this->target_2027 = $ind->target_2027;
            $this->target_2028 = $ind->target_2028;
            $this->target_2029 = $ind->target_2029;
            $this->target_2030 = $ind->target_2030;
            $this->target_satuan = $ind->satuan;
            $this->isOpenTarget = true;
        }
    }

    public function simpanTarget()
    {
        $ind = IndikatorKegiatan::find($this->indikator_id);
        if ($ind) {
            $ind->update([
                'target_2025' => $this->target_2025, 
                'target_2026' => $this->target_2026, 
                'target_2027' => $this->target_2027, 
                'target_2028' => $this->target_2028, 
                'target_2029' => $this->target_2029, 
                'target_2030' => $this->target_2030
            ]);
        } 
        $this->closeModal();
    }
}