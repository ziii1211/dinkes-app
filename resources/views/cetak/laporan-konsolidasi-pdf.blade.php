<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Konsolidasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 11px;
        }

        .header-table td {
            padding: 2px;
            vertical-align: top;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid black;
            padding: 4px;
            vertical-align: top;
        }

        .bg-gray {
            background-color: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Width Kolom agar proporsional */
        .col-kode {
            width: 8%;
        }

        .col-uraian {
            width: 30%;
        }

        .col-satuan {
            width: 8%;
        }

        .col-pagu {
            width: 12%;
        }

        .col-bobot {
            width: 5%;
        }

        .col-realisasi {
            width: 12%;
        }

        .col-persen {
            width: 6%;
        }

        .col-sisa {
            width: 12%;
        }

        /* Helper Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Font size khusus header tabel */
        thead th {
            font-size: 9px;
            text-transform: uppercase;
            background-color: #e0e0e0;
        }

        /* Padding indentasi hierarki */
        .indent-kegiatan {
            padding-left: 15px !important;
        }

        .indent-sub {
            padding-left: 30px !important;
        }
    </style>
</head>

<body>

    {{-- HEADER LAPORAN --}}
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
                <th rowspan="2" class="text-center align-middle col-kode">Kode</th>
                <th rowspan="2" class="text-center align-middle col-uraian">Program / Kegiatan / Sub Kegiatan / Sub Output</th>
                <th rowspan="2" class="text-center align-middle col-satuan">Satuan Unit</th>
                <th rowspan="2" class="text-center align-middle col-pagu">Pagu Anggaran</th>
                <th rowspan="2" class="text-center align-middle col-bobot">Bobot</th>

                {{-- REALISASI S/D BULAN INI --}}
                <th colspan="2" class="text-center">Realisasi S/D {{ $laporan->bulan }}</th>

                <th rowspan="2" class="text-center align-middle col-realisasi">Realisasi (Rp)</th>
                <th rowspan="2" class="text-center align-middle col-sisa">Sisa Anggaran</th>
            </tr>
            <tr>
                <th class="text-center col-persen">Fisik %</th>
                <th class="text-center col-persen">Keu %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $progId => $group)
            @php
            $prog = $group['program'];

            // Ambil Anggaran Program
            $progAnggaran = $group['anggaran'] ?? \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
            ->where('program_id', $prog->id)
            ->first();

            $paguProg = $progAnggaran->pagu_anggaran ?? 0;
            $realisasiProg = $progAnggaran->pagu_realisasi ?? 0;
            $fisikProg = $progAnggaran->realisasi_fisik ?? 0;

            // Hitung % Keuangan Program
            $persenKeuProg = ($paguProg > 0) ? ($realisasiProg / $paguProg * 100) : 0;

            // Sisa Anggaran Program
            $sisaProg = $paguProg - $realisasiProg;
            @endphp

            {{-- BARIS PROGRAM --}}
            <tr class="bg-gray">
                <td class="font-bold">{{ $prog->kode }}</td>
                <td class="font-bold">{{ strtoupper($prog->nama ?? $prog->nama_program) }}</td>
                <td class="text-center">-</td>
                <td class="text-right font-bold">{{ number_format($paguProg, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-center font-bold">{{ number_format($fisikProg, 2, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($persenKeuProg, 2, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($realisasiProg, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($sisaProg, 0, ',', '.') }}</td>
            </tr>

            {{-- LOOP KEGIATAN --}}
            @foreach($group['kegiatans'] as $kegId => $kegData)
            @php
            $keg = $kegData['kegiatan'];
            $kegAnggaran = $kegData['anggaran'] ?? \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
            ->where('kegiatan_id', $keg->id)
            ->first();

            $paguKeg = $kegAnggaran->pagu_anggaran ?? 0;
            $realisasiKeg = $kegAnggaran->pagu_realisasi ?? 0;
            $fisikKeg = $kegAnggaran->realisasi_fisik ?? 0;

            $persenKeuKeg = ($paguKeg > 0) ? ($realisasiKeg / $paguKeg * 100) : 0;
            $sisaKeg = $paguKeg - $realisasiKeg;
            @endphp

            {{-- BARIS KEGIATAN --}}
            <tr>
                <td class="font-bold">{{ $keg->kode }}</td>
                <td class="font-bold indent-kegiatan">{{ $keg->nama ?? $keg->nama_kegiatan }}</td>
                <td class="text-center">-</td>
                <td class="text-right font-bold">{{ number_format($paguKeg, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-center font-bold">{{ number_format($fisikKeg, 2, ',', '.') }}</td>
                <td class="text-center font-bold">{{ number_format($persenKeuKeg, 2, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($realisasiKeg, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($sisaKeg, 0, ',', '.') }}</td>
            </tr>

            {{-- LOOP SUB KEGIATAN (DETAILS) --}}
            @foreach($kegData['details'] as $detail)
            @php
            $sub = $detail;
            $paguSub = $sub->pagu_anggaran ?? 0;
            $realisasiSub = $sub->pagu_realisasi ?? 0;

            $targetSub = $sub->target > 0 ? $sub->target : 1;
            $fisikSubVal = $sub->realisasi_fisik ?? 0;

            $persenFisikSub = ($targetSub > 0) ? ($fisikSubVal / $targetSub * 100) : 0;
            $persenFisikSub = min($persenFisikSub, 100);

            $persenKeuSub = ($paguSub > 0) ? ($realisasiSub / $paguSub * 100) : 0;
            $sisaSub = $paguSub - $realisasiSub;

            $namaSub = $sub->subKegiatan->nama ?? 'Sub Kegiatan';
            @endphp
            <tr>
                <td>{{ $sub->kode }}</td>
                <td class="indent-sub">
                    <div style="font-weight: bold;">{{ $namaSub }}</div>
                    <div style="font-style: italic; font-size: 9px; color: #444; margin-top: 2px;">
                        - {{ $sub->sub_output ?? '-' }}
                    </div>
                </td>
                <td class="text-center">{{ $sub->satuan_unit }}</td>
                <td class="text-right">{{ number_format($paguSub, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($persenFisikSub, 2, ',', '.') }}</td>
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
        // --- HITUNG TOTAL LANGSUNG DARI DATABASE (LEVEL PROGRAM) ---
        // Ini menjamin angkanya SAMA PERSIS dengan di Input Data Blade (Total Anggaran & Total Realisasi)

        $grandTotalPagu = \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
        ->whereNotNull('program_id') // Filter hanya baris Program
        ->sum('pagu_anggaran');

        $grandTotalRealisasi = \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
        ->whereNotNull('program_id') // Filter hanya baris Program
        ->sum('pagu_realisasi');

        // Hitung Sisa Anggaran
        $grandTotalSisa = $grandTotalPagu - $grandTotalRealisasi;

        // Hitung Persen Keuangan Total
        $grandTotalPersenKeu = ($grandTotalPagu > 0) ? ($grandTotalRealisasi / $grandTotalPagu * 100) : 0;

        // Hitung Rata-rata Fisik Total (Rata-rata dari capaian fisik Program)
        $grandAvgFisik = \App\Models\LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $laporan->id)
        ->whereNotNull('program_id')
        ->avg('realisasi_fisik') ?? 0;
        @endphp
        <tfoot>
            <tr class="bg-gray font-bold">
                <td colspan="3" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-right">{{ number_format($grandTotalPagu, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ number_format($grandAvgFisik, 2, ',', '.') }}</td>
                <td class="text-center">{{ number_format($grandTotalPersenKeu, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalRealisasi, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandTotalSisa, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN DINAMIS --}}
    @php
    // 1. SETUP DEFAULT (Kepala Dinas)
    $ttdJabatan = 'Kepala Dinas Kesehatan';
    $ttdInstansi = 'Provinsi Kalimantan Selatan';
    $ttdNama = 'Hj. RAUDATUL JANNAH, SKM, M.Kes';
    $ttdNip = '19680815 198903 2 008';

    // 2. CEK JIKA ADA PILIHAN JABATAN DARI MODAL
    if(isset($selectedJabatan) && $selectedJabatan) {
    $ttdJabatan = $selectedJabatan->nama;

    // Cari Pegawai yang menjabat
    $pegawai = \App\Models\Pegawai::where('jabatan_id', $selectedJabatan->id)->first();

    if($pegawai) {
    $ttdNama = $pegawai->nama;
    $ttdNip = $pegawai->nip;
    } else {
    $ttdNama = '.....................................';
    $ttdNip = '.....................................';
    }
    }
    @endphp

    <br><br>
    <table width="100%">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center" style="font-size: 11px;">
                Banjarmasin, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                {{ $ttdJabatan }}<br>
                {{ $ttdInstansi }}
                <br><br><br><br><br>
                <b><u>{{ $ttdNama }}</u></b><br>
                NIP. {{ $ttdNip }}
            </td>
        </tr>
    </table>

</body>

</html>