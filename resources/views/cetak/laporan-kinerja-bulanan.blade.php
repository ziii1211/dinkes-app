<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kinerja Bulanan</title>
    <style>
        /* SETUP HALAMAN */
        @page { size: A4 landscape; margin: 1.5cm 1.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 9.5pt; color: #000; line-height: 1.3; }
        
        /* KOP SURAT */
        .table-kop { width: 100%; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .table-kop td { border: none; vertical-align: middle; }
        .logo-img { width: 75px; height: auto; }
        .header-title { font-size: 13pt; font-weight: bold; text-align: center; text-transform: uppercase; line-height: 1.3; }

        /* IDENTITAS SKPD & JABATAN */
        .table-identitas { width: 100%; margin-bottom: 15px; font-weight: bold; font-size: 10pt; }
        .table-identitas td { border: none; padding: 3px 0; vertical-align: top; }

        /* TABEL UTAMA */
        .table-main { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-main th, .table-main td { border: 1px solid #000; padding: 6px; vertical-align: middle; }
        /* PERBAIKAN: Background color abu-abu dihapus agar polos */
        .table-main th { text-align: center; font-weight: bold; }
        
        /* TABEL PENJELASAN */
        .table-penjelasan { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .table-penjelasan td { border: none; padding: 2px 5px; vertical-align: top; }

        /* TANDA TANGAN */
        .table-signature { width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid; }
        .table-signature td { border: none; text-align: center; padding: 0; vertical-align: top; }
        .row-space td { height: 80px; }
        .name-underline { font-weight: bold; text-decoration: underline; margin: 0 0 3px 0; }

        /* UTILITIES */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    {{-- 1. KOP SURAT BERLOGO --}}
    <table class="table-kop">
        <tr>
            <td style="width: 12%; text-align: center;">
                <img src="{{ public_path('logo pemprov.png') }}" class="logo-img" alt="Logo Pemprov">
            </td>
            <td style="width: 76%;">
                <div class="header-title">
                    LAPORAN PENGUKURAN KINERJA S/D BULAN {{ strtoupper($namaBulan) }} TAHUN {{ $tahun }}<br>
                    DINAS KESEHATAN PROVINSI KALIMANTAN SELATAN
                </div>
            </td>
            <td style="width: 12%;"></td>
        </tr>
    </table>

    {{-- 2. IDENTITAS --}}
    <table class="table-identitas">
        <tr>
            <td style="width: 15%;">NAMA SKPD</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Dinas Kesehatan Provinsi Kalimantan Selatan</td>
        </tr>
        <tr>
            <td>NAMA JABATAN</td>
            <td>:</td>
            <td style="text-transform: uppercase;">{{ $jabatan->nama ?? '-' }}</td>
        </tr>
    </table>

    {{-- 3. TABEL DATA UTAMA --}}
    <table class="table-main">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No.</th>
                <th rowspan="2" style="width: 20%;">Kinerja Utama</th>
                <th rowspan="2" style="width: 22%;">Indikator</th>
                <th rowspan="2" style="width: 8%;">Capaian<br>Tahun Lalu</th>
                <th rowspan="2" style="width: 8%;">Satuan</th>
                <th colspan="3">TARGET DAN CAPAIAN</th>
                <th rowspan="2" style="width: 7%;">Target Akhir<br>Renstra</th>
                <th rowspan="2" style="width: 10%;">Capaian Kinerja<br>Bulan {{ ucfirst(strtolower($namaBulan)) }}<br>(Realisasi:Target)</th>
            </tr>
            <tr>
                <th style="width: 7%;">Target</th>
                <th style="width: 7%;">Realisasi s.d<br>Bulan Ini</th>
                <th style="width: 8%;">% Capaian</th>
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
                                    <td rowspan="{{ $rowspan }}" class="text-center" style="vertical-align: top;">{{ $no++ }}</td>
                                    <td rowspan="{{ $rowspan }}" class="text-left" style="vertical-align: top;">
                                        {{ $sasaran->sasaran ?? '-' }}
                                    </td>
                                @endif

                                <td class="text-left" style="vertical-align: top;">
                                    {{ $indikator->nama_indikator ?? $indikator->indikator ?? '-' }}
                                </td>
                                
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

                                <td class="text-center">-</td>
                                <td class="text-center">{{ $indikator->satuan }}</td>
                                <td class="text-center font-bold">{{ $numTarget }}</td>
                                <td class="text-center font-bold">{{ $numRealisasi }}</td>
                                <td class="text-center font-bold">{{ number_format($capaianSd, 2) }}%</td>
                                <td class="text-center">-</td>
                                <td class="text-center font-bold text-blue-800">
                                    {{ number_format($capaianSd, 2) }}%
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="text-left">{{ $sasaran->sasaran ?? '-' }}</td>
                            <td class="text-center font-bold" style="color: red;">- Belum ada indikator -</td>
                            <td colspan="7"></td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="10" class="text-center" style="padding: 15px;">Data Kinerja Belum Tersedia</td>
                </tr>
            @endif

            {{-- JIKA ADA RENCANA AKSI (AKTIVITAS) --}}
            @if(isset($rencanaAksis) && $rencanaAksis->count() > 0)
                {{-- PERBAIKAN: Background color abu-abu dihapus --}}
                <tr>
                    <td></td>
                    <td></td>
                    <td class="font-bold text-center">Aktifitas yang Berhubungan dengan Indikator</td>
                    <td class="font-bold text-center">Capaian Aktifitas<br>Tahun Lalu</td>
                    <td class="font-bold text-center">Satuan</td>
                    <td class="font-bold text-center">Target<br>Aktifitas</td>
                    <td class="font-bold text-center">Realisasi<br>Aktifitas</td>
                    <td colspan="3" class="font-bold text-center">Capaian<br>Aktifitas</td>
                </tr>

                @foreach($rencanaAksis as $aksi)
                    @php
                        $targetAksi = (float) str_replace(',', '.', (string)$aksi->target);
                        $listRealisasiAksi = isset($realisasiAksiData[$aksi->id]) ? $realisasiAksiData[$aksi->id] : collect([]);
                        $realisasiAksiSd = $listRealisasiAksi->sum(function($item) {
                            return (float) str_replace(',', '.', (string)$item->realisasi);
                        });
                        $capaianAksiSd = ($targetAksi > 0) ? ($realisasiAksiSd / $targetAksi * 100) : 0;
                        
                        $numTargetAksi = (float)$targetAksi == (int)$targetAksi ? (int)$targetAksi : $targetAksi;
                        $numRealAksi = (float)$realisasiAksiSd == (int)$realisasiAksiSd ? (int)$realisasiAksiSd : $realisasiAksiSd;
                    @endphp
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-left" style="vertical-align: top;">{{ $aksi->nama_aksi }}</td>
                        <td class="text-center">-</td>
                        <td class="text-center">{{ $aksi->satuan }}</td>
                        <td class="text-center">{{ $numTargetAksi }}</td>
                        <td class="text-center">{{ $numRealAksi }}</td>
                        <td colspan="3" class="text-center font-bold">{{ number_format($capaianAksiSd, 2) }}%</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    {{-- 4. SECTION PENJELASAN --}}
    <table class="table-penjelasan">
        <tr>
            <td class="font-bold" style="padding-bottom: 5px;">Penjelasan per Indikator Kinerja :</td>
        </tr>
        
        <tr>
            <td class="font-bold">A. Upaya :</td>
        </tr>
        <tr>
            <td style="padding-bottom: 10px;">
                @if(isset($penjelasans) && $penjelasans->count() > 0)
                    @foreach($penjelasans as $index => $item)
                        @if($item->upaya)
                            {{ $index + 1 }}. {{ $item->upaya }}<br>
                        @endif
                    @endforeach
                @else
                    - Tidak ada catatan upaya.
                @endif
            </td>
        </tr>

        <tr>
            <td class="font-bold">B. Hambatan :</td>
        </tr>
        <tr>
            <td style="padding-bottom: 10px;">
                @if(isset($penjelasans) && $penjelasans->count() > 0)
                    @foreach($penjelasans as $index => $item)
                        @if($item->hambatan)
                            {{ $index + 1 }}. {{ $item->hambatan }}<br>
                        @endif
                    @endforeach
                @else
                    - Tidak ada catatan hambatan.
                @endif
            </td>
        </tr>

        <tr>
            <td class="font-bold">C. Rencana Tindak Lanjut :</td>
        </tr>
        <tr>
            <td>
                @if(isset($penjelasans) && $penjelasans->count() > 0)
                    @foreach($penjelasans as $index => $item)
                        @if($item->tindak_lanjut)
                            {{ $index + 1 }}. {{ $item->tindak_lanjut }}<br>
                        @endif
                    @endforeach
                @else
                    - Tidak ada rencana tindak lanjut.
                @endif
            </td>
        </tr>
    </table>

    {{-- 5. SECTION TANDA TANGAN --}}
    <table class="table-signature">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center; padding-bottom: 10px;">
                Banjarmasin, {{ $hariIni }} {{ ucfirst(strtolower($namaBulan)) }} {{ $tahun }}
            </td>
        </tr>
        
        <tr>
            <td style="text-align: center;">Mengetahui Atasan Langsung,</td>
            <td style="text-align: center;">Yang Melaporkan,</td>
        </tr>

        <tr>
            <td style="text-align: center; font-weight: bold; text-transform: uppercase;">
                {{ $atasan->nama ?? '(Jabatan Atasan)' }}
            </td>
            <td style="text-align: center; font-weight: bold; text-transform: uppercase;">
                {{ $jabatan->nama ?? '(Nama Jabatan)' }}
            </td>
        </tr>

        {{-- Spasi untuk tanda tangan atau stempel --}}
        <tr class="row-space">
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td style="text-align: center;">
                <p class="name-underline">{{ $atasan->pegawai->nama ?? '(Belum ada pejabat)' }}</p>
                <p style="margin: 0;">{{ $atasan->pegawai->pangkat ?? '' }} {{ $atasan->pegawai->golongan ?? '' }}</p>
                <p style="margin: 0;">NIP. {{ $atasan->pegawai->nip ?? '-' }}</p>
            </td>
            <td style="text-align: center;">
                <p class="name-underline">{{ $jabatan->pegawai->nama ?? '(Belum ada pejabat)' }}</p>
                <p style="margin: 0;">{{ $jabatan->pegawai->pangkat ?? '' }} {{ $jabatan->pegawai->golongan ?? '' }}</p>
                <p style="margin: 0;">NIP. {{ $jabatan->pegawai->nip ?? '-' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>