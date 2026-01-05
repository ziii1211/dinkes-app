<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\PohonKinerja;
use App\Models\RenstraSetting;
use App\Exports\DokumenRenstraExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use stdClass;

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

    // Variable global untuk menyimpan tree memory
    protected $flatPohons;

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
    // 1. CORE LOGIC (STRICT CASCADING & UNLIMITED DEPTH)
    // =================================================================================

    public function getDataRenstra()
    {
        // A. BUILD TREE IN MEMORY
        $allPohons = PohonKinerja::with('indikators')->get();
        $this->flatPohons = $allPohons; 

        // Mapping Parent-Child di Memory
        $pohonDict = $allPohons->keyBy('id');
        foreach ($allPohons as $pohon) {
            $pohon->setRelation('children_nodes', collect([]));
        }
        foreach ($allPohons as $pohon) {
            if ($pohon->parent_id && isset($pohonDict[$pohon->parent_id])) {
                $pohonDict[$pohon->parent_id]->children_nodes->push($pohon);
            }
        }

        // B. PROSES DATA

        // 1. TUJUAN
        $finalTujuans = collect([]);
        foreach (Tujuan::all() as $item) {
            $pohonNode = $this->flatPohons->where('tujuan_id', $item->id)->first();
            if (!$pohonNode) {
                $pohonNode = $this->findMatchingNode($item->tujuan ?? $item->sasaran_rpjmd);
            }
            
            $item->indikators_from_pohon = $this->resolveIndikators($pohonNode);
            
            // Generate Virtual Rows (Anak, Cucu, dst)
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'tujuan') : collect([]);

            // Hoisting: Jika induk kosong, pinjam dari anak pertama
            if ($item->indikators_from_pohon->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift(); 
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            }

            $finalTujuans->push($item);
            $finalTujuans = $finalTujuans->concat($virtualRows);
        }

        // 2. SASARAN
        $finalSasarans = collect([]);
        foreach (Sasaran::all() as $item) {
            $pohonNode = $this->findMatchingNode($item->sasaran);
            
            $item->indikators_from_pohon = $this->resolveIndikators($pohonNode);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'sasaran') : collect([]);

            if ($item->indikators_from_pohon->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift(); 
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            }

            $finalSasarans->push($item);
            $finalSasarans = $finalSasarans->concat($virtualRows);
        }

        // 3. OUTCOME
        $finalOutcomes = collect([]);
        foreach (Outcome::with('program')->get() as $item) {
            $pohonNode = $this->findMatchingNode($item->outcome);

            $item->indikators_from_pohon = $this->resolveIndikators($pohonNode);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'outcome') : collect([]);

            if ($item->indikators_from_pohon->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift();
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            }

            $finalOutcomes->push($item);
            $finalOutcomes = $finalOutcomes->concat($virtualRows);
        }

        // 4. KEGIATAN
        $finalKegiatans = collect([]);
        foreach (Kegiatan::whereNotNull('output')->get() as $item) {
            $pohonNode = $this->findMatchingNode($item->nama); 

            $item->indikators_from_pohon = $this->resolveIndikators($pohonNode);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'kegiatan') : collect([]);

            if ($item->indikators_from_pohon->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift();
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            }

            $finalKegiatans->push($item);
            $finalKegiatans = $finalKegiatans->concat($virtualRows);
        }

        // 5. SUB KEGIATAN
        $finalSubKegiatans = collect([]);
        foreach (SubKegiatan::all() as $item) {
            $pohonNode = $this->findMatchingNode($item->nama);

            $item->indikators_from_pohon = $this->resolveIndikators($pohonNode);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'sub_kegiatan') : collect([]);

            if ($item->indikators_from_pohon->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift();
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            }

            $finalSubKegiatans->push($item);
            $finalSubKegiatans = $finalSubKegiatans->concat($virtualRows);
        }

        return [
            'unit_kerja'      => $this->unit_kerja,
            'nomor_dokumen'   => $this->nomor_dokumen,
            'tanggal_dokumen' => $this->tanggal_dokumen,
            'periode'         => $this->periode,
            'tujuans'         => $finalTujuans,
            'sasarans'        => $finalSasarans,
            'outcomes'        => $finalOutcomes,
            'kegiatans'       => $finalKegiatans,
            'sub_kegiatans'   => $finalSubKegiatans,
        ];
    }

    // =================================================================================
    // 2. HELPER FUNCTIONS (RECURSIVE & FILTERING)
    // =================================================================================

    private function resolveIndikators($pohonNode)
    {
        // STRICT MODE: Hanya ambil dari Pohon Kinerja
        if ($pohonNode && $pohonNode->indikators && $pohonNode->indikators->isNotEmpty()) {
            return $this->filterIndikators($pohonNode->indikators);
        }
        return collect([]);
    }

    private function filterIndikators($collection)
    {
        if (!$collection) return collect([]);
        
        // STRICT MODE: Hanya indikator yang punya 'nama_indikator'
        return $collection->filter(function($ind) {
            return !empty($ind->nama_indikator) && $ind->nama_indikator !== '-';
        })->values();
    }

    /**
     * PERBAIKAN UTAMA: generateVirtualRows sekarang Rekursif Tanpa Batas Level
     */
    private function generateVirtualRows($parentNode, $type)
    {
        $rows = collect([]);

        if (!$parentNode || !$parentNode->children_nodes) return $rows;

        foreach ($parentNode->children_nodes as $child) {
            // Ambil indikator anak ini
            $inds = $this->filterIndikators($child->indikators);
            
            // Jika anak ini punya indikator, buat baris tampilannya
            if ($inds->isNotEmpty()) {
                $virtualRow = new stdClass();
                $virtualRow->indikators_from_pohon = $inds;
                $this->setEmptyFields($virtualRow, $type);
                $rows->push($virtualRow);
            }

            // REKURSIF: Cari ke Cucu, Cicit, dst. (Unlimited Depth)
            // Hapus pembatasan if(type == ...) agar semua level tertelusuri
            $childRows = $this->generateVirtualRows($child, $type);
            $rows = $rows->concat($childRows);
        }

        return $rows;
    }

    private function findMatchingNode($text)
    {
        if (empty($text) || strlen($text) < 3) return null;
        if (!$this->flatPohons) return null;

        $target = $this->normalizeText($text);
        
        // 1. Exact Match
        $exact = $this->flatPohons->first(function($pohon) use ($target) {
            return $this->normalizeText($pohon->nama_pohon) === $target;
        });
        if ($exact) return $exact;

        // 2. Substring Match
        $substring = $this->flatPohons->first(function($pohon) use ($target) {
            $pohonName = $this->normalizeText($pohon->nama_pohon);
            return str_contains($pohonName, $target) || str_contains($target, $pohonName);
        });
        if ($substring) return $substring;

        // 3. Fuzzy Match
        $bestMatch = null;
        $highestSim = 0;
        foreach ($this->flatPohons as $pohon) {
            $pohonName = $this->normalizeText($pohon->nama_pohon);
            similar_text($target, $pohonName, $percent);
            
            if ($percent > 80 && $percent > $highestSim) {
                $highestSim = $percent;
                $bestMatch = $pohon;
            }
        }
        return $bestMatch;
    }

    private function setEmptyFields($row, $type)
    {
        if ($type === 'tujuan') {
            $row->tujuan = ''; 
            $row->sasaran_rpjmd = '';
        } elseif ($type === 'sasaran') {
            $row->sasaran = '';
        } elseif ($type === 'outcome') {
            $row->outcome = '';
            $row->program = (object)['kode' => '', 'nama' => ''];
        } elseif ($type === 'kegiatan') {
            $row->output = '';
            $row->kode = '';
            $row->nama = '';
        } elseif ($type === 'sub_kegiatan') {
            $row->output = '';
            $row->kode = '';
            $row->nama = '';
        }
    }

    private function normalizeText($text)
    {
        return trim(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $text)));
    }

    // =================================================================================
    // 3. RENDER & EXPORT
    // =================================================================================

    public function render()
    {
        $data = $this->getDataRenstra();
        return view('livewire.dokumen-renstra', $data);
    }

    public function downloadPdf()
    {
        $data = $this->getDataRenstra();
        $pdf = Pdf::loadView('cetak.dokumen-renstra', $data);
        $pdf->setPaper('a4', 'landscape'); 
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Dokumen-Renstra-' . $this->periode . '.pdf');
    }

    public function downloadExcel()
    {
        $data = $this->getDataRenstra();
        return Excel::download(new DokumenRenstraExport($data), 'Dokumen-Renstra-' . $this->periode . '.xlsx');
    }

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
        if (auth()->check() && !in_array(auth()->user()->role, ['admin', 'pimpinan'])) {
            abort(403, 'AKSES DITOLAK');
        }
        $this->validate([
            'edit_nomor_dokumen' => 'required|string|max:100', 
            'edit_tanggal_penetapan' => 'required|string|max:50',
        ]);
        RenstraSetting::updateOrCreate(['id' => 1], [
            'nomor_dokumen' => $this->edit_nomor_dokumen,
            'tanggal_dokumen' => $this->edit_tanggal_penetapan
        ]);
        $this->nomor_dokumen = $this->edit_nomor_dokumen;
        $this->tanggal_dokumen = $this->edit_tanggal_penetapan;
        $this->closeModal();
        session()->flash('message', 'Data dokumen berhasil diperbarui.');
    }

    public function save()
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang boleh mengunggah dokumen.');
        }
        $this->validate(['file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120']);
        $filename = 'RENSTRA_' . time() . '.' . $this->file->getClientOriginalExtension();
        $this->file->storeAs('dokumen-rahasia', $filename);
        $this->file = null;
        session()->flash('message', 'Dokumen berhasil diunggah dengan aman.');
    }
}