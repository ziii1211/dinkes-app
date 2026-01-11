<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Program;
use App\Models\Outcome;

class ProgramKegiatan extends Component
{
    // --- STATES ---
    public $isOpen = false;        // Modal Program
    public $isOpenOutcome = false; // Modal Pilih Outcome
    public $isEditMode = false;

    // --- FORM PROGRAM ---
    public $program_id, $kode, $nama;

    // --- FORM PILIH OUTCOME ---
    public $selected_program_id;
    public $outcome_id_to_add;

    public function render()
    {
        return view('livewire.program-kegiatan', [
            // 1. Ambil Program beserta Outcome dan Indikatornya untuk Tabel
            // UBAH SORTING: Menggunakan 'id' 'asc' agar data lama tetap di atas, data baru di bawah
            'programs' => Program::with(['outcomes.indikators', 'outcomes.sasaran'])
                        ->orderBy('id', 'asc') 
                        ->get(),
            
            // 2. Ambil Data Outcome untuk Dropdown
            'outcomes_list' => Outcome::with('sasaran')
                            ->orderBy('id', 'desc') 
                            ->get() 
        ]);
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isOpenOutcome = false;
        $this->resetValidation();
        $this->reset(['program_id', 'kode', 'nama', 'isEditMode', 'selected_program_id', 'outcome_id_to_add']);
    }

    // --- PROGRAM CRUD ---
    public function openModal() { $this->isOpen = true; }
    
    public function create() { 
        $this->reset(['kode', 'nama', 'isEditMode']); 
        $this->openModal(); 
    }
    
    public function store() {
        $this->validate(['kode' => 'required', 'nama' => 'required']);
        
        if ($this->isEditMode) { 
            Program::find($this->program_id)->update([
                'kode' => $this->kode, 
                'nama' => $this->nama
            ]); 
        } else { 
            Program::create([
                'kode' => $this->kode, 
                'nama' => $this->nama
            ]); 
        }
        
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }
    
    public function edit($id) {
        $data = Program::find($id);
        if ($data) { 
            $this->program_id = $id; 
            $this->kode = $data->kode; 
            $this->nama = $data->nama; 
            $this->isEditMode = true; 
            $this->openModal(); 
        }
    }
    
    public function delete($id) { 
        $data = Program::find($id); 
        if ($data) $data->delete(); 

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }


    // --- LOGIC TAMBAH OUTCOME KE PROGRAM ---
    
    public function addOutcome($programId)
    {
        $this->selected_program_id = $programId;
        $this->reset('outcome_id_to_add'); 
        $this->isOpenOutcome = true; 
    }

    public function storeOutcome()
    {
        $this->validate([
            'outcome_id_to_add' => 'required'
        ], [
            'outcome_id_to_add.required' => 'Silakan pilih Outcome terlebih dahulu.'
        ]);

        // Update Outcome dengan Program ID yang dipilih
        $outcome = Outcome::find($this->outcome_id_to_add);
        
        if ($outcome) {
            $outcome->update([
                'program_id' => $this->selected_program_id
            ]);
        }

        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // Fungsi untuk melepas Outcome dari Program
    public function removeOutcome($outcomeId)
    {
        $outcome = Outcome::find($outcomeId);
        if($outcome) {
            // Set program_id jadi NULL agar lepas dari program ini
            $outcome->update(['program_id' => null]);
        }

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }
}