<?php

namespace App\Livewire;

use App\Models\IndikatorKegiatan;
use App\Models\Jabatan;
use App\Models\Kegiatan;
use App\Models\OutputKegiatan; // Pastikan Model ini sudah dibuat
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
    
    // Variable untuk Output
    public $output; // Digunakan sebagai deskripsi output di form
    public $output_id; // ID untuk OutputKegiatan (saat edit)

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
    public $selected_output_id;

    public function mount($id)
    {
        $this->program = Program::findOrFail($id);
    }

    public function render()
{
    return view('livewire.kegiatan-renstra', [
        'program' => $this->program,
        
        // UPDATE DI SINI:
        // Gunakan 'outputs.indikators' agar indikator terambil bersarang di dalam output
        'kegiatans' => Kegiatan::with(['outputs.indikators', 'jabatan.pegawai'])
            ->where('program_id', $this->program->id)
            ->orderBy('id', 'asc')
            ->get(),
            
        'jabatans' => Jabatan::all(),
    ]);
}

    // --- FUNGSI NAVIGASI KE SUB KEGIATAN ---
    public function openSubKegiatan($kegiatanId)
    {
        return redirect()->route('renstra.sub_kegiatan', [
            'id' => $kegiatanId,
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
            'kegiatan_id', 'kode', 'nama', 'isEditMode', 
            'output', 'output_id', // Reset variabel output
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
        $this->reset(['kode', 'nama', 'isEditMode']);
        $this->openModal();
    }

    // Simpan Kegiatan Utama (Tanpa Output, karena Output tabel terpisah)
    public function store()
    {
        $this->validate(['kode' => 'required', 'nama' => 'required']);
        
        if ($this->isEditMode) {
            Kegiatan::find($this->kegiatan_id)->update([
                'kode' => $this->kode, 
                'nama' => $this->nama
                // Output tidak disimpan di sini lagi
            ]);
        } else {
            Kegiatan::create([
                'program_id' => $this->program->id, 
                'kode' => $this->kode, 
                'nama' => $this->nama
            ]);
        } 
        $this->closeModal();

        return redirect(request()->header('Referer'));
    }

    public function edit($id)
    {
        $data = Kegiatan::find($id);
        if ($data) {
            $this->kegiatan_id = $id;
            $this->kode = $data->kode;
            $this->nama = $data->nama;
            // $this->output = $data->output; // Hapus baris ini karena output sudah di tabel lain
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
        
        return redirect(request()->header('Referer'));
    }

    // --- LOGIKA BARU UNTUK OUTPUT (TABEL TERPISAH) ---

    public function tambahOutput($kegiatanId)
    {
        // Reset form output
        $this->reset(['output', 'output_id']);
        $this->kegiatan_id = $kegiatanId; // Set Parent ID
        $this->isOpenOutput = true;
    }

    public function editOutput($id)
    {
        // $id di sini adalah ID dari OutputKegiatan, bukan Kegiatan
        $data = OutputKegiatan::find($id);
        if ($data) {
            $this->output_id = $id;
            $this->kegiatan_id = $data->kegiatan_id;
            $this->output = $data->deskripsi; // Asumsi nama kolom di tabel baru adalah 'deskripsi'
            $this->isOpenOutput = true;
        }
    }

    public function storeOutput()
    {
        $this->validate(['output' => 'required']);

        if ($this->output_id) {
            // Update Existing Output
            $data = OutputKegiatan::find($this->output_id);
            if ($data) {
                $data->update(['deskripsi' => $this->output]);
            }
        } else {
            // Create New Output
            OutputKegiatan::create([
                'kegiatan_id' => $this->kegiatan_id,
                'deskripsi' => $this->output
            ]);
        }
        
        $this->closeModal();
        return redirect(request()->header('Referer'));
    }

    public function hapusOutput($id)
    {
        // Delete OutputKegiatan
        $data = OutputKegiatan::find($id);
        if ($data) {
            $data->delete();
        }

        return redirect(request()->header('Referer'));
    }

    // --- FUNGSI PJ, INDIKATOR, DLL TETAP SAMA ---

    public function pilihPenanggungJawab($id)
    {
        $data = Kegiatan::find($id);
        if ($data) {
            $this->kegiatan_id = $id;
            // Mengambil deskripsi output pertama jika ada, atau nama kegiatan
            $outputDesc = $data->outputs->first()->deskripsi ?? '-';
            $this->pj_kegiatan_text = $data->nama . ' (Output: ' . $outputDesc . ')';
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

        return redirect(request()->header('Referer'));
    }

    public function tambahIndikator($outputId) // Parameter sekarang Output ID
    {
        $this->reset(['ind_keterangan', 'ind_satuan', 'isEditMode']);
        $this->selected_output_id = $outputId; // Simpan ID Output
        $this->isOpenIndikator = true;
    }

    public function editIndikator($id)
    {
        $ind = IndikatorKegiatan::find($id);
        if ($ind) {
            $this->indikator_id = $id;
            $this->selected_output_id = $ind->output_kegiatan_id; // Ambil output_id
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
            'output_kegiatan_id' => $this->selected_output_id, // Gunakan ID Output
            'keterangan' => $this->ind_keterangan, 
            'satuan' => $this->ind_satuan
        ];
        
        if ($this->isEditMode) {
            IndikatorKegiatan::find($this->indikator_id)->update($data);
        } else {
            IndikatorKegiatan::create($data);
        } 
        
        $this->closeModal();
        return redirect(request()->header('Referer'));
    }   

    public function deleteIndikator($id)
    {
        $ind = IndikatorKegiatan::find($id);
        if ($ind) {
            $ind->delete();
        }

        return redirect(request()->header('Referer'));
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

        return redirect(request()->header('Referer'));
    }
}