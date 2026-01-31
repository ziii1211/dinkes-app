<?php

namespace App\Livewire\LaporanKonsolidasi;

use Livewire\Component;
use App\Models\LaporanKonsolidasi;

class Form extends Component
{
    public $id, $judul, $bulan, $tahun;
    public $isEdit = false;

    public function mount($id = null)
    {
        if ($id) {
            $laporan = LaporanKonsolidasi::findOrFail($id);
            $this->id = $laporan->id;
            $this->judul = $laporan->judul;
            $this->bulan = $laporan->bulan;
            $this->tahun = $laporan->tahun;
            $this->isEdit = true;
        } else {
            $this->tahun = date('Y');
        }
    }

    public function save()
    {
        $this->validate([
            'judul' => 'required',
            'bulan' => 'required',
            'tahun' => 'required|numeric',
        ]);

        LaporanKonsolidasi::updateOrCreate(
            ['id' => $this->id],
            [
                'judul' => $this->judul,
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
            ]
        );

        return redirect()->route('laporan-konsolidasi.index')->with('message', 'Data berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.laporan-konsolidasi.form');
    }
}