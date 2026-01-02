<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class StrukturOrganisasi extends Component
{
    use WithFileUploads;
    use WithPagination;

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

    // Reset pagination saat mengetik pencarian
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 1. OPTIMASI: Caching Tree Jabatan selama 24 jam (agar loading cepat)
        // Cache akan dihapus otomatis jika ada Create/Update/Delete Jabatan
        $sortedJabatans = Cache::remember('jabatan_tree', 60 * 60 * 24, function () {
            $allJabatans = Jabatan::all();
            return $this->sortJabatanTree($allJabatans);
        });

        // 2. OPTIMASI: Pagination Pegawai (10 data per halaman)
        $pegawais = Pegawai::with('jabatan')
            ->when($this->search, function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('livewire.struktur-organisasi', [
            'jabatans' => $sortedJabatans,
            'pegawais' => $pegawais
        ]);
    }

    // Fungsi Rekursif untuk menyusun hirarki jabatan
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
    // LOGIC JABATAN (DILINDUNGI SERVER-SIDE)
    // =================================================================
    
    public function createJabatan()
    {
        // [KEAMANAN 2] Cek Role di Server (Backend)
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }

        $this->reset(['jab_id', 'jab_nama', 'jab_parent_id', 'isEditMode']);
        $this->resetValidation(); 
        $this->modalJabatanOpen = true;
    }

    public function editJabatan($id)
    {
        // [KEAMANAN 2] Cek Role
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }

        $jabatan = Jabatan::find($id);
        if($jabatan) {
            $this->resetValidation();
            $this->jab_id = $id;
            $this->jab_nama = $jabatan->nama;
            $this->jab_parent_id = $jabatan->parent_id;
            $this->isEditMode = true;
            $this->modalJabatanOpen = true;
        }
    }

    public function storeJabatan()
    {
        // [KEAMANAN 2] Cek Role
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }

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
            $message = 'Data Jabatan berhasil diperbarui.';
        } else {
            Jabatan::create([
                'nama' => $this->jab_nama,
                'parent_id' => $this->jab_parent_id ?: null,
                'level' => $level
            ]);
            $message = 'Data Jabatan berhasil ditambahkan.';
        }

        // Hapus Cache agar perubahan struktur langsung terlihat
        Cache::forget('jabatan_tree');

        session()->flash('success', $message);
        return redirect(request()->header('Referer'));
    }

    public function deleteJabatan($id)
    {
        // [KEAMANAN 2] Cek Role
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }
        
        $jabatan = Jabatan::find($id);
        if($jabatan) {
            $jabatan->delete();
            Cache::forget('jabatan_tree'); // Hapus cache
            session()->flash('success', 'Data Jabatan berhasil dihapus.');
            return redirect(request()->header('Referer'));
        }
    }

    // =================================================================
    // LOGIC PEGAWAI (DILINDUNGI SERVER-SIDE & VALIDASI KETAT)
    // =================================================================

    public function createPegawai()
    {
        // [KEAMANAN 2] Cek Role
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }

        $this->reset(['peg_id', 'peg_nama', 'peg_nip', 'peg_status', 'peg_jabatan_id', 'peg_foto', 'isEditMode']);
        $this->resetValidation();
        $this->peg_status = 'Definitif'; 
        $this->modalPegawaiOpen = true;
    }

    public function editPegawai($id)
    {
        // [KEAMANAN 2] Cek Role
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }

        $pegawai = Pegawai::find($id);
        if($pegawai) {
            $this->resetValidation();
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
        // [KEAMANAN 2] Cek Role
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }
        
        // [KEAMANAN 3] Validasi File Super Ketat
        $this->validate([
            'peg_nama' => 'required',
            'peg_nip' => 'required', // Idealnya tambahkan unique:pegawais,nip,id_saat_edit
            'peg_status' => 'required',
            // VALIDASI FOTO:
            // - nullable: Boleh kosong
            // - image: Harus file gambar
            // - mimes: Ekstensi harus jpeg, png, jpg, atau webp (TOLAK .php, .exe)
            // - max: Ukuran maksimal 2048 KB (2MB)
            'peg_foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $data = [
            'nama' => $this->peg_nama,
            'nip' => $this->peg_nip,
            'status' => $this->peg_status,
            'jabatan_id' => $this->peg_jabatan_id ?: null,
        ];

        if ($this->peg_foto) {
            // Livewire otomatis memberi nama acak pada file, ini juga fitur keamanan
            $filename = $this->peg_foto->store('fotos_pegawai', 'public');
            $data['foto'] = $filename;
            
            if ($this->isEditMode && $this->peg_foto_lama) {
                Storage::disk('public')->delete($this->peg_foto_lama);
            }
        }

        if ($this->isEditMode) {
            Pegawai::find($this->peg_id)->update($data);
            $message = 'Data Pegawai berhasil diperbarui.';
        } else {
            Pegawai::create($data);
            $message = 'Data Pegawai berhasil ditambahkan.';
        }
        
        session()->flash('success', $message);
        return redirect(request()->header('Referer'));
    }

    public function deletePegawai($id)
    {
        // [KEAMANAN 2] Cek Role
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Administrator.');
        }
        
        $pegawai = Pegawai::find($id);
        if ($pegawai) {
            if ($pegawai->foto) Storage::disk('public')->delete($pegawai->foto);
            $pegawai->delete();
            
            session()->flash('success', 'Data Pegawai berhasil dihapus.');
            return redirect(request()->header('Referer'));
        }
    }

    public function closeModal()
    {
        $this->modalJabatanOpen = false;
        $this->modalPegawaiOpen = false;
        $this->resetValidation();
    }
}