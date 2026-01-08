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
        // $this->selectedPegawaiId = ''; // Opsional: reset jika perlu
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

        // Urutkan berdasarkan update terakhir agar data baru muncul di atas
        $users = $query->orderBy('updated_at', 'desc')->paginate(10);

        $daftarPegawai = Pegawai::join('jabatans', 'pegawais.jabatan_id', '=', 'jabatans.id')
            ->select('pegawais.*', 'jabatans.nama as nama_jabatan', 'jabatans.level')
            ->orderBy('jabatans.id', 'asc')
            ->get();

        return view('livewire.admin.manajemen-user', [
            'users' => $users,
            'daftarPegawai' => $daftarPegawai,
            'stats' => [
                'pimpinan' => User::where('role', 'pimpinan')->count(),
                'pegawai' => User::where('role', 'pegawai')->count(),
                'admin' => User::where('role', 'admin')->count()
            ]
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    // --- PERBAIKAN DI SINI (STORE) ---
    public function store()
    {
        $rules = [
            'role' => 'required|in:admin,pegawai,pimpinan',
            'password' => 'required|min:6',
        ];

        if ($this->role == 'admin') {
            $rules['username'] = 'required|unique:users,username';
            $rules['name'] = 'required';
            $usernameToSave = $this->username;
        } else {
            $rules['selectedPegawaiId'] = 'required';
            $rules['nip'] = [
                'required',
                'numeric',
                // Validasi: NIP boleh sama asalkan Role-nya berbeda
                Rule::unique('users')->where(function ($query) {
                    return $query->where('role', $this->role);
                }),
            ];
            
            // Generate Username Otomatis
            $usernameToSave = ($this->role == 'pimpinan') ? $this->nip . '.pimpinan' : $this->nip;
        }

        $this->validate($rules, [
            'selectedPegawaiId.required' => 'Silakan pilih Pegawai dari daftar.',
            'nip.unique' => 'Pegawai ini sudah memiliki akun sebagai ' . ucfirst($this->role) . '.',
        ]);

        // [PENGECEKAN MANUAL USERNAME] Mencegah Error SQL Duplicate Entry
        if (User::where('username', $usernameToSave)->exists()) {
            $pesanError = "Username otomatis '$usernameToSave' sudah digunakan.";
            
            // Beri saran spesifik
            if ($this->role == 'pegawai') {
                $pesanError .= " Kemungkinan akun Pimpinan user ini masih menggunakan username NIP lama. Silakan edit akun Pimpinannya terlebih dahulu agar usernamenya terupdate menjadi berakhiran '.pimpinan'.";
            }
            
            $this->addError('nip', $pesanError);
            return; // Stop proses simpan
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

        // FLASH MESSAGE & RESET FILTER AGAR TABEL REFRESH
        session()->flash('message', 'User berhasil dibuat.');
        $this->reset(['search', 'filterRole']); 
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
        $this->password = ''; 
        
        if($user->nip) {
            $p = Pegawai::where('nip', $user->nip)->first();
            $this->selectedPegawaiId = $p ? $p->id : '';
        } else {
            $this->selectedPegawaiId = '';
        }

        $this->isModalOpen = true;
    }

    // --- PERBAIKAN DI SINI (UPDATE) ---
    public function update()
    {
        $rules = [
            'role' => 'required|in:admin,pegawai,pimpinan',
        ];

        if ($this->role == 'admin') {
            $rules['username'] = 'required|unique:users,username,'.$this->userId;
            $rules['name'] = 'required';
            $usernameToSave = $this->username;
        } else {
            if(empty($this->name) || empty($this->nip)) {
                 $this->validate(['selectedPegawaiId' => 'required'], ['selectedPegawaiId.required' => 'Silakan pilih pegawai.']);
            }
            $rules['nip'] = [
                'required',
                'numeric',
                Rule::unique('users')->ignore($this->userId)->where(function ($query) {
                    return $query->where('role', $this->role);
                }),
            ];
            
            // Generate Username Otomatis
            $usernameToSave = ($this->role == 'pimpinan') ? $this->nip . '.pimpinan' : $this->nip;
        }

        if (!empty($this->password)) {
            $rules['password'] = 'min:6';
        }

        $this->validate($rules);

        // [PENGECEKAN MANUAL USERNAME SAAT UPDATE]
        // Pastikan username baru tidak bentrok dengan user LAIN (kecuali diri sendiri)
        $cekDouble = User::where('username', $usernameToSave)->where('id', '!=', $this->userId)->first();
        if ($cekDouble) {
             $this->addError('nip', "Gagal update: Username '$usernameToSave' sudah dipakai oleh user lain (ID: {$cekDouble->id}).");
             return;
        }

        $user = User::findOrFail($this->userId);
        
        $user->name = $this->name;
        $user->role = $this->role;
        $user->username = $usernameToSave;
        
        // Update email agar konsisten (opsional)
        if($user->isDirty('username')) {
             $user->email = $usernameToSave . '@dinkes.local';
        }

        $user->nip = ($this->role == 'admin') ? null : $this->nip;
        $user->jabatan = ($this->role == 'admin') ? null : $this->jabatan;

        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        session()->flash('message', 'Data User berhasil diperbarui.');
        $this->reset(['search', 'filterRole']);
        $this->closeModal();
    }

    // --- PERBAIKAN DI SINI (DELETE LANGSUNG) ---
    public function delete($id)
    {
        if($id == auth()->id()) {
            session()->flash('error', 'Anda tidak bisa menghapus akun sendiri!');
            return;
        }

        $user = User::find($id);
        if ($user) {
            $user->delete();
            session()->flash('message', 'User berhasil dihapus.');
        } else {
            session()->flash('error', 'User tidak ditemukan.');
        }
    }

    // (Opsional: Method ini bisa dihapus jika sudah pakai delete langsung di atas)
    #[On('deleteConfirmed')] 
    public function deleteConfirmed($id)
    {
        $this->delete($id);
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