<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DokumenRenstraExport implements FromView, WithColumnWidths, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    // PENTING: Kita arahkan ke view khusus Excel (bukan PDF)
    public function view(): View
    {
        // Pastikan Anda membuat file view ini (lihat Langkah 2)
        return view('cetak.dokumen-renstra-excel', $this->data);
    }

    /**
     * 1. Atur Lebar Kolom (Disesuaikan dengan konten Renstra)
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Tujuan (Narasi cukup panjang)
            'B' => 25, // Sasaran
            'C' => 30, // Outcome (Program)
            'D' => 30, // Output (Kegiatan)
            'E' => 50, // Indikator (Paling butuh ruang karena list)
            'F' => 40, // Nama Program/Kegiatan (Kode + Nama)
        ];
    }

    /**
     * 2. Styling Lengkap Agar Rapi
     */
    public function styles(Worksheet $sheet)
    {
        // Ambil baris terakhir data
        $lastRow = $sheet->getHighestRow();

        // --- A. GLOBAL STYLE (Seluruh Sel) ---
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP, // PENTING: Teks mulai dari atas
                'wrapText' => true, // PENTING: Teks turun ke bawah jika panjang
            ],
        ]);

        // --- B. JUDUL (Baris 1 & 2) ---
        // Kita asumsikan Baris 1: Unit Kerja, Baris 2: Periode
        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        
        // --- C. HEADER TABEL (Baris 4) ---
        // (Baris 3 kita kasih jeda kosong biar rapi)
        $headerRow = 4; 
        
        $sheet->getRowDimension($headerRow)->setRowHeight(30); 
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Teks Putih
                'size' => 10
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1A2C42'], // Background Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // --- D. BORDER TABEL ---
        // Beri garis hanya pada area Tabel (Mulai dari Header sampai bawah)
        $sheet->getStyle("A{$headerRow}:F{$lastRow}")
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}