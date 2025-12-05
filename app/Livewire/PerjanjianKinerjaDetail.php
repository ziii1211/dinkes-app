<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PerjanjianKinerja;

class PerjanjianKinerjaDetail extends Component
{
    use WithPagination;

    public $jabatan;
    public $pegawai; // Pihak 1 (Pejabat saat ini)
    public $search = '';

    // --- MODAL PROPERTIES ---
    public $isOpen = false;
    public $tahun;
    public $keterangan;
    
    // Data Display di Modal (Untuk Pihak 2)
    public $atasan_pegawai; // Pihak 2 (Pejabat Atasan)
    public $atasan_jabatan; // Jabatan Atasan
    
    // Properti baru untuk Pihak 2 Khusus (Gubernur)
    public $is_kepala_dinas = false;
    public $gubernur_nama = 'H. MUHIDIN';
    public $gubernur_jabatan = 'GUBERNUR KALIMANTAN SELATAN';
    public $gubernur_foto = 'muhidin (1).png'; // Pastikan file ini ada di public/storage

    public function mount($id)
    {
        $this->jabatan = Jabatan::findOrFail($id);
        $this->pegawai = Pegawai::where('jabatan_id', $id)->latest()->first();
        $this->tahun = date('Y') + 1;

        // Cek apakah ini jabatan tertinggi (Kepala Dinas)
        // Asumsi: Kepala Dinas tidak punya parent_id
        $this->is_kepala_dinas = is_null($this->jabatan->parent_id);
    }

    public function render()
    {
        $pks = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->when($this->search, function($q) {
                $q->where('keterangan', 'like', '%' . $this->search . '%');
            })
            ->orderBy('tahun', 'desc')
            ->paginate(10);

        $totalPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)->count();
        $draftPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)->where('status', 'draft')->count();
        $finalPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)->where('status', 'final')->count();

        return view('livewire.perjanjian-kinerja-detail', [
            'pks' => $pks,
            'totalPk' => $totalPk,
            'draftPk' => $draftPk,
            'finalPk' => $finalPk
        ]);
    }

    // --- ACTIONS ---

    public function openModal()
    {
        $this->reset(['keterangan']);
        $this->tahun = date('Y') + 1;
        $this->keterangan = "PK " . $this->jabatan->nama . " Tahun " . $this->tahun;

        $this->atasan_pegawai = null;
        $this->atasan_jabatan = null;

        // LOGIKA CARI ATASAN (PIHAK 2)
        if ($this->is_kepala_dinas) {
            // Jika Kepala Dinas, Pihak 2 adalah Gubernur (Data statis sudah diset di properti)
        } elseif ($this->jabatan->parent_id) {
            // Jika bukan Kepala Dinas, cari atasan dari database
            $parentJabatan = Jabatan::find($this->jabatan->parent_id);
            if ($parentJabatan) {
                $this->atasan_jabatan = $parentJabatan;
                $this->atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
            }
        }

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function store()
    {
        $this->validate([
            'tahun' => 'required',
            'keterangan' => 'required',
        ]);

        // Tentukan siapa Pihak 2 untuk disimpan di database
        // Catatan: Anda mungkin perlu menyesuaikan struktur tabel 'perjanjian_kinerjas' 
        // jika ingin menyimpan data pihak 2 yang statis (Gubernur) ini secara permanen.
        // Untuk saat ini, saya asumsikan jika pegawai_id_atasan null, berarti itu Gubernur.
        
        $pegawaiIdAtasan = null;
        if (!$this->is_kepala_dinas && $this->atasan_pegawai) {
            $pegawaiIdAtasan = $this->atasan_pegawai->id;
        }

        PerjanjianKinerja::create([
            'jabatan_id' => $this->jabatan->id,
            'pegawai_id' => $this->pegawai ? $this->pegawai->id : null, // Pihak 1
            // 'pegawai_id_atasan' => $pegawaiIdAtasan, // Hapus komentar jika sudah ada kolomnya
            'tahun' => $this->tahun,
            'keterangan' => $this->keterangan,
            'status' => 'draft',
            'tanggal_penetapan' => now()
        ]);

        $this->closeModal();
    }

    public function publish($id)
    {
        $pk = PerjanjianKinerja::find($id);
        if ($pk) {
            $pk->update(['status' => 'final']);
        }
    }
}