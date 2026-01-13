<?php

namespace App\Livewire;

use App\Models\IndikatorKegiatan;
use App\Models\Jabatan;
use App\Models\Kegiatan;
use App\Models\OutputKegiatan;
use App\Models\Program;
use Livewire\Component;

class KegiatanRenstra extends Component
{
    public $program;
    
    // --- STATE FILTER ---
    // Tambahkan ini agar Livewire mengingat ID outcome yang dipilih
    public $filter_outcome_id; 

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
    public $output; // Deskripsi output
    public $output_id; // ID Output (untuk edit)

    // Variable Penting: Menyimpan ID Output yang sedang dipilih
    public $selected_output_id; 

    public $pj_kegiatan_text;
    public $pj_jabatan_id;

    public $indikator_id;
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
        // 1. Load Program saja (Relasi diload nanti di render agar filter konsisten)
        $this->program = Program::findOrFail($id);

        // 2. Tangkap parameter dari URL dan simpan ke public property
        $this->filter_outcome_id = request()->query('outcome_id');
    }

    public function render()
    {
        // Logic filter outcome (tetap sama)
        $this->program->load(['outcomes' => function($query) {
            if ($this->filter_outcome_id) {
                $query->where('id', $this->filter_outcome_id);
            }
        }]);

        // --- UPDATE QUERY KEGIATAN DI SINI ---
        // Kita mulai query
        $queryKegiatan = Kegiatan::with([
                'outputs.indikators', 
                'outputs.jabatan.pegawai'
            ])
            ->where('program_id', $this->program->id);

        // Filter Tambahan: Jika ada filter_outcome_id, ambil kegiatan yg outcome_id nya cocok
        if ($this->filter_outcome_id) {
            $queryKegiatan->where('outcome_id', $this->filter_outcome_id);
        }

        return view('livewire.kegiatan-renstra', [
            'program' => $this->program,
            'kegiatans' => $queryKegiatan->orderBy('id', 'asc')->get(), // Eksekusi query di sini
            'jabatans' => Jabatan::all(),
        ]);
    }

    // --- FUNGSI NAVIGASI ---
    public function openSubKegiatan($kegiatanId)
    {
        return redirect()->route('renstra.sub_kegiatan', ['id' => $kegiatanId]);
    }

    // --- MANAJEMEN MODAL & RESET ---
    public function closeModal()
    {
        $this->isOpen = false;
        $this->isOpenOutput = false;
        $this->isOpenIndikator = false;
        $this->isOpenTarget = false;
        $this->isOpenPJ = false;
        $this->resetValidation();
        
        // Reset semua variabel form
        $this->reset([
            'kegiatan_id', 'kode', 'nama', 'isEditMode', 
            'output', 'output_id', 'selected_output_id', 
            'ind_keterangan', 'ind_satuan', 'indikator_id', 
            'target_2025', 'target_2026', 'target_2027', 'target_2028', 'target_2029', 'target_2030', 
            'target_satuan', 'pj_kegiatan_text', 'pj_jabatan_id'
        ]);
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    // --- CRUD KEGIATAN ---
    public function create()
    {
        // Panggil closeModal dulu untuk memastikan state bersih sebelum tambah baru
        $this->closeModal(); 
        $this->reset(['kode', 'nama', 'isEditMode']);
        $this->openModal();
    }

    public function store()
    {
        $this->validate(['kode' => 'required', 'nama' => 'required']);
        
        if ($this->isEditMode) {
            // Update Data
            Kegiatan::find($this->kegiatan_id)->update([
                'kode' => $this->kode, 
                'nama' => $this->nama
                // Saat edit, outcome_id biasanya tidak diubah, jadi biarkan saja
            ]);
        } else {
            // Buat Data Baru
            Kegiatan::create([
                'program_id' => $this->program->id, 
                
                // --- UPDATE DI SINI ---
                // Masukkan ID outcome yang sedang dipilih (dari URL) ke database
                'outcome_id' => $this->filter_outcome_id, 
                
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
            $this->isEditMode = true;
            $this->openModal();
        }
    }

    public function delete($id)
    {
        $data = Kegiatan::find($id);
        if ($data) $data->delete();
        return redirect(request()->header('Referer'));
    }

    // --- CRUD OUTPUT (Tabel output_kegiatans) ---
    public function tambahOutput($kegiatanId)
    {
        $this->closeModal(); // Reset dulu
        $this->reset(['output', 'output_id']);
        $this->kegiatan_id = $kegiatanId; // Induknya adalah Kegiatan
        $this->isOpenOutput = true;
    }

    public function editOutput($id)
    {
        $data = OutputKegiatan::find($id);
        if ($data) {
            $this->output_id = $id;
            $this->kegiatan_id = $data->kegiatan_id;
            $this->output = $data->deskripsi;
            $this->isOpenOutput = true;
        }
    }

    public function storeOutput()
    {
        $this->validate(['output' => 'required']);

        if ($this->output_id) {
            OutputKegiatan::find($this->output_id)->update(['deskripsi' => $this->output]);
        } else {
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
        $data = OutputKegiatan::find($id);
        if ($data) $data->delete();
        return redirect(request()->header('Referer'));
    }

    // --- CRUD INDIKATOR (Anak dari Output) ---
    public function tambahIndikator($outputId)
    {
        $this->closeModal(); // Reset dulu
        $this->reset(['ind_keterangan', 'ind_satuan', 'isEditMode']);
        $this->selected_output_id = $outputId; // Induknya adalah Output
        $this->isOpenIndikator = true;
    }

    public function editIndikator($id)
    {
        $ind = IndikatorKegiatan::find($id);
        if ($ind) {
            $this->indikator_id = $id;
            $this->selected_output_id = $ind->output_kegiatan_id; // Set ID Output induk
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
        if ($ind) $ind->delete();
        return redirect(request()->header('Referer'));
    }

    // --- FITUR PENANGGUNG JAWAB (PER OUTPUT) ---
    public function pilihPenanggungJawab($outputId) // Menerima ID Output
    {
        $output = OutputKegiatan::find($outputId);
        if ($output) {
            $this->selected_output_id = $outputId; // Simpan ID agar tidak hilang saat simpan
            $this->pj_kegiatan_text = "Output: " . $output->deskripsi;
            $this->pj_jabatan_id = $output->jabatan_id;
            $this->isOpenPJ = true;
        }
    }

    public function simpanPenanggungJawab()
    {
        // Cari Output berdasarkan ID yang disimpan
        $output = OutputKegiatan::find($this->selected_output_id);
        if ($output) {
            $output->update(['jabatan_id' => $this->pj_jabatan_id ?: null]);
        } 
        $this->closeModal();
        return redirect(request()->header('Referer'));
    }

    // --- FITUR TARGET TAHUNAN ---
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