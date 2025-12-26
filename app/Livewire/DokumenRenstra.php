<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\PohonKinerja;
use App\Models\RenstraSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\DokumenRenstraExport;
use Barryvdh\DomPDF\Facade\Pdf; 
use Livewire\WithFileUploads;

class DokumenRenstra extends Component
{
    use WithFileUploads;

    public $unit_kerja = 'DINAS KESEHATAN';
    
    // Default values
    public $nomor_dokumen = '031';
    public $tanggal_dokumen = '12 September 2025';
    public $periode = '2025 - 2029';

    public $isOpenEdit = false;
    public $edit_nomor_dokumen;
    public $edit_tanggal_penetapan;
    
    public $file; 

    protected $all_pohons;

    // =================================================================================
    // 0. MOUNT
    // =================================================================================
    public function mount()
    {
        $setting = RenstraSetting::firstOrCreate(
            ['id' => 1],
            [
                'nomor_dokumen' => '031', 
                'tanggal_dokumen' => '12 September 2025'
            ]
        );

        $this->nomor_dokumen = $setting->nomor_dokumen;
        $this->tanggal_dokumen = $setting->tanggal_dokumen;
    }

    // =================================================================================
    // 1. CORE LOGIC
    // =================================================================================

    public function getDataRenstra()
    {
        // 1. Ambil data Pohon & HITUNG LEVEL (DEPTH)
        $this->all_pohons = PohonKinerja::with(['indikators', 'children'])->get();
        $this->calculateDepths(); 

        // 2. Siapkan Stop Lists (Daftar ID yang sudah ada di DB agar tidak digenerate ulang sebagai virtual)
        // Stop untuk Sasaran: Item yang ada di tabel Outcome (next level) ATAU di tabel Sasaran itu sendiri (current level)
        $idsInSasaranDb = $this->getStopIds(Sasaran::all(), 'sasaran');
        $idsInOutcomeDb = $this->getStopIds(Outcome::all(), 'outcome');
        $stopForSasaranVirtual = array_merge($idsInOutcomeDb, $idsInSasaranDb);

        // Stop untuk Outcome: Item yang ada di tabel Kegiatan (next level) ATAU di tabel Outcome itu sendiri
        $idsInKegiatanDb = $this->getStopIds(Kegiatan::all(), 'kegiatan');
        $stopForOutcomeVirtual = array_merge($idsInKegiatanDb, $idsInOutcomeDb);
        
        $stopForKegiatan = $this->getStopIds(SubKegiatan::all(), 'sub_kegiatan');

        // --- KOLOM 1: TUJUAN ---
        $tujuans = Tujuan::all()->map(function($item) {
            $node = $this->findPohonNode($item, 'tujuan');
            $item->indikators_from_pohon = $this->getDirectIndicators($node);
            return $item;
        });

        // --- KOLOM 2: SASARAN (FIX: HIDE ROOT TAPI SHOW CHILDREN) ---
        $finalSasarans = collect([]);
        foreach (Sasaran::all() as $item) {
            $node = $this->findPohonNode($item, 'sasaran');
            
            // A. Add Item Asli (Hanya jika BUKAN Root/Tujuan)
            $isRoot = ($node && isset($node->depth) && $node->depth == 0);
            
            if (!$isRoot) {
                $item->indikators_from_pohon = $this->getDirectIndicators($node); 
                $finalSasarans->push($item);
            }

            // B. Cari Anak/Turunan (Virtual Rows)
            // Tetap jalankan ini MESKIPUN $isRoot true, agar anak-anaknya tampil.
            // Gunakan $stopForSasaranVirtual agar anak yang SUDAH ada di DB Sasaran tidak muncul ganda.
            if ($node) {
                $virtualChildren = $this->createVirtualRows($node, $stopForSasaranVirtual, 'sasaran');
                $finalSasarans = $finalSasarans->concat($virtualChildren);
            }
        }

        // --- KOLOM 3: OUTCOME ---
        $finalOutcomes = collect([]);
        foreach (Outcome::with('program')->get() as $item) {
            $node = $this->findPohonNode($item, 'outcome');
            
            // Cek Depth Outcome (biasanya Level 2). Jika Level < 2, hati-hati duplikat.
            // Logika sama: Jika dia Level 1 (Sasaran) tapi nyasar ke Outcome, bisa di-skip display-nya, tapi anaknya tetap dicari.
            $isSasaranLevel = ($node && isset($node->depth) && $node->depth <= 1); 

            if (!$isSasaranLevel) {
                $item->indikators_from_pohon = $this->getDirectIndicators($node);
                $finalOutcomes->push($item);
            }

            if ($node) {
                $virtualChildren = $this->createVirtualRows($node, $stopForOutcomeVirtual, 'outcome');
                $finalOutcomes = $finalOutcomes->concat($virtualChildren);
            }
        }
        
        // --- KOLOM 4: KEGIATAN ---
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

        // --- KOLOM 5: SUB KEGIATAN ---
        $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) {
            $manual = $item->indikators ? $item->indikators->map(fn($ind) => (object)['nama_indikator' => $ind->keterangan ?? $ind->nama_indikator ?? '-']) : collect([]);
            
            $node = $this->findPohonNode($item, 'sub_kegiatan');
            $pohonInd = $this->getDirectIndicators($node);

            $item->indikators_from_pohon = $manual->concat($pohonInd)
                ->filter(fn($i) => !empty($i->nama_indikator) && $i->nama_indikator !== '-')
                ->unique(fn($i) => $this->normalizeText($i->nama_indikator))
                ->values();
            
            return $item;
        });

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
    // 2. HELPER FUNCTIONS
    // =================================================================================

    private function calculateDepths() {
        foreach ($this->all_pohons as $pohon) {
            $depth = 0;
            $curr = $pohon;
            $safety = 0;
            while ($curr && $curr->parent_id && $safety < 20) {
                $depth++;
                $parentId = $curr->parent_id;
                $curr = $this->all_pohons->where('id', $parentId)->first();
                $safety++;
            }
            $pohon->depth = $depth;
        }
    }

    private function normalizeText($text) {
        if (empty($text)) return '';
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function findPohonNode($item, $type) {
        if ($type === 'tujuan' && isset($item->id)) {
            $matchById = $this->all_pohons->where('tujuan_id', $item->id)->first();
            if ($matchById) return $matchById;
        }

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
            ->unique(fn($i) => $this->normalizeText($i->nama_indikator))
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
            // STOP jika ID anak ini ada di daftar stopIds 
            // (artinya anak ini sudah tampil sebagai data asli di database, jadi jangan buat duplikat virtual)
            if (in_array($child->id, $stopIds)) continue;

            $virtualRow = new \stdClass();
            $virtualRow->{$textField} = ''; // Kolom nama dikosongkan utk virtual
            $virtualRow->indikators_from_pohon = $this->getDirectIndicators($child);
            
            if($textField == 'outcome') {
                $virtualRow->program = (object)['kode' => '', 'nama' => ''];
            }

            $rows->push($virtualRow);

            $childRows = $this->createVirtualRows($child, $stopIds, $textField);
            $rows = $rows->concat($childRows);
        }
        return $rows;
    }

    // =================================================================================
    // 3. RENDER
    // =================================================================================

    public function render()
    {
        $data = $this->getDataRenstra();
        return view('livewire.dokumen-renstra', $data);
    }

    // =================================================================================
    // 4. EXPORT FUNCTIONS
    // =================================================================================

    public function downloadPdf()
    {
        $data = $this->getDataRenstra();
        $pdf = Pdf::loadView('cetak.dokumen-renstra', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Dokumen-Renstra-' . $this->periode . '.pdf');
    }

    public function downloadExcel()
    {
        $data = $this->getDataRenstra();
        return Excel::download(new DokumenRenstraExport($data), 'Dokumen-Renstra-' . $this->periode . '.xlsx');
    }

    // =================================================================================
    // 5. MODAL EDIT & SAVE
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
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'pimpinan') {
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin untuk mengubah dokumen ini.');
        }

        $this->validate([
            'edit_nomor_dokumen' => 'required|string|max:100', 
            'edit_tanggal_penetapan' => 'required|string|max:50',
        ]);

        RenstraSetting::updateOrCreate(
            ['id' => 1], 
            [
                'nomor_dokumen' => $this->edit_nomor_dokumen,
                'tanggal_dokumen' => $this->edit_tanggal_penetapan
            ]
        );

        session()->flash('message', 'Data dokumen berhasil diperbarui secara permanen.');

        return redirect()->route('matrik.dokumen');
    }

    public function save()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang boleh mengunggah dokumen.');
        }

        $this->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120', 
        ]);

        $filename = 'RENSTRA_' . time() . '.' . $this->file->getClientOriginalExtension();
        $this->file->storeAs('dokumen-rahasia', $filename);

        $this->file = null;
        session()->flash('message', 'Dokumen berhasil diunggah dengan aman.');
    }
}