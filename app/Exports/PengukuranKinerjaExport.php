<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Style\Fill; // Tidak perlu import Fill lagi

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
        // 1. Font Default: Arial 10
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        
        // 2. Alignment Default: Top & Wrap Text
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setWrapText(true);

        return [
            // Style Judul Laporan
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Style Header Tabel (Baris 6 & 7) - Hapus Fill Color
            '6:7' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Cari baris terakhir data
                $lastDataRow = 7; 
                for ($row = 8; $row <= $highestRow; $row++) {
                    $valA = $sheet->getCell('A' . $row)->getValue();
                    $valB = $sheet->getCell('B' . $row)->getValue();
                    $valC = $sheet->getCell('C' . $row)->getValue();
                    
                    if (str_contains((string)$valA, 'Penjelasan') || str_contains((string)$valB, 'Penjelasan') || str_contains((string)$valC, 'Banjarmasin')) {
                        break;
                    }
                    if (!empty($valA) || !empty($valB) || !empty($valC) || !empty($sheet->getCell('D'.$row)->getValue())) {
                         $lastDataRow = $row;
                    }
                }

                // 1. BORDER KOTAK PER DATA (Hitam)
                $sheet->getStyle('A6:J' . $lastDataRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // 2. STYLING HEADER AKTIVITAS (Hanya Bold & Italic, Tanpa Warna)
                for ($row = 8; $row <= $lastDataRow; $row++) {
                    $valC = $sheet->getCell('C' . $row)->getValue();
                    if ($valC === 'Aktifitas yang berhubungan dengan Indikator') {
                        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
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