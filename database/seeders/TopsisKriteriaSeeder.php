<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopsisKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriterias = [
            ['kode' => 'C1', 'nama' => 'Rata-rata Capaian Kinerja', 'tipe' => 'benefit', 'bobot' => 0.40],
            ['kode' => 'C2', 'nama' => 'Jumlah Isu Kritis', 'tipe' => 'cost', 'bobot' => 0.20],
            ['kode' => 'C3', 'nama' => 'Indikator Tercapai (>=100%)', 'tipe' => 'benefit', 'bobot' => 0.20],
            ['kode' => 'C4', 'nama' => 'Stabilitas Kinerja (Min Capaian)', 'tipe' => 'benefit', 'bobot' => 0.20],
        ];

        foreach ($kriterias as $kriteria) {
            \App\Models\TopsisKriteria::updateOrCreate(['kode' => $kriteria['kode']], $kriteria);
        }
    }
}
