<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tujuan;
use App\Models\Jabatan;
use App\Models\IndikatorTujuan;

class TujuanRenstra extends Component
{
    // --- STATE MODALS ---
    public $isOpen = false;          // Modal Tujuan
    public $isOpenPJ = false;        // Modal Penanggung Jawab
    public $isOpenIndikator = false; // Modal Indikator
    public $isOpenTarget = false;    // Modal Target
    
    public $isEditMode = false;

    // --- DATA TUJUAN ---
    public $tujuan_id, $sasaran_rpjmd, $tujuan;
    public $pj_tujuan_text, $pj_jabatan_id;

    // --- DATA INDIKATOR ---
    public $indikator_id;
    public $selected_tujuan_id;
    public $ind_keterangan, $ind_satuan, $ind_arah;

    // --- DATA TARGET ---
    public $target_2025, $target_2026, $target_2027, $target_2028, $target_2029, $target_2030;
    public $target_satuan;

    public $opsi_sasaran = [
        'Meningkatnya Kualitas Kesehatan Masyarakat', //yang di ganti khusus untuk tujuan
    ];

    public function render()
    {
        return view('livewire.tujuan-renstra', [
            // Agar data yang baru diinput masuk ke urutan paling bawah
            'tujuans' => Tujuan::with(['jabatan', 'indikators'])->orderBy('id', 'asc')->get(),
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
            'sasaran_rpjmd', 'tujuan', 'tujuan_id', 'isEditMode', 
            'pj_tujuan_text', 'pj_jabatan_id',
            'ind_keterangan', 'ind_satuan', 'ind_arah', 'indikator_id', 'selected_tujuan_id',
            'target_2025', 'target_2026', 'target_2027', 'target_2028', 'target_2029', 'target_2030',
            'target_satuan'
        ]);
    }

    // =================================================================
    // LOGIC TUJUAN (INDUK)
    // =================================================================
    public function openModal() { $this->isOpen = true; }
    public function create() { $this->reset(['sasaran_rpjmd', 'tujuan', 'tujuan_id', 'isEditMode']); $this->openModal(); }
    
    public function store() {
        $this->validate(['sasaran_rpjmd' => 'required', 'tujuan' => 'required']);
        
        if ($this->isEditMode) { 
            // Update data
            Tujuan::find($this->tujuan_id)->update([
                'sasaran_rpjmd' => $this->sasaran_rpjmd, 
                'tujuan' => $this->tujuan
            ]); 
        } else { 
            // Create data baru
            Tujuan::create([
                'sasaran_rpjmd' => $this->sasaran_rpjmd, 
                'tujuan' => $this->tujuan
            ]); 
        }
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function edit($id) {
        $data = Tujuan::find($id);
        if ($data) { $this->tujuan_id = $id; $this->sasaran_rpjmd = $data->sasaran_rpjmd; $this->tujuan = $data->tujuan; $this->isEditMode = true; $this->openModal(); }
    }

    public function delete($id) { 
        $data = Tujuan::find($id); 
        if ($data) $data->delete();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // =================================================================
    // LOGIC PENANGGUNG JAWAB (PJ)
    // =================================================================
    public function pilihPenanggungJawab($id) {
        $data = Tujuan::find($id);
        if ($data) { $this->tujuan_id = $id; $this->pj_tujuan_text = $data->tujuan; $this->pj_jabatan_id = $data->jabatan_id; $this->isOpenPJ = true; }
    }

    public function simpanPenanggungJawab() {
        $data = Tujuan::find($this->tujuan_id);
        if ($data) { $data->update(['jabatan_id' => $this->pj_jabatan_id ?: null]); }
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // =================================================================
    // LOGIC INDIKATOR (ANAK)
    // =================================================================
    public function tambahIndikator($tujuanId) {
        $this->reset(['ind_keterangan', 'ind_satuan', 'ind_arah', 'indikator_id', 'isEditMode']);
        $this->selected_tujuan_id = $tujuanId;
        $this->isOpenIndikator = true;
    }
    public function editIndikator($id) {
        $ind = IndikatorTujuan::find($id);
        if($ind){
            $this->indikator_id = $id; $this->selected_tujuan_id = $ind->tujuan_id;
            $this->ind_keterangan = $ind->keterangan; $this->ind_satuan = $ind->satuan; $this->ind_arah = $ind->arah;
            $this->isEditMode = true; $this->isOpenIndikator = true;
        }
    }

    public function storeIndikator() {
        $this->validate(['ind_keterangan' => 'required', 'ind_satuan' => 'required', 'ind_arah' => 'required']);
        $data = ['tujuan_id' => $this->selected_tujuan_id, 'keterangan' => $this->ind_keterangan, 'satuan' => $this->ind_satuan, 'arah' => $this->ind_arah];
        if($this->isEditMode){ IndikatorTujuan::find($this->indikator_id)->update($data); } 
        else { IndikatorTujuan::create($data); }
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function deleteIndikator($id) {
        $ind = IndikatorTujuan::find($id); if($ind) $ind->delete();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    // =================================================================
    // LOGIC TARGET
    // =================================================================
    public function aturTarget($id) {
        $ind = IndikatorTujuan::find($id);
        if ($ind) {
            $this->indikator_id = $id;
            $this->target_2025 = $ind->target_2025; $this->target_2026 = $ind->target_2026; $this->target_2027 = $ind->target_2027;
            $this->target_2028 = $ind->target_2028; $this->target_2029 = $ind->target_2029; $this->target_2030 = $ind->target_2030;
            $this->target_satuan = $ind->satuan;
            $this->isOpenTarget = true;
        }
    }

    public function simpanTarget() {
        $ind = IndikatorTujuan::find($this->indikator_id);
        if ($ind) {
            $ind->update([
                'target_2025' => $this->target_2025, 'target_2026' => $this->target_2026, 'target_2027' => $this->target_2027,
                'target_2028' => $this->target_2028, 'target_2029' => $this->target_2029, 'target_2030' => $this->target_2030,
            ]);
        }
        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }
}