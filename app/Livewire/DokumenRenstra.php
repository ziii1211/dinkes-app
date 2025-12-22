<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\PohonKinerja;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel; // Pastikan package excel sudah install
use App\Exports\DokumenRenstraExport;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan package dompdf sudah install

class DokumenRenstra extends Component
{
    public $unit_kerja = 'DINAS KESEHATAN';
    public $nomor_dokumen = '031';
    public $tanggal_dokumen = '12 September 2025';
    public $periode = '2025 - 2029';

    public $isOpenEdit = false;
    public $edit_nomor_dokumen;
    public $edit_tanggal_penetapan;

    // Cache data pohon untuk optimasi
    protected $all_pohons;

    // =================================================================================
    // 1. CORE LOGIC (DIPISAHKAN AGAR BISA DIPAKAI EXPORT)
    // =================================================================================

    public function getDataRenstra()
    {
        // Load data pohon sekali saja
        $this->all_pohons = PohonKinerja::with(['indikators', 'children'])->get();

        // 1. Identifikasi Stop Points
        $stopForSasaran = $this->getStopIds(Outcome::all(), 'outcome');  
        $stopForOutcome = $this->getStopIds(Kegiatan::all(), 'kegiatan'); 
        $stopForKegiatan = $this->getStopIds(SubKegiatan::all(), 'sub_kegiatan');

        // 2. Mapping Data

        // TUJUAN
        $tujuans = Tujuan::all()->map(function($item) {
            $node = $this->findPohonNode($item, 'tujuan');
            $item->indikators_from_pohon = $this->getDirectIndicators($node);
            return $item;
        });

        // SASARAN (Flattening Level 3)
        $finalSasarans = collect([]);
        foreach (Sasaran::all() as $item) {
            $node = $this->findPohonNode($item, 'sasaran');
            $item->indikators_from_pohon = $this->getDirectIndicators($node); 
            $finalSasarans->push($item);

            if ($node) {
                $virtualChildren = $this->createVirtualRows($node, $stopForSasaran, 'sasaran');
                $finalSasarans = $finalSasarans->concat($virtualChildren);
            }
        }

        // OUTCOME
        $finalOutcomes = collect([]);
        foreach (Outcome::with('program')->get() as $item) {
            $node = $this->findPohonNode($item, 'outcome');
            $item->indikators_from_pohon = $this->getDirectIndicators($node);
            $finalOutcomes->push($item);

            if ($node) {
                $virtualChildren = $this->createVirtualRows($node, $stopForOutcome, 'outcome');
                $finalOutcomes = $finalOutcomes->concat($virtualChildren);
            }
        }
        
        // KEGIATAN
        $finalKegiatans = collect([]);
        foreach (Kegiatan::whereNotNull('output')->get() as $item) {
            $node = $this->findPohonNode($item, 'kegiatan');
            $item->indikators_from_pohon = $this->getDirectIndicators($node);
            $finalKegiatans->push($item);

            if ($node) {
                $virtualChildren = $this->createVirtualRows($node, $stopForKegiatan, 'output');
                $virtualChildren->transform(function($v) {
                    $v->kode = ''; $v->nama = ''; return $v;
                });
                $finalKegiatans = $finalKegiatans->concat($virtualChildren);
            }
        }

        // SUB KEGIATAN
        $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) {
            $manual = $item->indikators ? $item->indikators->map(fn($ind) => (object)['nama_indikator' => $ind->keterangan ?? $ind->nama_indikator ?? '-']) : collect([]);
            
            $node = $this->findPohonNode($item, 'sub_kegiatan');
            $pohonInd = $this->getDirectIndicators($node);

            $item->indikators_from_pohon = $manual->concat($pohonInd)
                ->filter(fn($i) => !empty($i->nama_indikator))
                ->unique(fn($i) => $this->normalizeText($i->nama_indikator))
                ->values();
            return $item;
        });

        // KEMBALIKAN DATA MENTAH
        return [
            'unit_kerja'    => $this->unit_kerja,
            'nomor_dokumen' => $this->nomor_dokumen,
            'tanggal_dokumen' => $this->tanggal_dokumen,
            'periode'       => $this->periode,
            'tujuans'       => $tujuans,
            'sasarans'      => $finalSasarans,
            'outcomes'      => $finalOutcomes,
            'kegiatans'     => $finalKegiatans,
            'sub_kegiatans' => $sub_kegiatans,
        ];
    }

    // =================================================================================
    // 2. HELPER FUNCTIONS (PRIVATE METHODS)
    // =================================================================================

    private function normalizeText($text) {
        if (empty($text)) return '';
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        return strtolower(trim(preg_replace('/\s+/', ' ', $text)));
    }

    private function findPohonNode($item, $type) {
        // A. Coba by ID
        if ($type === 'tujuan' && isset($item->id)) {
            $matchById = $this->all_pohons->where('tujuan_id', $item->id)->first();
            if ($matchById) return $matchById;
        }

        // B. Siapkan teks
        $targetText = '';
        if ($type === 'tujuan') {
            $targetText = $this->normalizeText($item->tujuan ?? $item->sasaran_rpjmd);
        } elseif ($type === 'sasaran') {
            $targetText = $this->normalizeText($item->sasaran);
        } elseif ($type === 'outcome') {
            $targetText = $this->normalizeText($item->outcome);
        } elseif ($type === 'kegiatan' || $type === 'sub_kegiatan') {
            $targetText = $this->normalizeText($item->nama);
        }

        if (empty($targetText) || strlen($targetText) < 3) return null;

        // C. Matching
        $bestMatch = null;
        $highestPercent = 0;

        foreach ($this->all_pohons as $pohon) {
            $pohonName = $this->normalizeText($pohon->nama_pohon);
            
            if ($pohonName === $targetText) return $pohon;
            if (Str::contains($pohonName, $targetText) || Str::contains($targetText, $pohonName)) {
                $percent = 95; 
                if ($percent > $highestPercent) { $highestPercent = $percent; $bestMatch = $pohon; }
            }
            similar_text($targetText, $pohonName, $percent);
            if ($percent > $highestPercent) { $highestPercent = $percent; $bestMatch = $pohon; }
        }

        return ($highestPercent >= 70) ? $bestMatch : null;
    }

    private function getDirectIndicators($node) {
        if (!$node) return collect([]);
        return collect($node->indikators ?? [])
            ->filter(fn($i) => !empty($i->nama_indikator))
            ->unique(fn($i) => strtolower(trim($i->nama_indikator)))
            ->values();
    }

    private function getStopIds($collection, $type) {
        return $collection->map(function($item) use ($type) {
            $node = $this->findPohonNode($item, $type);
            return $node ? $node->id : null;
        })->filter()->toArray();
    }

    private function createVirtualRows($parentNode, $stopIds, $textField) {
        $rows = collect([]);
        if (!$parentNode || !$parentNode->children) return $rows;

        foreach ($parentNode->children as $child) {
            if (in_array($child->id, $stopIds)) continue;

            $virtualRow = new \stdClass();
            $virtualRow->{$textField} = ''; // Kosongkan nama agar kolom Sasaran bersih
            $virtualRow->indikators_from_pohon = $this->getDirectIndicators($child);
            
            if($textField == 'outcome') {
                $virtualRow->program = (object)['kode' => '', 'nama' => ''];
            }

            // Opsional: Cek jika indikator kosong, apakah baris tetap ditampilkan?
            // Jika ya, langsung push. Jika tidak, cek count > 0.
            $rows->push($virtualRow);

            $childRows = $this->createVirtualRows($child, $stopIds, $textField);
            $rows = $rows->concat($childRows);
        }
        return $rows;
    }

    // =================================================================================
    // 3. RENDER (TAMPILAN WEB)
    // =================================================================================

    public function render()
    {
        // Panggil Single Source of Truth tadi
        $data = $this->getDataRenstra();
        return view('livewire.dokumen-renstra', $data);
    }

    // =================================================================================
    // 4. EXPORT FUNCTIONS (PDF & EXCEL)
    // =================================================================================

    public function downloadPdf()
    {
        $data = $this->getDataRenstra();
        
        // Kita gunakan view khusus cetak yang bersih (tanpa tombol edit/navigasi)
        $pdf = Pdf::loadView('cetak.dokumen-renstra', $data);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Dokumen-Renstra-' . $this->periode . '.pdf');
    }

    public function downloadExcel()
    {
        $data = $this->getDataRenstra();
        
        // Panggil Class Export dan inject datanya
        return Excel::download(new DokumenRenstraExport($data), 'Dokumen-Renstra-' . $this->periode . '.xlsx');
    }

    // =================================================================================
    // 5. MODAL EDIT
    // =================================================================================

    public function openEditModal()
    {
        $this->edit_nomor_dokumen = $this->nomor_dokumen;
        $this->edit_tanggal_penetapan = $this->tanggal_dokumen; 
        $this->isOpenEdit = true;
    }

    public function closeModal()
    {
        $this->isOpenEdit = false;
        $this->resetValidation();
    }

    public function updateRenstra()
    {
        $this->validate([
            'edit_nomor_dokumen' => 'required',
            'edit_tanggal_penetapan' => 'required',
        ]);
        $this->nomor_dokumen = $this->edit_nomor_dokumen;
        $this->tanggal_dokumen = $this->edit_tanggal_penetapan;
        $this->closeModal();
    }
}