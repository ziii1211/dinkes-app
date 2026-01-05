<?php
// Script PHP Native untuk memaksa download file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Kinerja_Bulanan_".$bulan."_".$tahun.".xls");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .table-excel { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        .table-excel th, .table-excel td { border: 1px solid #000; padding: 4px; vertical-align: top; font-size: 11px; }
        .header-gray { background-color: #f2f2f2; text-align: center; font-weight: bold; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .align-middle { vertical-align: middle; }
        .no-border td { border: none !important; }
    </style>
</head>
<body>

    <table class="no-border" style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td colspan="10" class="text-center text-bold" style="font-size: 14px; height: 30px; vertical-align: middle;">
                LAPORAN PENGUKURAN KINERJA BULAN S/D BULAN {{ strtoupper($bulan) }} TAHUN {{ $tahun }}
            </td>
        </tr>
        <tr><td colspan="10"></td></tr>
        <tr>
            <td colspan="2" class="text-bold">NAMA SKPD</td>
            <td colspan="8" class="text-bold">: {{ $nama_skpd }}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-bold">NAMA JABATAN</td>
            <td colspan="8" class="text-bold">: {{ strtoupper($nama_jabatan) }}</td>
        </tr>
    </table>

    <br>

    <table class="table-excel">
        <thead>
            <tr class="header-gray">
                <th rowspan="2" width="30">No.</th>
                <th rowspan="2" width="250">Kinerja Utama</th>
                <th rowspan="2" width="250">Indikator</th>
                <th rowspan="2" width="80">Capaian Tahun Lalu</th>
                <th rowspan="2" width="80">Satuan Indikator Kinerja Utama</th>
                <th colspan="3">TARGET DAN CAPAIAN</th>
                <th rowspan="2" width="80">Target Akhir Renstra</th>
                <th rowspan="2" width="80">Capaian Kinerja Bulan {{ $bulan }}<br>(%)</th>
            </tr>
            <tr class="header-gray">
                <th width="80">Target</th>
                <th width="80">Realisasi</th>
                <th width="80">% Capaian</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($dataKinerja as $kinerja)
                @php 
                    $jmlIndikator = $kinerja->indikators->count(); 
                    $rowspan = $jmlIndikator > 0 ? $jmlIndikator : 1;
                @endphp

                @foreach($kinerja->indikators as $index => $indikator)
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ $rowspan }}" class="text-center align-middle">{{ $no++ }}.</td>
                            <td rowspan="{{ $rowspan }}" class="align-middle">{{ $kinerja->sasaran }}</td>
                        @endif

                        <td>{{ $indikator->nama_indikator }}</td>
                        <td class="text-center">100</td> 
                        <td class="text-center">{{ $indikator->satuan }}</td>

                        <td class="text-center">{{ $indikator->target_tahunan }}</td>
                        <td class="text-center">{{ $indikator->realisasi_bulan ?? '0' }}</td>
                        
                        @php
                            $capaian = $indikator->capaian_bulan ?? 0;
                        @endphp
                        <td class="text-center">{{ number_format($capaian, 2) }}%</td>

                        <td class="text-center">{{ $indikator->target_2029 ?? '-' }}</td>

                        <td class="text-center text-bold">{{ number_format($capaian, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <br>

    @if(isset($rencanaAksis) && $rencanaAksis->count() > 0)
        <table class="table-excel" style="margin-top: 10px;">
            <thead>
                <tr class="header-gray">
                    <th colspan="8" style="text-align: left; background-color: #e0e0e0;">
                        RENCANA AKSI / AKTIVITAS YANG BERHUBUNGAN DENGAN INDIKATOR
                    </th>
                </tr>
                <tr class="header-gray">
                    <th width="30">No.</th>
                    <th colspan="3">Aktivitas / Rencana Aksi</th>
                    <th width="80">Satuan</th>
                    <th width="80">Target</th>
                    <th width="80">Realisasi</th>
                    <th width="80">% Capaian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rencanaAksis as $index => $aksi)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}.</td>
                        <td colspan="3">{{ $aksi->nama_aksi }}</td>
                        <td class="text-center">{{ $aksi->satuan }}</td>
                        <td class="text-center">{{ $aksi->target }}</td>
                        <td class="text-center">{{ $aksi->realisasi_bulan ?? 0 }}</td>
                        <td class="text-center">
                            {{ $aksi->capaian_bulan ? number_format($aksi->capaian_bulan, 2).'%' : '0%' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    @endif

    <table class="no-border" style="width: 100%;">
        <tr>
            <td colspan="10" class="text-bold">Penjelasan per Indikator Kinerja</td>
        </tr>
        
        <tr>
            <td colspan="10">
                <strong>Upaya :</strong><br>
                @if($penjelasans->count() > 0)
                    <ol style="margin-top: 5px; margin-bottom: 5px; padding-left: 20px;">
                        @foreach($penjelasans as $item)
                            @if($item->upaya) <li>{{ $item->upaya }}</li> @endif
                        @endforeach
                    </ol>
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td colspan="10">
                <strong>Hambatan :</strong><br>
                @if($penjelasans->count() > 0)
                    <ol style="margin-top: 5px; margin-bottom: 5px; padding-left: 20px;">
                        @foreach($penjelasans as $item)
                            @if($item->hambatan) <li>{{ $item->hambatan }}</li> @endif
                        @endforeach
                    </ol>
                @else
                    -
                @endif
            </td>
        </tr>

        <tr>
            <td colspan="10">
                <strong>Rencana Tindak Lanjut :</strong><br>
                @if($penjelasans->count() > 0)
                    <ol style="margin-top: 5px; margin-bottom: 5px; padding-left: 20px;">
                        @foreach($penjelasans as $item)
                            @if($item->tindak_lanjut) <li>{{ $item->tindak_lanjut }}</li> @endif
                        @endforeach
                    </ol>
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <br><br>

    <table class="no-border" style="width: 100%; text-align: center;">
        <tr>
            <td width="50%"></td>
            <td width="50%">
                Banjarmasin, {{ date('d F Y') }} <br>
                Yang Melaporkan,
            </td>
        </tr>
        <tr>
            <td style="height: 70px; vertical-align: bottom;">
                {{ $pejabatPenilai->jabatan->nama_jabatan ?? 'Atasan Langsung' }}
            </td>
            <td style="height: 70px; vertical-align: bottom;">
                {{ $nama_jabatan }}
            </td>
        </tr>
        <tr>
            <td style="height: 40px;"></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <u><strong>{{ $pejabatPenilai->nama ?? 'Nama Atasan' }}</strong></u><br>
                NIP. {{ $pejabatPenilai->nip ?? '-' }}
            </td>
            <td>
                <u><strong>{{ $yangMelapor->nama ?? 'Nama Pelapor' }}</strong></u><br>
                NIP. {{ $yangMelapor->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>