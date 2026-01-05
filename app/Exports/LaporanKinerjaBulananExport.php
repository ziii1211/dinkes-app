<?php

namespace App\Exports;

use App\Models\Jabatan;
use App\Models\PenjelasanKinerja;
use App\Models\PerjanjianKinerja;
use App\Models\RealisasiKinerja;
use App\Models\RencanaAksi;
use App\Models\RealisasiRencanaAksi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanKinerjaBulananExport implements FromView, WithStyles, WithColumnWidths
{
    protected $jabatanId;
    protected $bulan;
    protected $tahun;

    public function __construct($jabatanId, $bulan, $tahun)
    {
        $this->jabatanId = $jabatanId;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $jabatan = Jabatan::with(['pegawai'])->find($this->jabatanId);

        // Ambil semua data penjelasan
        $penjelasans = PenjelasanKinerja::where('jabatan_id', $this->jabatanId)
                        ->where('bulan', $this->bulan)
                        ->where('tahun', $this->tahun)
                        ->get();

        $pk = PerjanjianKinerja::with(['sasarans.indikators'])
                ->where('jabatan_id', $this->jabatanId)
                ->where('tahun', $this->tahun)
                ->where('status_verifikasi', 'disetujui')
                ->first();

        // Data Realisasi
        $realisasiData = collect([]);
        if ($pk) {
            $indikatorIds = collect();
            foreach ($pk->sasarans as $sasaran) {
                $indikatorIds = $indikatorIds->merge($sasaran->indikators->pluck('id'));
            }
            $realisasiData = RealisasiKinerja::whereIn('indikator_id', $indikatorIds)
                                ->where('tahun', $this->tahun)
                                ->where('bulan', '<=', $this->bulan)
                                ->get()
                                ->groupBy('indikator_id');
        }

        // Data Rencana Aksi
        $rencanaAksis = RencanaAksi::where('jabatan_id', $this->jabatanId)
                            ->where('tahun', $this->tahun)
                            ->get();

        // Data Realisasi Rencana Aksi
        $aksiIds = $rencanaAksis->pluck('id');
        $realisasiAksiData = RealisasiRencanaAksi::whereIn('rencana_aksi_id', $aksiIds)
                                ->where('tahun', $this->tahun)
                                ->where('bulan', '<=', $this->bulan)
                                ->get()
                                ->groupBy('rencana_aksi_id');

        return view('cetak.laporan-kinerja-bulanan', [
            'jabatan' => $jabatan,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'penjelasans' => $penjelasans,
            'pk' => $pk,
            'realisasiData' => $realisasiData,
            'rencanaAksis' => $rencanaAksis,
            'realisasiAksiData' => $realisasiAksiData,
            'namaBulan' => $this->getNamaBulan($this->bulan)
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 35,
            'C' => 35,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 12,
            'H' => 10,
            'I' => 12,
            'J' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // 1. Tinggi Header Utama
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(50);

        // 2. Styling Header
        $sheet->getStyle('A4:J5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // 3. Styling Konten
        $sheet->getStyle('A6:J'.$lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('D6:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6:C'.$lastRow)->getAlignment()->setWrapText(true);

        // --- FITUR AUTO HEIGHT UNTUK MERGED CELL PENJELASAN ---
        // Kita loop dari baris 6 ke bawah untuk mencari label Penjelasan
        for ($row = 6; $row <= $lastRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();
            $cellValueString = (string)$cellValue;

            // Cek jika ini baris Header Bagian Penjelasan (Upaya, Hambatan, RTL)
            if (str_contains($cellValueString, 'Upaya :') || 
                str_contains($cellValueString, 'Hambatan :') || 
                str_contains($cellValueString, 'Rencana Tindak Lanjut :')) {
                
                // Baris datanya ada tepat di bawah header (row + 1)
                $dataRow = $row + 1;
                if ($dataRow > $lastRow) continue;

                // Ambil isi teks penjelasannya
                $content = $sheet->getCell('A' . $dataRow)->getValue();
                
                if (!empty($content)) {
                    // Hitung jumlah baris (Enter) manual
                    // PHPSpreadsheet merubah <br> menjadi \n saat import view
                    $newlines = substr_count($content, "\n");
                    
                    // Hitung juga wrap text berdasarkan panjang karakter
                    // Total lebar kolom A-J kira-kira 170 karakter
                    $charLength = strlen($content);
                    $wrapLines = ceil($charLength / 150); // Estimasi 1 baris muat 150 char
                    
                    // Ambil nilai terbesar antara jumlah enter atau jumlah wrap text
                    $totalLines = max($newlines + 1, $wrapLines);
                    
                    // Set Tinggi Baris (Estimasi 15 poin per baris + padding 10)
                    // Minimal tinggi 30
                    $height = max(30, $totalLines * 15 + 10);
                    
                    $sheet->getRowDimension($dataRow)->setRowHeight($height);
                    
                    // Pastikan Wrap Text aktif di baris data ini
                    $sheet->getStyle('A' . $dataRow)->getAlignment()->setWrapText(true);
                }
            }
        }

        return [];
    }

    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
        ];
        return $namaBulan[(int)$bulan] ?? '';
    }
}