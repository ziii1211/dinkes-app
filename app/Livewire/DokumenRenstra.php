<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\RenstraSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DokumenRenstra extends Component
{
    use WithFileUploads;

    public $fileRenstra;
    public $existingFile;
    public $periode;
    public $canManage = false; // Variable untuk cek hak akses

    public function mount()
    {
        // 1. Cek Hak Akses User
        // Asumsi: Role 'admin' atau 'kasubag' boleh edit. 'pegawai' hanya lihat.
        // Sesuaikan string role ini dengan data di tabel users kamu.
        $userRole = Auth::user()->role ?? ''; // Ambil role user login
        
        // Logic: Jika BUKAN pegawai, maka boleh manage (upload/hapus)
        // Atau kamu bisa ubah jadi: if ($userRole === 'admin')
        $this->canManage = ($userRole !== 'pegawai');

        // 2. Ambil Setting Renstra
        $setting = RenstraSetting::where('is_aktif', true)->first() ?? RenstraSetting::first();

        if ($setting) {
            $this->periode = $setting->periode;
            $this->existingFile = $setting->file_path;
        }
    }

    public function updatedFileRenstra()
    {
        // Validasi real-time
        $this->validate([
            'fileRenstra' => 'required|mimes:pdf|max:20480',
        ]);
    }

    public function save()
    {
        // Security guard: Cek lagi saat mau simpan
        if (!$this->canManage) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah dokumen ini.');
        }

        $this->validate([
            'fileRenstra' => 'required|mimes:pdf|max:20480',
        ]);

        $setting = RenstraSetting::where('is_aktif', true)->first() ?? RenstraSetting::first();

        if (!$setting) {
            $setting = RenstraSetting::create([
                'periode' => '2025-2029',
                'tahun_awal' => 2025,
                'tahun_akhir' => 2029,
                'is_aktif' => true
            ]);
        }

        // Hapus file lama jika ada
        if ($setting->file_path && Storage::disk('public')->exists($setting->file_path)) {
            Storage::disk('public')->delete($setting->file_path);
        }

        // Simpan file baru
        $path = $this->fileRenstra->store('dokumen-renstra', 'public');

        // Update DB
        $setting->update([
            'file_path' => $path
        ]);

        $this->existingFile = $path;
        $this->fileRenstra = null;

        session()->flash('message', 'Dokumen Renstra berhasil diupload!');
    }

    public function deleteFile()
    {
        // Security guard
        if (!$this->canManage) {
            abort(403, 'Akses ditolak.');
        }

        $setting = RenstraSetting::where('is_aktif', true)->first();

        if ($setting && $setting->file_path) {
            // Hapus fisik file
            if (Storage::disk('public')->exists($setting->file_path)) {
                Storage::disk('public')->delete($setting->file_path);
            }

            // Kosongkan kolom di database
            $setting->update(['file_path' => null]);
            
            $this->existingFile = null;
            session()->flash('message', 'Dokumen Renstra berhasil dihapus.');
        }
    }

    public function render()
    {
        return view('livewire.dokumen-renstra');
    }
}