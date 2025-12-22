<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DokumenRenstraExport implements FromView, ShouldAutoSize, WithStyles
{
    /**
     * Menyimpan data yang dikirim dari Livewire
     */
    protected $data;

    /**
     * Constructor untuk menerima data array
     * * @param array $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Render view menjadi Excel
     * Kita menggunakan view yang SAMA dengan PDF agar tampilan tabelnya konsisten
     */
    public function view(): View
    {
        return view('cetak.dokumen-renstra', $this->data);
    }

    /**
     * Styling tambahan untuk Sheet Excel
     * (Opsional) Membuat baris pertama menjadi Bold
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}