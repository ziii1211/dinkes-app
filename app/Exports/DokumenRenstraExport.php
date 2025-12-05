<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// Models
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\Outcome;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;

class DokumenRenstraExport implements FromView, WithEvents
{
    public function view(): View
    {
        $tujuans = Tujuan::with('indikators')->get();
        $sasarans = Sasaran::with('indikators')->get();
        $outcomes = Outcome::with(['indikators', 'program'])->get();
        $kegiatans = Kegiatan::whereNotNull('output')->get();
        $sub_kegiatans = SubKegiatan::with('indikators')->get();

        $header = [
            'unit_kerja' => 'DINAS KESEHATAN',
            'periode' => '2025 - 2029'
        ];

        return view('cetak.dokumen-renstra', compact(
            'tujuans', 'sasarans', 'outcomes', 'kegiatans', 'sub_kegiatans', 'header'
        ));
    }

    /**
     * Register events untuk styling Excel agar rapi
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // --- 1. SETUP HALAMAN ---
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1); 
                $sheet->getPageSetup()->setFitToHeight(0);

                // --- 2. PENGATURAN FONT DEFAULT ---
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getParent()->getDefaultStyle()->getAlignment()->setWrapText(true);

                // --- 3. ATUR LEBAR KOLOM ---
                $sheet->getColumnDimension('A')->setWidth(20); // Tujuan
                $sheet->getColumnDimension('B')->setWidth(20); // Sasaran
                $sheet->getColumnDimension('C')->setWidth(25); // Outcome
                $sheet->getColumnDimension('D')->setWidth(25); // Output
                $sheet->getColumnDimension('E')->setWidth(30); // Indikator
                $sheet->getColumnDimension('F')->setWidth(40); // Program/Kegiatan

                // --- 4. DEFINISI AREA ---
                // PERBAIKAN: Berdasarkan gambar, Header (Tujuan, Sasaran...) ada di Baris 2
                $headerRow = 2; 
                $highestRow = $sheet->getHighestRow();
                $lastColumn = 'F';

                // Styling Judul Unit Kerja (Baris 1)
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // CATATAN: Kode sebelumnya menulis judul di Baris 4 ($sheet->mergeCells("A4...")).
                // Ini dihapus karena Baris 4 sudah berisi DATA. 
                // Jika ingin judul "MATRIKS RENSTRA", sebaiknya tambahkan langsung di file BLADE (View).

                // --- 5. STYLING HEADER TABEL (Baris 2) ---
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'], // Teks Putih
                        'size' => 10
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '1E293B'], // Biru Gelap
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER, // Teks header di tengah vertikal
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                
                // Terapkan style header ke Baris 2
                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray($headerStyle);
                $sheet->getRowDimension($headerRow)->setRowHeight(30); // Tinggi baris header cukup 30

                // --- 6. STYLING ISI TABEL (Mulai baris 3 sampai bawah) ---
                if ($highestRow > $headerRow) {
                    $dataStartRow = $headerRow + 1; // Data mulai dari baris 3
                    $dataRange = "A{$dataStartRow}:{$lastColumn}{$highestRow}";
                    
                    // Border untuk seluruh data
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_TOP,
                            'indent' => 1, // Jarak sedikit dari kiri sel
                        ],
                    ]);

                    // Zebra Striping (Baris Genap Abu-abu Muda)
                    // Loop dimulai dari data row (3)
                    for ($row = $dataStartRow; $row <= $highestRow; $row++) {
                        if ($row % 2 == 0) {
                            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('F8FAFC'); // Abu-abu sangat muda (Slate-50)
                        }
                    }
                }
            },
        ];
    }
}