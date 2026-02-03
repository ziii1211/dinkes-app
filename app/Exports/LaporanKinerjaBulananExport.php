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
use Carbon\Carbon; // PENTING: Import Carbon untuk Tanggal

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

        // 3. Ambil Penjelasan Kinerja (Sudah Benar Filter Bulan)
        $penjelasans = PenjelasanKinerja::where('jabatan_id', $this->jabatanId)
                        ->where('bulan', $this->bulan)
                        ->where('tahun', $this->tahun)
                        ->get();

        // 4. Ambil PK (FIX: Filter Berdasarkan Bulan yang Dipilih)
        $pk = PerjanjianKinerja::with(['sasarans.indikators'])
                ->where('jabatan_id', $this->jabatanId)
                ->where('tahun', $this->tahun)
                ->where('status_verifikasi', 'disetujui')
                // Tambahkan Filter Bulan agar data bulan lain tidak terambil
                ->where('bulan', $this->bulan) 
                ->latest('id') // Ambil revisi terbaru jika ada
                ->first();

        // 5. Ambil Realisasi Indikator
        $realisasiData = collect([]);
        if ($pk) {
            $indikatorIds = collect();
            foreach ($pk->sasarans as $sasaran) {
                $indikatorIds = $indikatorIds->merge($sasaran->indikators->pluck('id'));
            }
            // Ubah logic realisasi agar spesifik bulan ini (sesuai permintaan user)
            $realisasiData = RealisasiKinerja::whereIn('indikator_id', $indikatorIds)
                                ->where('tahun', $this->tahun)
                                ->where('bulan', $this->bulan) // Hanya bulan ini
                                ->get()
                                ->groupBy('indikator_id');
        }

        // 6. Ambil Rencana Aksi (FIX: Filter Berdasarkan Bulan)
        $rencanaAksis = RencanaAksi::where('jabatan_id', $this->jabatanId)
                            ->where('tahun', $this->tahun)
                            // Filter Bulan Wajib Ada agar Rencana Aksi bulan lain tidak bocor
                            ->where('bulan', $this->bulan) 
                            ->get();

        // 7. Ambil Realisasi Rencana Aksi
        $aksiIds = $rencanaAksis->pluck('id');
        $realisasiAksiData = RealisasiRencanaAksi::whereIn('rencana_aksi_id', $aksiIds)
                                ->where('tahun', $this->tahun)
                                ->where('bulan', $this->bulan) // Hanya bulan ini
                                ->get()
                                ->groupBy('rencana_aksi_id');

        return view('cetak.laporan-kinerja-bulanan', [
            'jabatan' => $jabatan,
            'atasan'  => $atasan,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'penjelasans' => $penjelasans,
            'pk' => $pk,
            'realisasiData' => $realisasiData,
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