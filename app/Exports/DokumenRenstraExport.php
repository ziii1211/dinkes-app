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

    public function view(): View
    {
        return view('cetak.dokumen-renstra', $this->data);
    }

    /**
     * 1. Atur Lebar Kolom (Proporsional sesuai konten)
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Tujuan
            'B' => 20, // Sasaran
            'C' => 30, // Outcome
            'D' => 30, // Output
            'E' => 50, // Indikator (Paling lebar karena list)
            'F' => 45, // Program (Lebar sedang)
        ];
    }

    /**
     * 2. Styling Lengkap
     */
    public function styles(Worksheet $sheet)
    {
        // Hitung baris terakhir data
        $lastRow = $sheet->getHighestRow();

        // --- A. GLOBAL STYLE (Seluruh Halaman) ---
        // Font Arial, Size 10, Wrap Text (Turun baris), Rata Atas
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10, 
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP, // Data dimulai dari atas sel
                'wrapText' => true, // Teks panjang otomatis turun ke bawah
            ],
        ]);

        // --- B. JUDUL UTAMA (Baris 1) ---
        $sheet->mergeCells('A1:F1'); // Gabung kolom A-F
        $sheet->getRowDimension(1)->setRowHeight(30); // Tinggi baris judul
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true, 
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // --- C. HEADER TABEL (Baris 2) ---
        // Background Navy, Teks Putih, Bold, Rata Tengah
        $sheet->getRowDimension(2)->setRowHeight(35); // Tinggi baris header lebih lega
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Warna Putih
                'size' => 10
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1A2C42'], // Warna Navy Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Kiri-Kanan Tengah
                'vertical' => Alignment::VERTICAL_CENTER,   // Atas-Bawah Tengah (FIX ERROR DISINI)
            ],
        ]);

        // --- D. BORDER TABEL (Garis Hitam Tipis) ---
        // Terapkan border dari Header (A2) sampai Data Terakhir
        $sheet->getStyle('A2:F' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}