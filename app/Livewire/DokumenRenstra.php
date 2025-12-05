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
        // 1. AMBIL DATA POHON KINERJA
        $all_pohons = PohonKinerja::with('indikators')->get();

        // 2. FUNGSI PENGUMPUL INDIKATOR (HANYA MILIK NODE ITU SENDIRI)
        $getIndicatorsForNode = function($node) {
            return $node->indikators ?? collect([]);
        };

        // 3. FUNGSI PENCARI NODE (MATCHING TEKS)
        $findPohonNode = function($item, $type) use ($all_pohons) {
            
            // A. Cek ID (Prioritas Utama)
            if ($type === 'tujuan' && isset($item->id)) {
                $matchById = $all_pohons->where('tujuan_id', $item->id)->first();
                if ($matchById) return $matchById;
            }

            // B. Normalisasi Teks
            $cleaner = function($str) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $str)));
            };

            $targetTexts = [];
            if ($type === 'tujuan') {
                $targetTexts[] = $cleaner($item->tujuan);
                $targetTexts[] = $cleaner($item->sasaran_rpjmd);
            } elseif ($type === 'sasaran') {
                $targetTexts[] = $cleaner($item->sasaran);
            } elseif ($type === 'outcome') {
                $targetTexts[] = $cleaner($item->outcome);
            } elseif ($type === 'kegiatan') {
                $targetTexts[] = $cleaner($item->nama);
            } elseif ($type === 'sub_kegiatan') {
                // PERBAIKAN: Tambahkan pencarian untuk Sub Kegiatan
                $targetTexts[] = $cleaner($item->nama);
            }
            $targetTexts = array_filter($targetTexts);

            // C. Pencocokan (Scoring)
            $bestMatch = null;
            $bestScore = 0;

            foreach ($all_pohons as $pohon) {
                $pohonName = $cleaner($pohon->nama_pohon);
                $pohonWords = array_filter(explode(' ', $pohonName));
                
                if (count($pohonWords) == 0) continue;

                foreach ($targetTexts as $target) {
                    $targetWords = array_filter(explode(' ', $target));
                    if (count($targetWords) == 0) continue;

                    // Cek Contains
                    if (str_contains($pohonName, $target) || str_contains($target, $pohonName)) {
                        $tempScore = 0.85;
                        if ($pohonName === $target) $tempScore = 1.0;
                        
                        if ($tempScore > $bestScore) {
                            $bestScore = $tempScore;
                            $bestMatch = $pohon;
                        }
                    }

                    // Cek Word Overlap (Dice Coefficient)
                    $intersect = array_intersect($pohonWords, $targetWords);
                    $diceScore = (2 * count($intersect)) / (count($pohonWords) + count($targetWords));
                    
                    if ($diceScore > $bestScore) {
                        $bestScore = $diceScore;
                        $bestMatch = $pohon;
                    }
                }
            }

            // Ambang batas kemiripan 60%
            if ($bestScore >= 0.6) {
                return $bestMatch;
            }
            return null;
        };

        // 4. MAPPING DATA KE VIEW
        
        $tujuans = Tujuan::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'tujuan');
            $item->indikators_from_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
            return $item;
        });

        $sasarans = Sasaran::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'sasaran');
            $item->indikators_from_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
            return $item;
        });

        $outcomes = Outcome::with('program')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'outcome');
            $item->indikators_from_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
            return $item;
        });
        
        $kegiatans = Kegiatan::whereNotNull('output')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'kegiatan');
            $item->indikators_from_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
            return $item;
        }); 

        // E. SUB KEGIATAN (DIPERBAIKI)
        // Kita ambil indikatornya dari Pohon Kinerja (jika ada yang cocok)
        // Dan kita BIARKAN field output apa adanya (tidak di-null-kan)
        $sub_kegiatans = SubKegiatan::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'sub_kegiatan');
            $item->indikators_from_pohon = $node ? $getIndicatorsForNode($node) : collect([]);
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