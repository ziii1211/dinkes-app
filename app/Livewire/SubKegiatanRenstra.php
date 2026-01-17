<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Jabatan;
use App\Models\IndikatorSubKegiatan;

class SubKegiatanRenstra extends Component
{
    public $program;
    public $kegiatan;

    // --- FILTER STATES ---
    public $filter_outcome_id; 
    public $filter_output_id; // <--- INI KUNCI UTAMANYA

    // --- STATES MODAL ---
    public $isOpen = false;
    public $isOpenIndikator = false;
    public $isOpenTarget = false;
    public $isOpenPJ = false;
    public $isEditMode = false;

    // --- FORM SUB KEGIATAN ---
    public $sub_kegiatan_id, $kode, $nama, $output;
    
    // --- FORM PJ ---
    public $pj_sub_kegiatan_text, $pj_jabatan_id;

    // --- FORM INDIKATOR ---
    public $indikator_id, $selected_sub_kegiatan_id;
    public $ind_keterangan, $ind_satuan;

    // --- FORM TARGET & PAGU ---
    public $target_2025, $pagu_2025;
    public $target_2026, $pagu_2026;
    public $target_2027, $pagu_2027;
    public $target_2028, $pagu_2028;
    public $target_2029, $pagu_2029;
    public $target_2030, $pagu_2030;
    public $target_satuan;

    public function mount($id)
    {
        $this->kegiatan = Kegiatan::findOrFail($id);
        $this->program = Program::findOrFail($this->kegiatan->program_id);

        $this->filter_outcome_id = request()->query('outcome_id');
        $this->filter_output_id = request()->query('output_id');
    }

    // File: app/Livewire/SubKegiatanRenstra.php

public function render()
{
    // --- PERBAIKAN DI SINI ---
    // Daripada menggunakan $this->program->load(), kita query manual agar filter 'pasti' jalan
    // dan tidak terganggu oleh cache relasi di object $program
    $filteredOutcomes = $this->program->outcomes()
        ->when($this->filter_outcome_id, function($q) {
            $q->where('id', $this->filter_outcome_id);
        })
        ->get();

    // Filter Output (Info di tengah) - Jika ini sudah benar menurut Anda, biarkan load-nya
    $this->kegiatan->load(['outputs' => function($q) {
        if($this->filter_output_id) {
            $q->where('id', $this->filter_output_id);
        }
    }]);

    // Query Sub Kegiatan
    $querySub = SubKegiatan::with(['indikators', 'jabatan'])
        ->where('kegiatan_id', $this->kegiatan->id);

    if ($this->filter_output_id) {
        $querySub->where('output_kegiatan_id', $this->filter_output_id);
    }

    return view('livewire.sub-kegiatan-renstra', [
        'program' => $this->program,
        'outcomes_view' => $filteredOutcomes, // <--- Kirim variabel baru ini ke Blade
        'kegiatan' => $this->kegiatan,
        'sub_kegiatans' => $querySub->orderBy('id', 'asc')->get(),
        'jabatans' => Jabatan::all()
    ]);
}

    public function closeModal()
    {
        $this->isOpen = false; 
        $this->isOpenIndikator = false; 
        $this->isOpenTarget = false; 
        $this->isOpenPJ = false;
        
        $this->resetValidation();
        $this->reset([
            'sub_kegiatan_id', 'kode', 'nama', 'output', 'isEditMode', 
            'ind_keterangan', 'ind_satuan', 'indikator_id', 'selected_sub_kegiatan_id',
            'target_2025', 'pagu_2025', 'target_2026', 'pagu_2026', 'target_2027', 'pagu_2027',
            'target_2028', 'pagu_2028', 'target_2029', 'pagu_2029', 'target_2030', 'pagu_2030',
            'target_satuan', 'pj_sub_kegiatan_text', 'pj_jabatan_id'
        ]);
    }

    // --- CRUD SUB KEGIATAN ---
    public function openModal() { $this->isOpen = true; }
    
    public function create() { 
        $this->reset(['kode', 'nama', 'output', 'ind_keterangan', 'ind_satuan', 'isEditMode']); 
        $this->openModal(); 
    }

    public function store() {
        $this->validate([
            'kode' => 'required', 
            'nama' => 'required'
        ]);

        if ($this->isEditMode) {
            SubKegiatan::find($this->sub_kegiatan_id)->update([
                'kode' => $this->kode, 
                'nama' => $this->nama, 
                'output' => $this->output
                // Note: output_kegiatan_id biasanya tidak diubah saat edit kecuali perlu dipindah
            ]);
            
            // Notifikasi Modern Update
            $this->dispatch('alert', [
                'type' => 'success',
                'title' => 'Diperbarui',
                'message' => 'Data Sub Kegiatan berhasil diperbarui.'
            ]);

        } else {
            // --- PERBAIKAN DISINI: SIMPAN output_kegiatan_id ---
            $sub = SubKegiatan::create([
                'kegiatan_id' => $this->kegiatan->id,
                'output_kegiatan_id' => $this->filter_output_id, // <--- PENTING! Agar data terikat ke Output spesifik
                'kode' => $this->kode, 
                'nama' => $this->nama, 
                'output' => $this->output
            ]);

            if (!empty($this->ind_keterangan) && !empty($this->ind_satuan)) {
                IndikatorSubKegiatan::create([
                    'sub_kegiatan_id' => $sub->id,
                    'keterangan' => $this->ind_keterangan,
                    'satuan' => $this->ind_satuan
                ]);
            }

            // Notifikasi Modern Simpan
            $this->dispatch('alert', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Sub Kegiatan baru berhasil ditambahkan.'
            ]);
        }

