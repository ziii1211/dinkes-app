<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sasaran;
use App\Models\Tujuan;
use App\Models\Jabatan;
use App\Models\IndikatorSasaran;

class SasaranRenstra extends Component
{
    // --- STATE MODALS ---
    public $isOpen = false;          // Modal Sasaran
    public $isOpenPJ = false;        // Modal Penanggung Jawab
    public $isOpenIndikator = false; // Modal Indikator
    public $isOpenTarget = false;    // Modal Target
    
    public $isEditMode = false;

    // --- DATA SASARAN ---
    public $sasaran_id, $tujuan_id, $sasaran;
    public $pj_sasaran_text, $pj_jabatan_id;

    // --- DATA INDIKATOR ---
    public $indikator_id;
    public $selected_sasaran_id;
    public $ind_keterangan, $ind_satuan, $ind_arah;

    // --- DATA TARGET ---
    public $target_2025, $target_2026, $target_2027, $target_2028, $target_2029, $target_2030;
    public $target_satuan; // Variabel label dinamis

    public function render()
    {
        return view('livewire.sasaran-renstra', [
            'sasarans' => Sasaran::with(['tujuan', 'jabatan', 'indikators'])->get(),
            'list_tujuan' => Tujuan::all(), 
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
            'tujuan_id', 'sasaran', 'sasaran_id', 'isEditMode', 
            'pj_sasaran_text', 'pj_jabatan_id',
            'ind_keterangan', 'ind_satuan', 'ind_arah', 'indikator_id', 'selected_sasaran_id',
            'target_2025', 'target_2026', 'target_2027', 'target_2028', 'target_2029', 'target_2030',
            'target_satuan'
        ]);
    }

    // =================================================================
    // LOGIC SASARAN (CRUD UTAMA)
    // =================================================================
    public function openModal() { $this->isOpen = true; }
    public function create() { $this->reset(['tujuan_id', 'sasaran', 'sasaran_id', 'isEditMode']); $this->openModal(); }
    
    public function store() {
        $this->validate(['tujuan_id' => 'required', 'sasaran' => 'required']);
        
        if ($this->isEditMode) { 
            Sasaran::find($this->sasaran_id)->update(['tujuan_id' => $this->tujuan_id, 'sasaran' => $this->sasaran]); 
        } else { 
            Sasaran::create(['tujuan_id' => $this->tujuan_id, 'sasaran' => $this->sasaran]); 
        }
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function edit($id) {
        $data = Sasaran::find($id);
        if ($data) { 
            $this->sasaran_id = $id; 
            $this->tujuan_id = $data->tujuan_id; 
            $this->sasaran = $data->sasaran; 
            $this->isEditMode = true; 
            $this->openModal(); 
        }
    }
    
    public function delete($id) { 
        $data = Sasaran::find($id); 
        if ($data) $data->delete();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // =================================================================
    // LOGIC PENANGGUNG JAWAB (PJ)
    // =================================================================
    public function pilihPenanggungJawab($id) {
        $data = Sasaran::find($id);
        if ($data) { 
            $this->sasaran_id = $id; 
            $this->pj_sasaran_text = $data->sasaran; 
            $this->pj_jabatan_id = $data->jabatan_id; 
            $this->isOpenPJ = true; 
        }
    }
    
    public function simpanPenanggungJawab() {
        $data = Sasaran::find($this->sasaran_id);
        if ($data) { $data->update(['jabatan_id' => $this->pj_jabatan_id ?: null]); }
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // =================================================================
    // LOGIC INDIKATOR SASARAN
    // =================================================================
    public function tambahIndikator($sasaranId) {
        $this->reset(['ind_keterangan', 'ind_satuan', 'ind_arah', 'indikator_id', 'isEditMode']);
        $this->selected_sasaran_id = $sasaranId;
        $this->isOpenIndikator = true;
    }

    public function editIndikator($id) {
        $ind = IndikatorSasaran::find($id);
        if($ind){
            $this->indikator_id = $id; 
            $this->selected_sasaran_id = $ind->sasaran_id;
            $this->ind_keterangan = $ind->keterangan; 
            $this->ind_satuan = $ind->satuan; 
            $this->ind_arah = $ind->arah;
            $this->isEditMode = true; 
            $this->isOpenIndikator = true;
        }
    }

    public function storeIndikator() {
        $this->validate(['ind_keterangan' => 'required', 'ind_satuan' => 'required', 'ind_arah' => 'required']);
        
        $data = [
            'sasaran_id' => $this->selected_sasaran_id, 
            'keterangan' => $this->ind_keterangan, 
            'satuan' => $this->ind_satuan, 
            'arah' => $this->ind_arah
        ];

        if($this->isEditMode){ 
            IndikatorSasaran::find($this->indikator_id)->update($data); 
        } else { 
            IndikatorSasaran::create($data); 
        }
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function deleteIndikator($id) {
        $ind = IndikatorSasaran::find($id); if($ind) $ind->delete();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // =================================================================
    // LOGIC TARGET INDIKATOR
    // =================================================================
    public function aturTarget($id)
    {
        $ind = IndikatorSasaran::find($id);
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
        $ind = IndikatorSasaran::find($this->indikator_id);
        
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

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }
}