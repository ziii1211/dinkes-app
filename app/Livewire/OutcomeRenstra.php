<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outcome;
use App\Models\Sasaran;
use App\Models\Jabatan;
use App\Models\IndikatorOutcome;
use App\Models\Pegawai; // <--- TAMBAHKAN INI

class OutcomeRenstra extends Component
{
    // --- STATES MODAL ---
    public $isOpen = false;          // Modal Outcome
    public $isOpenPJ = false;        // Modal Penanggung Jawab
    public $isOpenIndikator = false; // Modal Indikator
    public $isOpenTarget = false;    // Modal Target
    
    public $isEditMode = false;

    // --- FORM OUTCOME ---
    public $outcome_id, $sasaran_id, $outcome;

    // --- FORM INDIKATOR ---
    public $indikator_id, $selected_outcome_id;
    public $ind_keterangan, $ind_satuan, $ind_arah; 

    // --- FORM PJ ---
    public $pj_outcome_text, $pj_jabatan_id;

    // --- FORM TARGET ---
    public $target_2025, $target_2026, $target_2027, $target_2028, $target_2029, $target_2030;
    public $target_satuan;

    public function render()
    {
        return view('livewire.outcome-renstra', [
            // PERBAIKAN DISINI:
            // Menambahkan 'jabatan.pegawai' agar status (Definitif/PLT) terbaca di View
            'outcomes' => Outcome::with(['indikators', 'sasaran', 'jabatan.pegawai'])
                        ->orderBy('id', 'asc')
                        ->get(),
            
            'sasarans' => Sasaran::all(),
            'jabatans' => Jabatan::all()
        ]);
    }

    // --- RESET & CLOSE ---
    public function closeModal() { 
        $this->isOpen = false; 
        $this->isOpenPJ = false;
        $this->isOpenIndikator = false; 
        $this->isOpenTarget = false;
        $this->resetValidation();
        $this->reset([
            'outcome', 'outcome_id', 'sasaran_id', 'isEditMode', 
            'pj_outcome_text', 'pj_jabatan_id',
            'ind_keterangan', 'ind_satuan', 'ind_arah', 'indikator_id', 'selected_outcome_id', 
            'target_2025', 'target_2026', 'target_2027', 'target_2028', 'target_2029', 'target_2030', 
            'target_satuan'
        ]);
    }

    // =================================================================
    // LOGIC OUTCOME (INDUK)
    // =================================================================
    public function openModal() { $this->isOpen = true; }
    
    public function create() { 
        $this->reset(['outcome', 'sasaran_id', 'isEditMode']); 
        $this->openModal(); 
    }
    
    public function store() {
        $this->validate(['sasaran_id' => 'required', 'outcome' => 'required']);
        
        if ($this->isEditMode) { 
            // Saat edit, ID tetap, jadi posisi tidak berubah
            Outcome::find($this->outcome_id)->update([
                'sasaran_id' => $this->sasaran_id, 
                'outcome' => $this->outcome
            ]); 
        } else { 
            // Saat create, ID baru lebih besar, jadi masuk paling bawah
            Outcome::create([
                'sasaran_id' => $this->sasaran_id, 
                'outcome' => $this->outcome
            ]); 
        }
        $this->closeModal();
    }

    public function edit($id) {
        $data = Outcome::find($id);
        if ($data) { 
            $this->outcome_id = $id; 
            $this->sasaran_id = $data->sasaran_id; 
            $this->outcome = $data->outcome; 
            $this->isEditMode = true; 
            $this->openModal(); 
        }
    }

    public function delete($id) { 
        $data = Outcome::find($id); 
        if ($data) $data->delete(); 
    }

    // =================================================================
    // LOGIC PENANGGUNG JAWAB (PJ)
    // =================================================================
    public function pilihPenanggungJawab($id) {
        $data = Outcome::find($id);
        if ($data) { 
            $this->outcome_id = $id; 
            $this->pj_outcome_text = $data->outcome; 
            $this->pj_jabatan_id = $data->jabatan_id; 
            $this->isOpenPJ = true; 
        }
    }
    
    public function simpanPenanggungJawab() {
        $data = Outcome::find($this->outcome_id);
        if ($data) { 
            $data->update(['jabatan_id' => $this->pj_jabatan_id ?: null]); 
        }
        $this->closeModal();
    }

    // =================================================================
    // LOGIC INDIKATOR OUTCOME (ANAK)
    // =================================================================
    public function tambahIndikator($outcomeId) {
        $this->reset(['ind_keterangan', 'ind_satuan', 'ind_arah', 'indikator_id', 'isEditMode']);
        $this->selected_outcome_id = $outcomeId;
        $this->isOpenIndikator = true;
    }

    public function editIndikator($id) {
        $ind = IndikatorOutcome::find($id);
        if($ind){
            $this->indikator_id = $id; 
            $this->selected_outcome_id = $ind->outcome_id;
            $this->ind_keterangan = $ind->keterangan; 
            $this->ind_satuan = $ind->satuan;
            $this->ind_arah = $ind->arah; 
            $this->isEditMode = true; 
            $this->isOpenIndikator = true;
        }
    }

    public function storeIndikator() {
        $this->validate([
            'ind_keterangan' => 'required', 
            'ind_satuan' => 'required',
            'ind_arah' => 'required'
        ]);
        
        $data = [
            'outcome_id' => $this->selected_outcome_id, 
            'keterangan' => $this->ind_keterangan, 
            'satuan' => $this->ind_satuan,
            'arah' => $this->ind_arah
        ];

        if($this->isEditMode){ 
            IndikatorOutcome::find($this->indikator_id)->update($data); 
        } else { 
            IndikatorOutcome::create($data); 
        }
        $this->closeModal();
    }

    public function deleteIndikator($id) { 
        $ind = IndikatorOutcome::find($id); 
        if($ind) $ind->delete(); 
    }

    // =================================================================
    // LOGIC TARGET INDIKATOR
    // =================================================================
    public function aturTarget($id) {
        $ind = IndikatorOutcome::find($id);
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

    public function simpanTarget() {
        $ind = IndikatorOutcome::find($this->indikator_id);
        if ($ind) {
            $ind->update([
                'target_2025' => $this->target_2025, 
                'target_2026' => $this->target_2026, 
                'target_2027' => $this->target_2027,
                'target_2028' => $this->target_2028, 
                'target_2029' => $this->target_2029, 
                'target_2030' => $this->target_2030,
            ]);
        }
        $this->closeModal();
    }
}