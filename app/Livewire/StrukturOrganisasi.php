<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasi extends Component
{
    use WithFileUploads;

    // --- STATE PENCARIAN ---
    public $search = '';

    // --- STATE MODAL ---
    public $modalJabatanOpen = false;
    public $modalPegawaiOpen = false;
    public $isEditMode = false;

    // --- FORM JABATAN ---
    public $jab_id, $jab_nama, $jab_parent_id;

    // --- FORM PEGAWAI ---
    public $peg_id, $peg_nama, $peg_nip, $peg_status = 'Definitif', $peg_jabatan_id, $peg_foto, $peg_foto_lama;

    public function render()
    {
        // LOGIKA BARU: Hierarchical Tree Sorting
        $allJabatans = Jabatan::all();
        $sortedJabatans = $this->sortJabatanTree($allJabatans);

        // LOGIKA PENCARIAN PEGAWAI
        $pegawais = Pegawai::with('jabatan')
            ->when($this->search, function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.struktur-organisasi', [
            // Data Jabatan (Tree)
            'jabatans' => $sortedJabatans,
            
            // Data Pegawai (Filtered)
            'pegawais' => $pegawais
        ]);
    }

    // --- FUNGSI HELPER UNTUK MENGURUTKAN JABATAN SEPERTI POHON ---
    private function sortJabatanTree($elements, $parentId = null)
    {
        $branch = collect();
        $children = $elements->where('parent_id', $parentId)->sortBy('id');

        foreach ($children as $child) {
            $branch->push($child);
            $grandChildren = $this->sortJabatanTree($elements, $child->id);
            if ($grandChildren->isNotEmpty()) {
                $branch = $branch->merge($grandChildren);
            }
        }

        return $branch;
    }

    // =================================================================
    // LOGIC JABATAN (PROTECTED)
    // =================================================================
    
    public function createJabatan()
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $this->reset(['jab_id', 'jab_nama', 'jab_parent_id', 'isEditMode']);
        $this->modalJabatanOpen = true;
    }

    public function editJabatan($id)
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $jabatan = Jabatan::find($id);
        if($jabatan) {
            $this->jab_id = $id;
            $this->jab_nama = $jabatan->nama;
            $this->jab_parent_id = $jabatan->parent_id;
            $this->isEditMode = true;
            $this->modalJabatanOpen = true;
        }
    }

    public function storeJabatan()
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $this->validate(['jab_nama' => 'required']);
        
        $level = 0;
        if ($this->jab_parent_id) {
            $atasan = Jabatan::find($this->jab_parent_id);
            if($atasan) $level = $atasan->level + 1;
        }

        if ($this->isEditMode) {
            $jabatan = Jabatan::find($this->jab_id);
            $jabatan->update([
                'nama' => $this->jab_nama,
                'parent_id' => $this->jab_parent_id ?: null,
                'level' => $level
            ]);
        } else {
            Jabatan::create([
                'nama' => $this->jab_nama,
                'parent_id' => $this->jab_parent_id ?: null,
                'level' => $level
            ]);
        }
        $this->modalJabatanOpen = false;
    }

    public function deleteJabatan($id)
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $jabatan = Jabatan::find($id);
        if($jabatan) $jabatan->delete();
    }

    // =================================================================
    // LOGIC PEGAWAI (PROTECTED)
    // =================================================================

    public function createPegawai()
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $this->reset(['peg_id', 'peg_nama', 'peg_nip', 'peg_status', 'peg_jabatan_id', 'peg_foto', 'isEditMode']);
        $this->peg_status = 'Definitif'; 
        $this->modalPegawaiOpen = true;
    }

    public function editPegawai($id)
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $pegawai = Pegawai::find($id);
        if($pegawai) {
            $this->peg_id = $id;
            $this->peg_nama = $pegawai->nama;
            $this->peg_nip = $pegawai->nip;
            $this->peg_status = $pegawai->status;
            $this->peg_jabatan_id = $pegawai->jabatan_id;
            $this->peg_foto_lama = $pegawai->foto;
            $this->isEditMode = true;
            $this->modalPegawaiOpen = true;
        }
    }

    public function storePegawai()
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $this->validate([
            'peg_nama' => 'required',
            'peg_nip' => 'required',
            'peg_status' => 'required',
            'peg_foto' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $this->peg_nama,
            'nip' => $this->peg_nip,
            'status' => $this->peg_status,
            'jabatan_id' => $this->peg_jabatan_id ?: null,
        ];

        if ($this->peg_foto) {
            $filename = $this->peg_foto->store('fotos_pegawai', 'public');
            $data['foto'] = $filename;
            
            if ($this->isEditMode && $this->peg_foto_lama) {
                Storage::disk('public')->delete($this->peg_foto_lama);
            }
        }

        if ($this->isEditMode) {
            Pegawai::find($this->peg_id)->update($data);
        } else {
            Pegawai::create($data);
        }
        $this->modalPegawaiOpen = false;
    }

    public function deletePegawai($id)
    {
        if (auth()->user()->hasRole('pimpinan')) return; // Proteksi
        $pegawai = Pegawai::find($id);
        if ($pegawai) {
            if ($pegawai->foto) Storage::disk('public')->delete($pegawai->foto);
            $pegawai->delete();
        }
    }

    public function closeModal()
    {
        $this->modalJabatanOpen = false;
        $this->modalPegawaiOpen = false;
    }
}