<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SkpdTujuan;
use App\Models\CrosscuttingRenstra;

class CascadingRenstra extends Component
{
    public $isOpen = false;

    // Form inputs
    public $selected_sumber; 
    public $skpd_id;
    public $selected_tujuan;

    public function render()
    {
        // 1. Data Dropdown Sumber/Tujuan (Gabungan)
        $sasarans = Sasaran::select('id', 'sasaran as teks')->get()->map(function($item){
            $item->type = 'sasaran';
            $item->label = '[Sasaran] ' . $item->teks;
            return $item;
        });

        $outcomes = Outcome::select('id', 'outcome as teks')->get()->map(function($item){
            $item->type = 'outcome';
            $item->label = '[Outcome] ' . $item->teks;
            return $item;
        });

        $kegiatans = Kegiatan::select('id', 'output as teks')->whereNotNull('output')->get()->map(function($item){
            $item->type = 'kegiatan';
            $item->label = '[Output] ' . $item->teks;
            return $item;
        });

        $kinerja_list = $sasarans->concat($outcomes)->concat($kegiatans);

        // 2. Data Tabel Utama
        $crosscuttings = CrosscuttingRenstra::with(['sumber', 'tujuan', 'skpd'])
            ->orderBy('created_at', 'asc') 
            ->get();

        return view('livewire.cascading-renstra', [
            'kinerja_list' => $kinerja_list,
            'skpds' => SkpdTujuan::all(),
            'crosscuttings' => $crosscuttings
        ]);
    }

    public function openModal()
    {
        $this->reset(['selected_sumber', 'skpd_id', 'selected_tujuan']);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
    }

    private function getModelClass($type) {
        return match ($type) {
            'sasaran' => Sasaran::class,
            'outcome' => Outcome::class,
            'kegiatan' => Kegiatan::class,
            default => null,
        };
    }

    public function store()
    {
        $this->validate([
            'selected_sumber' => 'required',
            'skpd_id' => 'required',
            'selected_tujuan' => 'required',
        ]);

        list($sumberTypeStr, $sumberId) = explode('|', $this->selected_sumber);
        list($tujuanTypeStr, $tujuanId) = explode('|', $this->selected_tujuan);

        CrosscuttingRenstra::create([
            'sumber_type' => $this->getModelClass($sumberTypeStr),
            'sumber_id' => $sumberId,
            'skpd_tujuan_id' => $this->skpd_id,
            'tujuan_type' => $this->getModelClass($tujuanTypeStr),
            'tujuan_id' => $tujuanId
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        $data = CrosscuttingRenstra::find($id);
        if ($data) {
            $data->delete();
        }
    }

    // --- FUNGSI HELPER BARU UNTUK MENAMPILKAN TEKS ---
    // Fungsi ini akan dipanggil dari View untuk membersihkan tampilan JSON
    public function getKinerjaLabel($model)
    {
        if (!$model) return '-';

        // Cek berdasarkan Tipe Class Model
        if ($model instanceof Sasaran) {
            return $model->sasaran;
        } elseif ($model instanceof Outcome) {
            return $model->outcome;
        } elseif ($model instanceof Kegiatan) {
            return $model->output;
        }

        // Fallback: Cek properti jika Class check gagal (misal karena relasi)
        if (isset($model->sasaran)) return $model->sasaran;
        if (isset($model->outcome)) return $model->outcome;
        if (isset($model->output)) return $model->output;

        return '-'; // Jika tidak ada yang cocok
    }
}