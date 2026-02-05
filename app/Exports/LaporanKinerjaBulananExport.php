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
use stdClass; 
use Carbon\Carbon;

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
        // 1. Ambil Tanggal Hari Ini (Zona Waktu WITA / Banjarmasin)
        $hariIni = Carbon::now('Asia/Makassar')->format('d');

        // 2. Ambil data Jabatan & Atasan
        $jabatan = Jabatan::with(['pegawai', 'parent.pegawai'])->find($this->jabatanId);
        $atasan = $jabatan->parent ?? null;

        // --- KHUSUS KEPALA DINAS (MANUAL GUBERNUR) ---
        if ($jabatan && stripos($jabatan->nama, 'Kepala Dinas') !== false) {
            $atasan = new stdClass();
            $atasan->nama = 'GUBERNUR KALIMANTAN SELATAN';
            $atasan->pegawai = new stdClass();
            $atasan->pegawai->nama = 'H. MUHIDIN';
            $atasan->pegawai->nip = '-';
            $atasan->pegawai->pangkat = '';
            $atasan->pegawai->golongan = '';
        }
        // ----------------------------------------------

        // 3. Ambil Penjelasan Kinerja (Tetap Filter Bulan Spesifik)
        $penjelasans = PenjelasanKinerja::where('jabatan_id', $this->jabatanId)
                        ->where('bulan', $this->bulan)
                        ->where('tahun', $this->tahun)
                        ->get();

        // 4. Ambil PK (FIX: LOGIKA SMART PK / EFFECTIVE DATE)
        // Masalah sebelumnya: where('bulan', $this->bulan) membuat bulan Feb kosong jika PK dibuat Jan.
        // Solusi: Cari PK yang bulan-nya <= bulan laporan, ambil yang paling baru.
        $pk = PerjanjianKinerja::with(['sasarans.indikators'])
                ->where('jabatan_id', $this->jabatanId)
                ->where('tahun', $this->tahun)
                ->where('status_verifikasi', 'disetujui')
                
                // [UPDATE PENTING DISINI]
                ->where('bulan', '<=', $this->bulan) 
                ->orderBy('bulan', 'desc') // Prioritaskan PK Perubahan (bulan lebih besar)
                ->orderBy('id', 'desc')
                ->first();

        // 5. Ambil Realisasi Indikator
        $realisasiData = collect([]);
        if ($pk) {
            $indikatorIds = collect();
            foreach ($pk->sasarans as $sasaran) {
                foreach ($sasaran->indikators as $indikator) {
                    // Inject Target Tahunan yang benar sesuai PK yang terpilih
                    $colTarget = 'target_' . $this->tahun;
                    $indikator->target_tahunan = $indikator->$colTarget ?? $indikator->target;
                    
                    $indikatorIds->push($indikator->id);
                }
            }
            
            // Logic realisasi tetap spesifik bulan ini (karena inputan bulanan)
            $realisasiData = RealisasiKinerja::whereIn('indikator_id', $indikatorIds)
                                ->where('tahun', $this->tahun)
                                ->where('bulan', $this->bulan) // Hanya bulan ini
                                ->get()
                                ->keyBy('indikator_id'); // Pakai keyBy agar mudah diakses di view
        }

        // 6. Ambil Rencana Aksi (Tetap Filter Bulan Spesifik)
        $rencanaAksis = RencanaAksi::where('jabatan_id', $this->jabatanId)
                            ->where('tahun', $this->tahun)
                            ->where('bulan', $this->bulan) 
                            ->get();

        // 7. Ambil Realisasi Rencana Aksi
        $aksiIds = $rencanaAksis->pluck('id');
        $realisasiAksiData = RealisasiRencanaAksi::whereIn('rencana_aksi_id', $aksiIds)
                                ->where('tahun', $this->tahun)
                                ->where('bulan', $this->bulan)
                                ->get()
                                ->keyBy('rencana_aksi_id');

        return view('cetak.laporan-kinerja-bulanan', [
            'jabatan' => $jabatan,
            'atasan'  => $atasan,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'penjelasans' => $penjelasans,
            'pk' => $pk,
            'realisasiData' => $realisasiData, // Dikirim sebagai Keyed Collection
            'rencanaAksis' => $rencanaAksis,
            'realisasiAksiData' => $realisasiAksiData,
            'namaBulan' => $this->getNamaBulan($this->bulan),
            'hariIni' => $hariIni 
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, 'B' => 35, 'C' => 35, 'D' => 10, 'E' => 10,
            'F' => 10, 'G' => 12, 'H' => 10, 'I' => 12, 'J' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(50);

        $sheet->getStyle('A4:J5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->getStyle('A6:J'.$lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('D6:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6:C'.$lastRow)->getAlignment()->setWrapText(true);

        for ($row = 6; $row <= $lastRow; $row++) {
            $cellValue = (string)$sheet->getCell('A' . $row)->getValue();
            if (str_contains($cellValue, 'Upaya :') || str_contains($cellValue, 'Hambatan :') || str_contains($cellValue, 'Rencana Tindak Lanjut :')) {
                $dataRow = $row + 1;
                if ($dataRow > $lastRow) continue;
                $content = $sheet->getCell('A' . $dataRow)->getValue();
                if (!empty($content)) {
                    $newlines = substr_count($content, "\n");
                    $charLength = strlen($content);
                    $wrapLines = ceil($charLength / 150); 
                    $totalLines = max($newlines + 1, $wrapLines);
                    $height = max(30, $totalLines * 15 + 10);
                    $sheet->getRowDimension($dataRow)->setRowHeight($height);
                    $sheet->getStyle('A' . $dataRow)->getAlignment()->setWrapText(true);
                }
            }
        }
        return [];
    }

    private function getNamaBulan($bulan)
    {
        $namaBulan = [1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'];
        return $namaBulan[(int)$bulan] ?? '';
    }
}