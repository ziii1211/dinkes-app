<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Realisasi Tahunan</title>
    <style>
        @page { size: A4 landscape; margin: 1.5cm 1.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; line-height: 1.3; }
        
        .table-kop { width: 100%; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .table-kop td { border: none; vertical-align: middle; }
        .logo-img { width: 75px; height: auto; }
        .header-title { font-size: 13pt; font-weight: bold; text-align: center; text-transform: uppercase; }

        .table-identitas { width: 100%; margin-bottom: 20px; font-weight: bold; font-size: 10.5pt; }
        .table-identitas td { border: none; padding: 3px 0; vertical-align: top; }

        .table-main { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .table-main th, .table-main td { border: 1px solid #000; padding: 6px; vertical-align: middle; }
        .table-main th { text-align: center; font-weight: bold; }
        
        .table-budget { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .table-budget th, .table-budget td { border: 1px solid #000; padding: 6px; }
        .table-budget th { text-align: center; font-weight: bold; }

        .table-signature { width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid; }
        .table-signature td { border: none; text-align: center; padding: 0; vertical-align: top; }
        .row-space td { height: 80px; }
        .name-underline { font-weight: bold; text-decoration: underline; margin: 0 0 3px 0; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <table class="table-kop">
        <tr>
            <td style="width: 12%; text-align: center;"><img src="{{ public_path('logo pemprov.png') }}" class="logo-img" alt="Logo Pemprov"></td>
            <td style="width: 76%;">
                <div class="header-title">
                    LAPORAN REALISASI KINERJA TAHUNAN<br>
                    TAHUN {{ $tahun }}<br>
                    DINAS KESEHATAN PROVINSI KALIMANTAN SELATAN
                </div>
            </td>
            <td style="width: 12%;"></td>
        </tr>
    </table>

    <table class="table-identitas">
        <tr><td style="width: 15%;">NAMA JABATAN</td><td style="width: 2%;">:</td><td style="text-transform: uppercase;">{{ $jabatan->nama ?? '-' }}</td></tr>
        <tr><td>PEJABAT PENILAI</td><td>:</td><td style="text-transform: uppercase;">{{ $atasan->nama ?? 'KEPALA DINAS' }}</td></tr>
    </table>

    <div class="font-bold" style="margin-bottom: 8px;">A. REALISASI KINERJA (FISIK)</div>
    <table class="table-main">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Sasaran / Kinerja Utama</th>
                <th style="width: 25%;">Indikator Kinerja</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 10%;">Target PK</th>
                <th style="width: 10%;">Realisasi Akhir</th>
                <th style="width: 15%;">% Capaian</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($pk) && $pk->sasarans->count() > 0)
                @php $no = 1; @endphp
                @foreach($pk->sasarans as $sasaran)
                    @php 
                        $jumlahIndikator = $sasaran->indikators->count();
                        $rowspan = $jumlahIndikator > 0 ? $jumlahIndikator : 1;
                    @endphp
                    @if($jumlahIndikator > 0)
                        @foreach($sasaran->indikators as $index => $indikator)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ $rowspan }}" class="text-center" style="vertical-align: top;">{{ $no++ }}.</td>
                                    <td rowspan="{{ $rowspan }}" class="text-left" style="vertical-align: top;">{{ $sasaran->sasaran ?? '-' }}</td>
                                @endif
                                <td class="text-left">{{ $indikator->nama_indikator ?? '-' }}</td>
                                <td class="text-center">{{ $indikator->satuan }}</td>
                                
                                @php
                                    $colTarget = 'target_' . $tahun;
                                    $target = $indikator->$colTarget ?? $indikator->target ?? 0;
                                    $target = (float) str_replace(',', '.', (string)$target);

                                    $listRealisasi = isset($realisasiData[$indikator->id]) ? $realisasiData[$indikator->id] : collect([]);
                                    $realisasiSd = $listRealisasi->sum(function($item) {
                                        return (float) str_replace(',', '.', (string)$item->realisasi);
                                    });
                                    $capaianSd = ($target > 0) ? ($realisasiSd / $target * 100) : 0;
                                    
                                    $numTarget = (float)$target == (int)$target ? (int)$target : $target;
                                    $numRealisasi = (float)$realisasiSd == (int)$realisasiSd ? (int)$realisasiSd : $realisasiSd;
                                @endphp
                                <td class="text-center font-bold">{{ $numTarget }}</td>
                                <td class="text-center font-bold">{{ $numRealisasi }}</td>
                                <td class="text-center font-bold">{{ number_format($capaianSd, 2) }}%</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center">{{ $no++ }}.</td>
                            <td class="text-left">{{ $sasaran->sasaran ?? '-' }}</td>
                            <td class="text-center font-bold" style="color: red;" colspan="5">- Belum ada indikator -</td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr><td colspan="7" class="text-center" style="padding: 15px;">Data Kinerja Belum Tersedia</td></tr>
            @endif
        </tbody>
    </table>

    <div class="font-bold" style="margin-bottom: 8px;">B. REALISASI ANGGARAN (KEUANGAN)</div>
    <table class="table-budget">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Program / Kegiatan / Sub Kegiatan</th>
                <th style="width: 25%;">Pagu Anggaran</th>
                <th style="width: 25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAnggaran = 0; @endphp
            @if(isset($pk->anggarans) && $pk->anggarans->count() > 0)
                @foreach($pk->anggarans as $idx => $anggaran)
                    @php $totalAnggaran += $anggaran->anggaran; @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}.</td>
                        <td>{{ $anggaran->subKegiatan ? $anggaran->subKegiatan->nama : preg_replace('/^[\d\.]+\s*/', '', $anggaran->nama_program_kegiatan) }}</td>
                        <td class="text-right">Rp {{ number_format($anggaran->anggaran, 0, ',', '.') }}</td>
                        <td class="text-center">-</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-center font-bold">JUMLAH TOTAL</td>
                    <td class="text-right font-bold">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                    <td class="text-center">-</td>
                </tr>
            @else
                <tr><td colspan="4" class="text-center" style="padding: 15px;">Data Anggaran Belum Tersedia</td></tr>
            @endif
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <table class="table-signature">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center; padding-bottom: 10px;">Banjarmasin, 31 Desember {{ $tahun }}</td>
        </tr>
        <tr>
            <td style="text-align: center;">Mengetahui Atasan Langsung,</td>
            <td style="text-align: center;">Yang Melaporkan,</td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold; text-transform: uppercase;">{{ $atasan->nama ?? '(Jabatan Atasan)' }}</td>
            <td style="text-align: center; font-weight: bold; text-transform: uppercase;">{{ $jabatan->nama ?? '(Nama Jabatan)' }}</td>
        </tr>
        <tr class="row-space"><td></td><td></td></tr>
        <tr>
            <td style="text-align: center;">
                <p class="name-underline">{{ $atasan->pegawai->nama ?? '(Belum ada pejabat)' }}</p>
                <p style="margin: 0;">NIP. {{ $atasan->pegawai->nip ?? '-' }}</p>
            </td>
            <td style="text-align: center;">
                <p class="name-underline">{{ $jabatan->pegawai->nama ?? '(Belum ada pejabat)' }}</p>
                <p style="margin: 0;">NIP. {{ $jabatan->pegawai->nip ?? '-' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>