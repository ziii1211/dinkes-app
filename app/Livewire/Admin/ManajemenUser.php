<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class ManajemenUser extends Component
{
    use WithPagination;

    // Form Variables
    public $userId = null;
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
        
        // Reset field jika berpindah role dan sedang mode tambah
        if(!$this->userId) {
            $this->name = '';
            $this->nip = '';
            $this->jabatan = '';
        }
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

        // Urutan ASC agar data lama stabil di atas
        $users = $query->orderBy('id', 'asc')->paginate(10);

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
            $rules['selectedPegawaiId'] = 'required';
            // Validasi: NIP boleh sama asalkan Role-nya berbeda
            $rules['nip'] = [
                'required',
                'numeric',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('role', $this->role);
                }),
            ];
        }

        $this->validate($rules, [
            'selectedPegawaiId.required' => 'Silakan pilih Pegawai dari daftar.',
            'nip.unique' => 'Pegawai ini sudah memiliki akun sebagai ' . ucfirst($this->role) . '.',
        ]);

        // Generate Username
        if ($this->role == 'admin') {
            $usernameToSave = $this->username;
        } else {
            // Jika Role Pimpinan, username = NIP + .pimpinan
            $usernameToSave = ($this->role == 'pimpinan') ? $this->nip . '.pimpinan' : $this->nip;
        }

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
        $this->resetValidation();
        $user = User::findOrFail($id);
        
        $this->userId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->nip = $user->nip;
        $this->jabatan = $user->jabatan;
        $this->password = ''; // Reset password field
        
        if($user->nip) {
            $p = Pegawai::where('nip', $user->nip)->first();
            $this->selectedPegawaiId = $p ? $p->id : '';
        } else {
            $this->selectedPegawaiId = '';
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
            if(empty($this->name) || empty($this->nip)) {
                 $this->validate(['selectedPegawaiId' => 'required'], ['selectedPegawaiId.required' => 'Silakan pilih pegawai.']);
            }
            // Validasi Update dengan Ignore ID User saat ini
            $rules['nip'] = [
                'required',
                'numeric',
                Rule::unique('users')->ignore($this->userId)->where(function ($query) {
                    return $query->where('role', $this->role);
                }),
            ];
        }

        if (!empty($this->password)) {
            $rules['password'] = 'min:6';
        }

        $this->validate($rules, [
            'nip.unique' => 'Pegawai ini sudah memiliki akun sebagai ' . ucfirst($this->role) . '.',
        ]);

        // [PERBAIKAN UTAMA] Menggunakan Direct Assignment + Save()
        // Ini menjamin data tersimpan meskipun ada isu di $fillable
        $user = User::findOrFail($this->userId);
        
        if ($this->role == 'admin') {
            $usernameToSave = $this->username;
        } else {
            $usernameToSave = ($this->role == 'pimpinan') ? $this->nip . '.pimpinan' : $this->nip;
        }

        $user->name = $this->name;
        $user->role = $this->role;
        $user->username = $usernameToSave;
        $user->email = $usernameToSave . '@dinkes.local';
        $user->nip = ($this->role == 'admin') ? null : $this->nip;
        $user->jabatan = ($this->role == 'admin') ? null : $this->jabatan;

        // Cek jika password diisi, maka update dan Hash
        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save(); // Simpan perubahan ke database

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Data User berhasil diperbarui.']);
        $this->closeModal();
    }

    public function delete($id)
    {
        if($id == auth()->id()) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Anda tidak bisa menghapus akun sendiri!']);
            return;
        }
        
        $this->dispatch('confirmDelete', id: $id);
    }

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
        $this->resetValidation();
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