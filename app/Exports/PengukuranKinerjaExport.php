<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PengukuranKinerjaExport implements FromView, ShouldAutoSize, WithStyles
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
        
        // Format tanggal cetak (misal: Banjarmasin, 29 Desember 2025)
        $this->tanggalCetak = date('d') . ' ' . ($months[date('n')] ?? '') . ' ' . date('Y');
    }

    public function styles(Worksheet $sheet)
    {
        // Mengatur default font menjadi Arial 10 untuk kesan resmi
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        
        // Alignment vertical center untuk semua sel agar rapi
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            // Baris Header Tabel (Baris ke-8) Bold & Center
            8 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4B5563']], // Abu Gelap
            ],
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
            'namaBulan' => $this->namaBulan,
            'tanggalCetak' => $this->tanggalCetak
        ]);
    }
}