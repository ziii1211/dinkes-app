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

class DokumenRenstra extends Component
{
    public $unit_kerja = 'DINAS KESEHATAN';
    public $nomor_dokumen = '031';
    public $tanggal_dokumen = '12 September 2025';
    public $periode = '2025 - 2029';

    public $isOpenEdit = false;
    public $edit_nomor_dokumen;
    public $edit_tanggal_penetapan;

    public function render()
    {
        // =================================================================================
        // 1. PERSIAPAN DATA (EAGER LOADING)
        // =================================================================================
        // Ambil indikator langsung dari eager loading
        $all_pohons = PohonKinerja::with('indikators')->get();

        // Helper: Normalisasi Teks untuk pencarian dan filter unik
        $normalizeText = function ($text) {
            if (empty($text)) return '';
            return strtolower(trim(preg_replace('/\s+/', ' ', $text)));
        };

        // =================================================================================
        // 2. FUNGSI PENGAMBIL INDIKATOR (LANGSUNG / DIRECT ONLY)
        // =================================================================================
        // PERBAIKAN: Tidak lagi mengambil indikator dari child (rekursif dihapus)
        // Agar data kolom 2 (Sasaran) tidak naik ke kolom 1 (Tujuan)
        
        $getIndicatorsForNode = function($node) use ($normalizeText) {
            if (!$node) return collect([]);

            // Hanya ambil indikator yang menempel langsung pada node ini
            $rawIndicators = $node->indikators ? collect($node->indikators) : collect([]);

            // Tetap lakukan filter duplikat untuk berjaga-jaga jika ada data kotor di node yang sama
            return $rawIndicators
                ->filter(function($item) {
                    return !empty($item->nama_indikator);
                })
                ->map(function($item) use ($normalizeText) {
                    $item->temp_normalized_name = $normalizeText($item->nama_indikator);
                    return $item;
                })
                ->unique('temp_normalized_name') // Filter unik
                ->values(); // Reset index
        };

        // =================================================================================
        // 3. FUNGSI PENCARIAN NODE (MATCHING DATA MASTER <-> CASCADING)
        // =================================================================================

        $findPohonNode = function($item, $type) use ($all_pohons, $normalizeText) {
            
            // Prioritas 1: Pencarian by ID
            if ($type === 'tujuan' && isset($item->id)) {
                $matchById = $all_pohons->where('tujuan_id', $item->id)->first();
                if ($matchById) return $matchById;
            }

            // Siapkan teks target
            $targetText = '';
            if ($type === 'tujuan') {
                $targetText = $normalizeText($item->tujuan ?? $item->sasaran_rpjmd);
            } elseif ($type === 'sasaran') {
                $targetText = $normalizeText($item->sasaran);
            } elseif ($type === 'outcome') {
                $targetText = $normalizeText($item->outcome);
            } elseif ($type === 'kegiatan' || $type === 'sub_kegiatan') {
                $targetText = $normalizeText($item->nama);
            }

            if (empty($targetText)) return null;

            // Prioritas 2: Exact Match
            $exactMatch = $all_pohons->first(function($pohon) use ($normalizeText, $targetText) {
                return $normalizeText($pohon->nama_pohon) === $targetText;
            });
            if ($exactMatch) return $exactMatch;

            // Prioritas 3: Fuzzy Match (Dice Coefficient) untuk typo dikit
            $bestMatch = null;
            $bestScore = 0;
            $targetWords = array_filter(explode(' ', $targetText));

            if (count($targetWords) > 0) {
                foreach ($all_pohons as $pohon) {
                    $pohonName = $normalizeText($pohon->nama_pohon);
                    $pohonWords = array_filter(explode(' ', $pohonName));

                    if (count($pohonWords) == 0) continue;

                    $intersection = array_intersect($pohonWords, $targetWords);
                    $diceScore = (2 * count($intersection)) / (count($pohonWords) + count($targetWords));

                    if ($diceScore > $bestScore) {
                        $bestScore = $diceScore;
                        $bestMatch = $pohon;
                    }
                }
            }

            if ($bestScore >= 0.75) {
                return $bestMatch;
            }

            return null;
        };

        // =================================================================================
        // 4. MAPPING DATA KE VIEW
        // =================================================================================

        $tujuans = Tujuan::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'tujuan');
            // Hanya ambil indikator milik node ini, tidak termasuk anak-anaknya
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        });

        $sasarans = Sasaran::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'sasaran');
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        });

        $outcomes = Outcome::with('program')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'outcome');
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        });
        
        $kegiatans = Kegiatan::whereNotNull('output')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'kegiatan');
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        }); 

        $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode, $normalizeText) {
            
            // 1. Ambil Indikator Manual (dari DB SubKegiatan langsung)
            $manualIndicators = collect([]);
            if($item->indikators && $item->indikators->isNotEmpty()) {
                $manualIndicators = $item->indikators->map(function($ind) {
                    $obj = new \stdClass();
                    $obj->nama_indikator = $ind->keterangan ?? $ind->nama_indikator ?? '-';
                    return $obj;
                });
            }

            // 2. Ambil Indikator dari Pohon (Node SubKegiatan saja)
            $node = $findPohonNode($item, 'sub_kegiatan');
            $cascadingIndicators = $getIndicatorsForNode($node);

            // 3. Gabungkan Keduanya
            $mergedIndicators = $manualIndicators->concat($cascadingIndicators);

            // 4. Filter Duplikat (Manual vs Pohon)
            $item->indikators_from_pohon = $mergedIndicators
                ->filter(function($ind) { return !empty($ind->nama_indikator); })
                ->map(function($ind) use ($normalizeText) {
                    $ind->temp_normalized_name = $normalizeText($ind->nama_indikator);
                    return $ind;
                })
                ->unique('temp_normalized_name')
                ->values();

            return $item;
        });

        return view('livewire.dokumen-renstra', [
            'tujuans'       => $tujuans,
            'sasarans'      => $sasarans,
            'outcomes'      => $outcomes,
            'kegiatans'     => $kegiatans,
            'sub_kegiatans' => $sub_kegiatans,
        ]);
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