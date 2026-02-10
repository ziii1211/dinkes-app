<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Konsolidasi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header-table { width: 100%; margin-bottom: 20px; font-weight: bold; font-size: 11px; }
        .header-table td { padding: 2px; vertical-align: top; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .main-table th, .main-table td { border: 1px solid black; padding: 4px; vertical-align: top; }
        
        .bg-gray { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Width Kolom agar mirip PDF asli */
        .col-kode { width: 8%; }
        .col-uraian { width: 25%; }
        .col-indikator { width: 20%; }
        .col-satuan { width: 6%; }
        .col-pagu { width: 10%; }
        .col-bobot { width: 5%; }
        .col-fisik { width: 8%; }
        .col-keu { width: 8%; }
        .col-sisa { width: 10%; }

        /* Helper Page Break */
        .page-break { page-break-after: always; }
        
        /* Font size khusus header tabel */
        thead th { font-size: 9px; text-transform: uppercase; background-color: #e0e0e0; }
    </style>
</head>
<body>

    {{-- HEADER LAPORAN (Mirip PDF Hal 1 Atas) --}}
    <table class="header-table">
        <tr>
            <td width="15%">Kode SKPD</td>
            <td width="2%">:</td>
            <td width="83%">1.02.0.00.0.00.01.0000</td>
        </tr>
        <tr>
            <td>Nama SKPD</td>
            <td>:</td>
            <td>DINAS KESEHATAN</td>
        </tr>
        <tr>
            <td colspan="3" style="height: 10px;"></td>
        </tr>
        <tr>
            <td colspan="3" class="text-center" style="font-size: 14px; text-decoration: underline;">
                {{ strtoupper($laporan->judul) }}
            </td>
        </tr>
    </table>

    {{-- TABEL UTAMA --}}
    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" class="text-center align-middle">Kode</th>
                <th rowspan="2" class="text-center align-middle">Program / Kegiatan / Sub Kegiatan<br>Sub Output</th>
                <th rowspan="2" class="text-center align-middle">Satuan Unit</th>
                <th rowspan="2" class="text-center align-middle">Pagu Anggaran</th>
                <th rowspan="2" class="text-center align-middle">Bobot</th>
                
                {{-- REALISASI S/D BULAN INI --}}
                <th colspan="2" class="text-center">Realisasi S/D {{ $laporan->bulan }}</th>
                
                <th rowspan="2" class="text-center align-middle">Realisasi (Rp)</th>
                <th rowspan="2" class="text-center align-middle">Sisa Anggaran</th>
            </tr>
            <tr>
                <th class="text-center">Fisik %</th>
                <th class="text-center">Keu %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $progData)
                @php
                    $prog = $progData['program'];
                    $progAnggaran = $progData['anggaran'];
                    
                    $paguProg = $progAnggaran->pagu_anggaran ?? 0;
                    $realisasiProg = $progAnggaran->pagu_realisasi ?? 0;
                    $fisikProg = $progAnggaran->realisasi_fisik ?? 0; // % Fisik sudah persen
                    
                    // Hitung % Keuangan
                    $persenKeuProg = ($paguProg > 0) ? ($realisasiProg / $paguProg * 100) : 0;
                    
                    // Sisa Anggaran
                    $sisaProg = $paguProg - $realisasiProg;
                @endphp

                {{-- BARIS PROGRAM --}}
                <tr class="bg-gray">
                    <td class="font-bold">{{ $prog->kode }}</td>
                    <td class="font-bold">{{ strtoupper($prog->nama ?? $prog->nama_program) }}</td>
                    <td class="text-center">-</td>
                    <td class="text-right font-bold">{{ number_format($paguProg, 0, ',', '.') }}</td>
                    <td class="text-center">-</td> {{-- Bobot Program biasanya manual/kosong di contoh --}}
                    <td class="text-center font-bold">{{ number_format($fisikProg, 2, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ number_format($persenKeuProg, 2, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($realisasiProg, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($sisaProg, 0, ',', '.') }}</td>
                </tr>

                @foreach($progData['kegiatans'] as $kegData)
                    @php
                        $keg = $kegData['kegiatan'];
                        $kegAnggaran = $kegData['anggaran'];
                        
                        $paguKeg = $kegAnggaran->pagu_anggaran ?? 0;
                        $realisasiKeg = $kegAnggaran->pagu_realisasi ?? 0;
                        $fisikKeg = $kegAnggaran->realisasi_fisik ?? 0;
                        
                        $persenKeuKeg = ($paguKeg > 0) ? ($realisasiKeg / $paguKeg * 100) : 0;
                        $sisaKeg = $paguKeg - $realisasiKeg;
                    @endphp

                    {{-- BARIS KEGIATAN --}}
                    <tr>
                        <td class="font-bold">{{ $keg->kode }}</td>
                        <td class="font-bold" style="padding-left: 15px;">{{ $keg->nama ?? $keg->nama_kegiatan }}</td>
                        <td class="text-center">-</td>
                        <td class="text-right font-bold">{{ number_format($paguKeg, 0, ',', '.') }}</td>
                        <td class="text-center">-</td>
                        <td class="text-center font-bold">{{ number_format($fisikKeg, 2, ',', '.') }}</td>
                        <td class="text-center font-bold">{{ number_format($persenKeuKeg, 2, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($realisasiKeg, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($sisaKeg, 0, ',', '.') }}</td>
                    </tr>

                    {{-- BARIS SUB KEGIATAN --}}
                    @foreach($kegData['details'] as $sub)
                        @php
                            $paguSub = $sub->pagu_anggaran ?? 0;
                            $realisasiSub = $sub->pagu_realisasi ?? 0;
                            
                            // Fisik di sub kegiatan biasanya: Realisasi Fisik / Target * 100
                            // Tapi di contoh PDF, kolom fisik menampilkan angka persen langsung (misal 92.71)
                            $targetSub = $sub->target > 0 ? $sub->target : 1;
                            $fisikSub = ($sub->realisasi_fisik / $targetSub) * 100;
                            $fisikSub = min($fisikSub, 100); // Cap 100%

                            $persenKeuSub = ($paguSub > 0) ? ($realisasiSub / $paguSub * 100) : 0;
                            $sisaSub = $paguSub - $realisasiSub;
                        @endphp
                        <tr>
                            <td>{{ $sub->kode }}</td>
                            <td style="padding-left: 30px;">
                                {{-- Nama Sub Kegiatan --}}
                                <div>{{ explode('/', $sub->nama_program_kegiatan)[2] ?? 'Sub Kegiatan' }}</div>
                                {{-- Sub Output (Indikator) --}}
                                <div style="font-style: italic; font-size: 9px; color: #555; margin-top: 2px;">
                                    {!! nl2br(e($sub->sub_output)) !!}
                                </div>
                            </td>
                            <td class="text-center">{{ $sub->satuan_unit }}</td>
                            <td class="text-right">{{ number_format($paguSub, 0, ',', '.') }}</td>
                            <td class="text-center">-</td>
                            <td class="text-center">{{ number_format($fisikSub, 2, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($persenKeuSub, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($realisasiSub, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($sisaSub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                @endforeach
            @endforeach
        </tbody>
        
        {{-- FOOTER TOTAL --}}
        @php
            // Hitung Total Keseluruhan (Ambil dari query Program saja agar tidak double)
            $totalPagu = \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
                            ->whereNotNull('program_id')->sum('pagu_anggaran');
            
            $totalRealisasi = \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
                            ->whereNotNull('program_id')->sum('pagu_realisasi');
            
            $totalSisa = $totalPagu - $totalRealisasi;
            $totalPersenKeu = ($totalPagu > 0) ? ($totalRealisasi / $totalPagu * 100) : 0;
            
            // Rata-rata fisik program (opsional, sesuaikan rumus dinas)
            $avgFisik = \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
                            ->whereNotNull('program_id')->avg('realisasi_fisik') ?? 0;
        @endphp
        <tfoot>
            <tr class="bg-gray font-bold">
                <td colspan="3" class="text-center">TOTAL</td>
                <td class="text-right">{{ number_format($totalPagu, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($avgFisik, 2, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totalPersenKeu, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalSisa, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN (Opsional, sesuaikan posisi) --}}
    <br><br>
    <table width="100%">
        <tr>
            <td width="70%"></td>
            <td width="30%" class="text-center">
                Banjarmasin, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                Kepala Dinas Kesehatan<br>
                Provinsi Kalimantan Selatan
                <br><br><br><br><br>
                <b><u>Hj. RAUDATUL JANNAH, SKM, M.Kes</u></b><br>
                NIP. 19680815 198903 2 008
            </td>
        </tr>
    </table>

</body>
</html>