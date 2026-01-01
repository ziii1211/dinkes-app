<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents; // Tambahkan ini
use Maatwebsite\Excel\Events\AfterSheet;   // Tambahkan ini
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PengukuranKinerjaExport implements FromView, ShouldAutoSize, WithStyles, WithColumnWidths, WithEvents
{
    protected $pk;
    protected $rencanaAksis;
    protected $jabatan;
    protected $tahun;
    protected $bulan;
    protected $namaBulan;
    protected $tanggalCetak;

    public function __construct($pk, $rencanaAksis, $jabatan, $tahun, $bulan)
    {
        $this->pk = $pk;
        $this->rencanaAksis = $rencanaAksis;
        $this->jabatan = $jabatan;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $this->namaBulan = $months[$bulan] ?? 'Bulan';
        // Format Tanggal: Banjarmasin, 31 Desember 2026
        $this->tanggalCetak = 'Banjarmasin, ' . date('t', strtotime("$tahun-$bulan-01")) . ' ' . ($months[$bulan] ?? '') . ' ' . $tahun;
    }

    public function columnWidths(): array
    {
        // Menyesuaikan lebar kolom agar mirip format laporan
        return [
            'A' => 5,   // No
            'B' => 30,  // Kinerja Utama
            'C' => 35,  // Indikator
            'D' => 15,  // Capaian Tahun Lalu
            'E' => 15,  // Satuan
            'F' => 12,  // Target
            'G' => 12,  // Realisasi
            'H' => 12,  // Capaian
            'I' => 12,  // Target Akhir
            'J' => 20,  // Capaian Kinerja
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Set Global Font ke Arial 10
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        
        // 2. Alignment Global: Top & Wrap Text
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setWrapText(true);

        return [
            // Style Judul Laporan (Baris 2)
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Style Header Tabel (Baris 6 dan 7)
            '6:7' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9'] // Abu-abu muda
                ],
            ],
        ];
    }

    /**
     * Menggunakan Event untuk memberikan Border ke seluruh tabel data secara dinamis
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Cari baris terakhir yang berisi data (mengabaikan footer tanda tangan)
                // Kita akan mencari baris di mana kolom No (A) atau kolom Kinerja (B) terakhir terisi dalam konteks tabel
                // Namun cara paling aman untuk "kotak per data" adalah mendeteksi range tabel.
                
                $highestRow = $sheet->getHighestRow();
                
                // Asumsi: Kita mencari baris sebelum tulisan "Penjelasan per Indikator Kinerja"
                // Atau kita loop dari bawah ke atas.
                $lastDataRow = 7; // Default minimal header
                
                for ($row = 8; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    $cellValueB = $sheet->getCell('B' . $row)->getValue();
                    
                    // Stop jika ketemu footer "Penjelasan..."
                    if (str_contains((string)$cellValue, 'Penjelasan per Indikator') || str_contains((string)$cellValueB, 'Penjelasan per Indikator')) {
                        break;
                    }
                    $lastDataRow = $row;
                }

                // Terapkan Border All (Kotak) ke seluruh tabel data
                $tableRange = 'A6:J' . $lastDataRow;
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Styling khusus untuk baris "Aktifitas" (Background abu-abu tipis)
                // Kita loop ulang untuk mencari header aktifitas
                for ($row = 8; $row <= $lastDataRow; $row++) {
                    $valC = $sheet->getCell('C' . $row)->getValue();
                    if ($valC == 'Aktifitas yang berhubungan dengan Indikator') {
                        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2'] // Abu-abu sangat muda
                            ],
                            'font' => ['bold' => true, 'italic' => true]
                        ]);
                    }
                }
            },
        ];
    }

    public function view(): View
    {
        return view('cetak.pengukuran-kinerja-excel', [
            'pk' => $this->pk,
            'rencanaAksis' => $this->rencanaAksis,
            'jabatan' => $this->jabatan,
            'tahun' => $this->tahun,
            'bulan' => $this->bulan,
            'namaBulan' => strtoupper($this->namaBulan),
            'namaBulanKecil' => $this->namaBulan,
            'tanggalCetak' => $this->tanggalCetak
        ]);
    }
}