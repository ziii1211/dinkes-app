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
    // 1. CORE LOGIC (HOISTING FIX)
    // =================================================================================

    public function getDataRenstra()
    {
        // A. BUILD TREE IN MEMORY (Optimasi)
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

        // B. PROSES DATA DENGAN LOGIKA HOISTING (TARIK DATA KE ATAS)

        // 1. TUJUAN
        $finalTujuans = collect([]);
        foreach (Tujuan::with('indikators')->get() as $item) {
            $pohonNode = $this->flatPohons->where('tujuan_id', $item->id)->first();
            
            // Ambil Indikator Utama & Virtual Rows (Anak)
            $mainInds = $this->resolveIndikators($pohonNode, $item->indikators);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'tujuan') : collect([]);

            // LOGIKA HOISTING:
            // Jika Induk kosong indikatornya, tapi punya Anak, tarik Anak Pertama ke posisi Induk
            if ($mainInds->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift(); // Ambil & hapus anak pertama dari antrian
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            } else {
                $item->indikators_from_pohon = $mainInds;
            }

            $finalTujuans->push($item);
            $finalTujuans = $finalTujuans->concat($virtualRows);
        }

        // 2. SASARAN
        $finalSasarans = collect([]);
        foreach (Sasaran::with('indikators')->get() as $item) {
            $pohonNode = $this->findMatchingNode($item->sasaran);
            
            $mainInds = $this->resolveIndikators($pohonNode, $item->indikators);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'sasaran') : collect([]);

            if ($mainInds->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift(); 
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            } else {
                $item->indikators_from_pohon = $mainInds;
            }

            $finalSasarans->push($item);
            $finalSasarans = $finalSasarans->concat($virtualRows);
        }

        // 3. OUTCOME
        $finalOutcomes = collect([]);
        foreach (Outcome::with(['indikators', 'program'])->get() as $item) {
            $pohonNode = $this->findMatchingNode($item->outcome);

            $mainInds = $this->resolveIndikators($pohonNode, $item->indikators);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'outcome') : collect([]);

            if ($mainInds->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift();
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            } else {
                $item->indikators_from_pohon = $mainInds;
            }

            $finalOutcomes->push($item);
            $finalOutcomes = $finalOutcomes->concat($virtualRows);
        }

        // 4. KEGIATAN
        $finalKegiatans = collect([]);
        foreach (Kegiatan::with('indikators')->whereNotNull('output')->get() as $item) {
            $pohonNode = $this->findMatchingNode($item->nama);

            $mainInds = $this->resolveIndikators($pohonNode, $item->indikators);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'kegiatan') : collect([]);

            if ($mainInds->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift();
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            } else {
                $item->indikators_from_pohon = $mainInds;
            }

            $finalKegiatans->push($item);
            $finalKegiatans = $finalKegiatans->concat($virtualRows);
        }

        // 5. SUB KEGIATAN
        $finalSubKegiatans = collect([]);
        foreach (SubKegiatan::with('indikators')->get() as $item) {
            $pohonNode = $this->findMatchingNode($item->nama);

            $mainInds = $this->resolveIndikators($pohonNode, $item->indikators);
            $virtualRows = $pohonNode ? $this->generateVirtualRows($pohonNode, 'sub_kegiatan') : collect([]);

            if ($mainInds->isEmpty() && $virtualRows->isNotEmpty()) {
                $firstChild = $virtualRows->shift();
                $item->indikators_from_pohon = $firstChild->indikators_from_pohon;
            } else {
                $item->indikators_from_pohon = $mainInds;
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
    // 2. HELPER FUNCTIONS
    // =================================================================================

    private function generateVirtualRows($parentNode, $type)
    {
        $rows = collect([]);

        if (!$parentNode || !$parentNode->children_nodes) return $rows;

        foreach ($parentNode->children_nodes as $child) {
            // Cek apakah anak ini punya indikator valid
            $inds = $this->filterIndikators($child->indikators);
            
            // Jika ada indikator, buat baris baru (KOTAK BARU)
            if ($inds->isNotEmpty()) {
                $virtualRow = new stdClass();
                
                // Set data indikator anak ini
                $virtualRow->indikators_from_pohon = $inds;

                // Kosongkan field teks utama agar tampilan bersih
                $this->setEmptyFields($virtualRow, $type);

                $rows->push($virtualRow);
            }

            // REKURSIF: Cari cucu dan seterusnya
            $childRows = $this->generateVirtualRows($child, $type);
            $rows = $rows->concat($childRows);
        }

        return $rows;
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

    private function resolveIndikators($pohonNode, $masterIndikators)
    {
        // 1. Cek dari Pohon
        if ($pohonNode && $pohonNode->indikators && $pohonNode->indikators->isNotEmpty()) {
            return $this->filterIndikators($pohonNode->indikators);
        }
        // 2. Fallback Master
        if ($masterIndikators && $masterIndikators->isNotEmpty()) {
            return $this->filterIndikators($masterIndikators);
        }
        return collect([]);
    }

    private function filterIndikators($collection)
    {
        if (!$collection) return collect([]);
        return $collection->filter(function($ind) {
            return !empty($ind->nama_indikator) && $ind->nama_indikator !== '-';
        })->values();
    }

    private function findMatchingNode($text)
    {
        if (empty($text) || strlen($text) < 3) return null;
        if (!$this->flatPohons) return null;

        $target = $this->normalizeText($text);
        
        $exact = $this->flatPohons->first(function($pohon) use ($target) {
            return $this->normalizeText($pohon->nama_pohon) === $target;
        });
        if ($exact) return $exact;

        $bestMatch = null;
        $highestSim = 0;
        foreach ($this->flatPohons as $pohon) {
            $pohonName = $this->normalizeText($pohon->nama_pohon);
            similar_text($target, $pohonName, $percent);
            if ($percent > 90 && $percent > $highestSim) {
                $highestSim = $percent;
                $bestMatch = $pohon;
            }
        }
        return $bestMatch;
    }

    private function normalizeText($text)
    {
        return trim(strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $text)));
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