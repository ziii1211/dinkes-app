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
        // Ambil semua node pohon kinerja beserta indikatornya dalam satu query agar performa cepat
        $all_pohons = PohonKinerja::with('indikators')->get();

        // =================================================================================
        // 2. FUNGSI REKURSIF PENGAMBIL INDIKATOR (CASCADING & UNIK)
        // =================================================================================
        // Mengambil indikator node saat ini + semua indikator anak-anaknya di bawahnya
        
        $getRecursiveIndicators = null;
        $getRecursiveIndicators = function($node) use ($all_pohons, &$getRecursiveIndicators) {
            if (!$node) return collect([]);

            // A. Ambil indikator milik node ini sendiri
            $indicators = $node->indikators ?? collect([]);

            // B. Cari anak-anak dari node ini (menggunakan filter collection, bukan query DB)
            $children = $all_pohons->where('parent_id', $node->id);

            // C. Loop setiap anak dan ambil indikatornya secara rekursif (turunan)
            foreach ($children as $child) {
                $indicators = $indicators->merge($getRecursiveIndicators($child));
            }

            return $indicators;
        };

        // Wrapper utama untuk digunakan di map
        $getIndicatorsForNode = function($node) use ($getRecursiveIndicators) {
            if (!$node) return collect([]);

            // Ambil semua indikator (Induk + Turunan)
            $rawIndicators = $getRecursiveIndicators($node);

            // Filter Duplikat dengan Normalisasi Teks
            // (Mengatasi beda spasi atau huruf besar kecil)
            return $rawIndicators->unique(function ($item) {
                return strtolower(trim($item->nama_indikator));
            })->values(); // Reset key array
        };

        // =================================================================================
        // 3. FUNGSI PENCARIAN NODE (MATCHING YANG LEBIH KETAT)
        // =================================================================================
        // Mencari node PohonKinerja yang cocok dengan data dokumen (Tujuan/Sasaran/dll)

        $findPohonNode = function($item, $type) use ($all_pohons) {
            
            // --- PRIORITAS 1: PENCARIAN BY ID (KHUSUS TUJUAN) ---
            // Jika ada relasi ID yang jelas (seperti di model Tujuan), gunakan itu.
            if ($type === 'tujuan' && isset($item->id)) {
                $matchById = $all_pohons->where('tujuan_id', $item->id)->first();
                if ($matchById) return $matchById;
            }

            // --- PERSIAPAN TEKS ---
            // Bersihkan teks dari simbol dan ubah ke huruf kecil
            $cleaner = function($str) {
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $str ?? '')));
            };

            $targetText = '';
            if ($type === 'tujuan') {
                $targetText = $cleaner($item->tujuan ?? $item->sasaran_rpjmd);
            } elseif ($type === 'sasaran') {
                $targetText = $cleaner($item->sasaran);
            } elseif ($type === 'outcome') {
                $targetText = $cleaner($item->outcome);
            } elseif ($type === 'kegiatan' || $type === 'sub_kegiatan') {
                $targetText = $cleaner($item->nama);
            }

            if (empty($targetText)) return null;

            // --- PRIORITAS 2: EXACT MATCH (TEKS PERSIS SAMA) ---
            // Cari node yang namanya persis sama dengan target
            $exactMatch = $all_pohons->first(function($pohon) use ($cleaner, $targetText) {
                return $cleaner($pohon->nama_pohon) === $targetText;
            });
            if ($exactMatch) return $exactMatch;

            // --- PRIORITAS 3: FUZZY MATCH (KEMIRIPAN KATA) ---
            // Jika tidak ada yang persis, cari yang paling mirip (Skor Dice Coefficient)
            $bestMatch = null;
            $bestScore = 0;
            $targetWords = array_filter(explode(' ', $targetText)); // Pecah jadi kata-kata

            foreach ($all_pohons as $pohon) {
                $pohonName = $cleaner($pohon->nama_pohon);
                $pohonWords = array_filter(explode(' ', $pohonName));

                if (count($pohonWords) == 0 || count($targetWords) == 0) continue;

                // Hitung kemiripan kata (Dice Coefficient)
                $intersection = array_intersect($pohonWords, $targetWords);
                $diceScore = (2 * count($intersection)) / (count($pohonWords) + count($targetWords));

                // Simpan skor tertinggi
                if ($diceScore > $bestScore) {
                    $bestScore = $diceScore;
                    $bestMatch = $pohon;
                }
            }

            // Hanya kembalikan jika kemiripan minimal 75% (0.75) agar kotak tidak salah
            // Sebelumnya 0.6, dinaikkan agar lebih akurat
            if ($bestScore >= 0.75) {
                return $bestMatch;
            }

            return null;
        };

        // =================================================================================
        // 4. MAPPING DATA KE VIEW
        // =================================================================================
        // Loop setiap data master dan cari pasangannya di Pohon Kinerja

        $tujuans = Tujuan::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'tujuan');
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

        $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            // Prioritaskan indikator manual dari DB sub_kegiatan jika ada
            if($item->indikators && $item->indikators->isNotEmpty()) {
                // Konversi format indikator manual agar sama strukturnya dengan indikator pohon
                // Asumsi indikator manual punya properti 'keterangan' yang dipetakan ke 'nama_indikator'
                $manualIndikators = $item->indikators->map(function($ind) {
                    $obj = new \stdClass();
                    $obj->nama_indikator = $ind->keterangan ?? $ind->nama_indikator;
                    return $obj;
                });
                $item->indikators_from_pohon = $manualIndikators;
            } else {
                // Jika manual kosong, baru cari di pohon
                $node = $findPohonNode($item, 'sub_kegiatan');
                $item->indikators_from_pohon = $getIndicatorsForNode($node);
            }
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
    // 5. FUNGSI MODAL EDIT (DATA DOKUMEN)
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