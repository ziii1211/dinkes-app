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
use App\Models\PohonKinerja; // Tambahkan ini

class DokumenRenstraExport implements FromView, WithEvents
{
    public function view(): View
    {
        // 1. LOGIKA PENCARIAN POHON (Sama dengan Route PDF)
        $all_pohons = PohonKinerja::with('indikators')->get();
        $cleaner = fn($str) => strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $str)));

        $findPohonNode = function($item, $type) use ($all_pohons, $cleaner) {
            // Cek ID (Khusus Tujuan)
            if ($type === 'tujuan' && isset($item->id)) {
                $matchById = $all_pohons->where('tujuan_id', $item->id)->first();
                if ($matchById) return $matchById;
            }

            $targetName = $cleaner($type == 'tujuan' ? ($item->tujuan ?? $item->sasaran_rpjmd) : 
                         ($type == 'sasaran' ? $item->sasaran : 
                         ($type == 'outcome' ? $item->outcome : 
                         ($type == 'kegiatan' || $type == 'sub_kegiatan' ? $item->nama : ''))));

            foreach ($all_pohons as $pohon) {
                $pohonName = $cleaner($pohon->nama_pohon);
                if ($pohonName === $targetName || str_contains($pohonName, $targetName)) {
                    return $pohon;
                }
            }
            return null;
        };

        // 2. MAPPING DATA
        $tujuans = Tujuan::all()->map(function($item) use ($findPohonNode) {
            $node = $findPohonNode($item, 'tujuan');
            $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
            return $item;
        });

        $sasarans = Sasaran::all()->map(function($item) use ($findPohonNode) {
            $node = $findPohonNode($item, 'sasaran');
            $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
            return $item;
        });

        $outcomes = Outcome::with(['program'])->get()->map(function($item) use ($findPohonNode) {
            $node = $findPohonNode($item, 'outcome');
            $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
            return $item;
        });

        $kegiatans = Kegiatan::whereNotNull('output')->get()->map(function($item) use ($findPohonNode) {
            $node = $findPohonNode($item, 'kegiatan');
            $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
            return $item;
        });

        $sub_kegiatans = SubKegiatan::with('indikators')->get()->map(function($item) use ($findPohonNode) {
            $node = $findPohonNode($item, 'sub_kegiatan');
            $item->indikators_from_pohon = $node ? $node->indikators : collect([]);
            return $item;
        });

        $header = [
            'unit_kerja' => 'DINAS KESEHATAN',
            'periode' => '2025 - 2029'
        ];

        return view('cetak.dokumen-renstra', compact(
            'tujuans', 'sasarans', 'outcomes', 'kegiatans', 'sub_kegiatans', 'header'
        ));
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Setup Halaman
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1); 
                $sheet->getPageSetup()->setFitToHeight(0);

                // Font Default
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getParent()->getDefaultStyle()->getAlignment()->setWrapText(true);

                // Lebar Kolom
                $sheet->getColumnDimension('A')->setWidth(25); // Tujuan
                $sheet->getColumnDimension('B')->setWidth(25); // Sasaran
                $sheet->getColumnDimension('C')->setWidth(25); // Outcome
                $sheet->getColumnDimension('D')->setWidth(25); // Output
                $sheet->getColumnDimension('E')->setWidth(35); // Indikator
                $sheet->getColumnDimension('F')->setWidth(40); // Program/Kegiatan

                // Header
                $headerRow = 2; 
                $lastColumn = 'F';
                
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
                ]);
                
                $highestRow = $sheet->getHighestRow();
                if ($highestRow > $headerRow) {
                    $sheet->getStyle("A".($headerRow+1).":{$lastColumn}{$highestRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'indent' => 1],
                    ]);
                }
            },
        ];
    }
}