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
        // 1. PERSIAPAN DATA
        // =================================================================================
        // Hapus 'children' dari eager loading untuk mencegah kebocoran data anak ke induk
        $all_pohons = PohonKinerja::with('indikators')->get();

        // Helper: Normalisasi Teks (Pembersihan tanda baca & spasi)
        $normalizeText = function ($text) {
            if (empty($text)) return '';
            // Hapus karakter aneh, sisakan huruf dan angka, lalu lowercase
            $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
            return strtolower(trim(preg_replace('/\s+/', ' ', $text)));
        };

        // =================================================================================
        // 2. FUNGSI PENGAMBIL INDIKATOR (STRICT / DIRECT ONLY)
        // =================================================================================
        // PERBAIKAN UTAMA:
        // Fungsi ini hanya mengambil indikator yang menempel LANGSUNG pada node tersebut.
        // Tidak ada looping ke children/anak. Ini menjamin data tidak naik ke atas (tidak duplikat).

        $getIndicatorsForNode = function($node) {
            if (!$node) return collect([]);

            // Ambil relasi indikator langsung
            $indicators = $node->indikators ? collect($node->indikators) : collect([]);

            return $indicators
                ->filter(function($item) {
                    return !empty($item->nama_indikator);
                })
                // Pastikan unik berdasarkan nama (case insensitive) dalam satu kotak
                ->unique(function($item) {
                    return strtolower(trim($item->nama_indikator));
                })
                ->values();
        };

        // =================================================================================
        // 3. FUNGSI PENCARIAN NODE (SIMILAR TEXT MATCHING)
        // =================================================================================
        // Logika ini dipertahankan karena sudah berhasil menemukan node yang typo/mirip.

        $findPohonNode = function($item, $type) use ($all_pohons, $normalizeText) {
            
            // A. Coba cari berdasarkan ID (Paling Akurat)
            if ($type === 'tujuan' && isset($item->id)) {
                $matchById = $all_pohons->where('tujuan_id', $item->id)->first();
                if ($matchById) return $matchById;
            }

            // B. Siapkan teks target dari Tabel Master
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

            if (empty($targetText) || strlen($targetText) < 3) return null;

            // C. Pencarian Cerdas (Looping & Scoring)
            $bestMatch = null;
            $highestPercent = 0;

            foreach ($all_pohons as $pohon) {
                $pohonName = $normalizeText($pohon->nama_pohon);
                
                // 1. Exact Match (Persis Sama) - Prioritas Tertinggi
                if ($pohonName === $targetText) {
                    return $pohon;
                }

                // 2. Contains (Saling Mengandung Kata)
                if (Str::contains($pohonName, $targetText) || Str::contains($targetText, $pohonName)) {
                    $percent = 95; 
                    if ($percent > $highestPercent) {
                        $highestPercent = $percent;
                        $bestMatch = $pohon;
                    }
                }

                // 3. Similar Text (Cek Typo)
                similar_text($targetText, $pohonName, $percent);

                if ($percent > $highestPercent) {
                    $highestPercent = $percent;
                    $bestMatch = $pohon;
                }
            }

            // Threshold 70%: Cukup ketat agar tidak salah ambil, tapi toleran terhadap typo kecil
            if ($highestPercent >= 70) {
                return $bestMatch;
            }

            return null;
        };

        // =================================================================================
        // 4. MAPPING DATA KE VIEW
        // =================================================================================

        // TUJUAN
        $tujuans = Tujuan::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'tujuan');
            // Hanya ambil indikator milik TUJUAN itu sendiri
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        });

        // SASARAN
        $sasarans = Sasaran::all()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'sasaran');
            // Hanya ambil indikator milik SASARAN itu sendiri
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        });

        // OUTCOME (PROGRAM)
        $outcomes = Outcome::with('program')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'outcome');
            // Hanya ambil indikator milik OUTCOME/PROGRAM itu sendiri
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        });
        
        // KEGIATAN (OUTPUT)
        $kegiatans = Kegiatan::whereNotNull('output')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode) {
            $node = $findPohonNode($item, 'kegiatan');
            $item->indikators_from_pohon = $getIndicatorsForNode($node);
            return $item;
        }); 

        // SUB KEGIATAN
        $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) use ($findPohonNode, $getIndicatorsForNode, $normalizeText) {
            // 1. Indikator Manual (dari Input Subkegiatan)
            $manualIndicators = collect([]);
            if($item->indikators && $item->indikators->isNotEmpty()) {
                $manualIndicators = $item->indikators->map(function($ind) {
                    $obj = new \stdClass();
                    $obj->nama_indikator = $ind->keterangan ?? $ind->nama_indikator ?? '-';
                    return $obj;
                });
            }

            // 2. Indikator Pohon (dari Cascading)
            $node = $findPohonNode($item, 'sub_kegiatan');
            $cascadingIndicators = $getIndicatorsForNode($node);

            // 3. Gabungkan (Manual + Pohon)
            $mergedIndicators = $manualIndicators->concat($cascadingIndicators);
            
            $item->indikators_from_pohon = $mergedIndicators
                ->filter(function($ind) { return !empty($ind->nama_indikator); })
                ->unique(function($ind) use ($normalizeText) {
                     return $normalizeText($ind->nama_indikator);
                })
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
    // 5. MODAL EDIT & UPDATE (Tidak Berubah)
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