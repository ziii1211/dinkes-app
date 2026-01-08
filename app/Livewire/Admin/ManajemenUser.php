<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On; // [PENTING] Import atribut On
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class ManajemenUser extends Component
{
    use WithPagination;

    // Form Variables
    public $userId;
    public $name, $username, $password, $role = 'pegawai';
    public $nip, $jabatan;
    
    // Variable untuk Dropdown Pegawai
    public $selectedPegawaiId = ''; 

    // UI State
    public $isModalOpen = false;
    public $search = '';
    public $filterRole = ''; 

    protected $queryString = ['search', 'filterRole'];

    public function updatedSearch() { $this->resetPage(); }
    
    public function updatedRole()
    {
        $this->resetValidation();
        $this->selectedPegawaiId = '';
        $this->name = '';
        $this->nip = '';
        $this->jabatan = '';
    }

    public function updatedSelectedPegawaiId($val)
    {
        if($val) {
            $pegawai = Pegawai::with('jabatan')->find($val);
            if($pegawai) {
                $this->name = $pegawai->nama;
                $this->nip = $pegawai->nip;
                $this->jabatan = $pegawai->jabatan ? $pegawai->jabatan->nama : '-';
            }
        }
    }

    public function render()
    {
        $query = User::with('pegawai'); 

        if($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('username', 'like', '%'.$this->search.'%')
                  ->orWhere('nip', 'like', '%'.$this->search.'%');
            });
        }

        if($this->filterRole) {
            $query->where('role', $this->filterRole);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        $daftarPegawai = Pegawai::join('jabatans', 'pegawais.jabatan_id', '=', 'jabatans.id')
            ->select('pegawais.*', 'jabatans.nama as nama_jabatan', 'jabatans.level')
            ->orderBy('jabatans.id', 'asc')
            ->get();

        $totalPimpinan = User::where('role', 'pimpinan')->count();
        $totalPegawai = User::where('role', 'pegawai')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        return view('livewire.admin.manajemen-user', [
            'users' => $users,
            'daftarPegawai' => $daftarPegawai,
            'stats' => [
                'pimpinan' => $totalPimpinan,
                'pegawai' => $totalPegawai,
                'admin' => $totalAdmin
            ]
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function store()
    {
        $rules = [
            'role' => 'required|in:admin,pegawai,pimpinan',
            'password' => 'required|min:6',
        ];

        if ($this->role == 'admin') {
            $rules['username'] = 'required|unique:users,username';
            $rules['name'] = 'required';
        } else {
            $rules['nip'] = 'required|numeric|unique:users,nip';
            $rules['selectedPegawaiId'] = 'required';
        }

        $this->validate($rules, [
            'selectedPegawaiId.required' => 'Silakan pilih Pegawai dari daftar.',
            'nip.unique' => 'Pegawai ini sudah memiliki akun user.',
        ]);

        $usernameToSave = ($this->role == 'admin') ? $this->username : $this->nip;

        User::create([
            'name' => $this->name,
            'username' => $usernameToSave, 
            'email' => $usernameToSave.'@dinkes.local',
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'nip' => $this->role == 'admin' ? null : $this->nip,
            'jabatan' => $this->role == 'admin' ? null : $this->jabatan,
        ]);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'User berhasil dibuat.']);
        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->nip = $user->nip;
        $this->jabatan = $user->jabatan;
        $this->password = ''; 
        
        if($user->nip) {
            $p = Pegawai::where('nip', $user->nip)->first();
            $this->selectedPegawaiId = $p ? $p->id : '';
        }

        $this->isModalOpen = true;
    }

    public function update()
    {
        $rules = [
            'role' => 'required|in:admin,pegawai,pimpinan',
        ];

        if ($this->role == 'admin') {
            $rules['username'] = 'required|unique:users,username,'.$this->userId;
            $rules['name'] = 'required';
        } else {
            $rules['nip'] = 'required|numeric|unique:users,nip,'.$this->userId;
        }

        if (!empty($this->password)) {
            $rules['password'] = 'min:6';
        }

        $this->validate($rules);

        $user = User::findOrFail($this->userId);
        $usernameToSave = ($this->role == 'admin') ? $this->username : $this->nip;

        $data = [
            'name' => $this->name,
            'role' => $this->role,
            'username' => $usernameToSave,
            'nip' => ($this->role == 'admin') ? null : $this->nip,
            'jabatan' => ($this->role == 'admin') ? null : $this->jabatan,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'User berhasil diperbarui.']);
        $this->closeModal();
    }

    // --- LOGIC HAPUS DATA (DIPERBAIKI) ---

    public function delete($id)
    {
        if($id == auth()->id()) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Anda tidak bisa menghapus akun sendiri!']);
            return;
        }
        
        // [PENTING] Gunakan named parameter 'id' agar JS bisa membacanya sebagai object { id: ... }
        $this->dispatch('confirmDelete', id: $id);
    }

    // [PENTING] Tambahkan atribut #[On] agar method ini bisa dipanggil via event browser
    #[On('deleteConfirmed')] 
    public function deleteConfirmed($id)
    {
        $user = User::find($id);
        
        if ($user) {
            $user->delete();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'User berhasil dihapus permanen.']);
        } else {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal menghapus: User tidak ditemukan.']);
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->username = '';
        $this->password = '';
        $this->role = 'pegawai';
        $this->nip = '';
        $this->jabatan = '';
        $this->selectedPegawaiId = '';
        $this->userId = null;
    }
}