        $this->closeModal();
        // return redirect(request()->header('Referer')); // Tidak perlu redirect jika pakai Livewire
    }

    public function edit($id) {
        $data = SubKegiatan::find($id);
        if ($data) {
            $this->sub_kegiatan_id = $id; 
            $this->kode = $data->kode; 
            $this->nama = $data->nama; 
            $this->output = $data->output;
            $this->isEditMode = true; 
            $this->openModal();
        }
    }

    public function delete($id) { 
        $data = SubKegiatan::find($id); 
        if ($data) {
            $data->delete(); 
            
            // Notifikasi Modern Hapus
            $this->dispatch('alert', [
                'type' => 'success',
                'title' => 'Terhapus',
                'message' => 'Data Sub Kegiatan telah dihapus.'
            ]);
        }
    }

    // --- PENANGGUNG JAWAB ---
    public function pilihPenanggungJawab($id) {
        $data = SubKegiatan::find($id);
        if ($data) {
            $this->sub_kegiatan_id = $id; 
            $this->pj_sub_kegiatan_text = $data->nama; 
            $this->pj_jabatan_id = $data->jabatan_id;
            $this->isOpenPJ = true;
        }
    }

    public function simpanPenanggungJawab() {
        $data = SubKegiatan::find($this->sub_kegiatan_id);
        if ($data) { 
            $data->update(['jabatan_id' => $this->pj_jabatan_id ?: null]); 
            
            $this->dispatch('alert', [
                'type' => 'success',
                'title' => 'Disimpan',
                'message' => 'Penanggung Jawab berhasil diupdate.'
            ]);
        }
        $this->closeModal();
    }

    // --- INDIKATOR ---
    public function tambahIndikator($subId) {
        $this->reset(['ind_keterangan', 'ind_satuan', 'isEditMode']);
        $this->selected_sub_kegiatan_id = $subId;
        $this->isOpenIndikator = true;
    }

    public function editIndikator($id) {
        $ind = IndikatorSubKegiatan::find($id);
        if ($ind) {
            $this->indikator_id = $id; 
            $this->selected_sub_kegiatan_id = $ind->sub_kegiatan_id;
            $this->ind_keterangan = $ind->keterangan; 
            $this->ind_satuan = $ind->satuan;
            $this->isEditMode = true; 
            $this->isOpenIndikator = true;
        }
    }

    public function storeIndikator() {
        $this->validate([
            'ind_keterangan' => 'required', 
            'ind_satuan' => 'required'
        ]);
        
        $data = [
            'sub_kegiatan_id' => $this->selected_sub_kegiatan_id, 
            'keterangan' => $this->ind_keterangan, 
            'satuan' => $this->ind_satuan
        ];

        if ($this->isEditMode) { 
            IndikatorSubKegiatan::find($this->indikator_id)->update($data);
            $pesan = 'Indikator diperbarui.';
        } else { 
            IndikatorSubKegiatan::create($data); 
            $pesan = 'Indikator ditambahkan.';
        }

        $this->dispatch('alert', ['type'=>'success', 'title'=>'Berhasil', 'message'=>$pesan]);
        $this->closeModal();
    }

    public function deleteIndikator($id) { 
        $ind = IndikatorSubKegiatan::find($id); 
        if ($ind) {
            $ind->delete(); 
            $this->dispatch('alert', ['type'=>'success', 'title'=>'Terhapus', 'message'=>'Indikator dihapus.']);
        }
    }

    // --- TARGET & PAGU ---
    public function aturTarget($id) {
        $ind = IndikatorSubKegiatan::find($id);
        if ($ind) {
            $this->indikator_id = $id;
            $this->target_satuan = $ind->satuan;
            foreach([2025,2026,2027,2028,2029,2030] as $y) {
                $this->{'target_'.$y} = $ind->{'target_'.$y};
                $this->{'pagu_'.$y} = $ind->{'pagu_'.$y};
            }
            $this->isOpenTarget = true;
        }
    }

    private function bersihkanAngka($nilai)
    {
        if (empty($nilai)) return 0;
        $bersih = str_replace('.', '', $nilai);
        $bersih = str_replace(',', '.', $bersih);
        return $bersih;
    }

    public function simpanTarget() {
        $ind = IndikatorSubKegiatan::find($this->indikator_id);
        if ($ind) {
            $data = [];
            foreach([2025,2026,2027,2028,2029,2030] as $y) {
                $data['target_'.$y] = $this->{'target_'.$y};
                $data['pagu_'.$y] = $this->bersihkanAngka($this->{'pagu_'.$y});
            }
            $ind->update($data);
            
            $this->dispatch('alert', ['type'=>'success', 'title'=>'Disimpan', 'message'=>'Target & Pagu berhasil disimpan.']);
        }
        $this->closeModal();
    }
}