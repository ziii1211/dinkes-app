<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Rencana Aksi</title>
    <style>
        @page { size: A4 landscape; margin: 1.5cm 2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; line-height: 1.3; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* HEADER SURAT (KOP) */
        .table-kop { width: 100%; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .table-kop td { border: none; vertical-align: middle; }
        .logo-img { width: 80px; height: auto; }
        .header-title { font-size: 13pt; font-weight: bold; text-align: center; text-transform: uppercase; line-height: 1.3; }

        /* TABEL UTAMA */
        .table-main { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-main th, .table-main td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        /* PERBAIKAN: Menghapus background-color agar menjadi putih polos */
        .table-main th { background-color: #fff; text-align: center; font-weight: bold; font-size: 10pt; }
        .table-main td { font-size: 10pt; text-align: justify; }
        .table-main td.text-center { text-align: center; }

        /* TABEL ANGGARAN */
        .table-budget { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .table-budget td { padding: 4px 6px; vertical-align: top; border: none; font-size: 10pt; }
        .border-top-black { border-top: 1px solid #000 !important; }

        /* TANDA TANGAN (DIPERBAIKI) */
        .table-signature { width: 100%; border-collapse: collapse; page-break-inside: avoid; margin-top: 10px; }
        .table-signature td { border: none; vertical-align: top; padding: 0; }
        .signature-date { margin-bottom: 5px; }
        .row-space td { height: 90px; } /* Jarak untuk cap/tanda tangan */
        .name-underline { font-weight: bold; text-decoration: underline; margin: 0 0 3px 0; }
    </style>
</head>
<body>

    <table class="table-kop">
        <tr>
            <td style="width: 15%; text-align: center;">
                <img src="{{ public_path('logo pemprov.png') }}" class="logo-img" alt="Logo Pemprov">
            </td>
            <td style="width: 70%;">
                <div class="header-title">
                    RENCANA AKSI KINERJA TAHUN {{ $pk->tahun }}<br>
                    {{ $jabatan->nama }}<br>
                    DINAS KESEHATAN PROVINSI KALIMANTAN SELATAN
                </div>
            </td>
            <td style="width: 15%;"></td>
        </tr>
    </table>

    {{-- TABEL 1: RENCANA AKSI (SASARAN & TRIWULAN) --}}
    <table class="table-main">
        <thead>
            <tr>
                <th width="4%" rowspan="2">No</th>
                <th width="24%" rowspan="2">Kinerja Utama</th>
                <th width="28%" rowspan="2">Indikator Kinerja</th>
                <th width="12%" rowspan="2">Target Tahunan</th>
                <th width="32%" colspan="4">Target Rencana Aksi</th>
            </tr>
            <tr>
                <th width="8%">TW I</th>
                <th width="8%">TW II</th>
                <th width="8%">TW III</th>
                <th width="8%">TW IV</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($pk->sasarans as $sasaran)
                @php 
                    $countIndikator = $sasaran->indikators->count();
                    $rowspan = $countIndikator > 0 ? $countIndikator : 1; 
                @endphp
                
                @if($countIndikator > 0)
                    @foreach($sasaran->indikators as $index => $ind)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ $rowspan }}" class="text-center">{{ $no++ }}.</td>
                                <td rowspan="{{ $rowspan }}">{{ $sasaran->sasaran }}</td>
                            @endif

                            <td>{{ $ind->nama_indikator }}</td>
                            <td class="text-center">
                                @php 
                                    $colTarget = 'target_' . $pk->tahun;
                                    $val = $ind->$colTarget ?? $ind->target; 
                                    $numVal = (float)$val;
                                    $satuan = $ind->satuan;
                                    
                                    // Logika Pembagian Triwulan (1/4 dari Target)
                                    $twVal = $numVal > 0 ? number_format($numVal / 4, 2) : '-';
                                    $twVal = str_replace('.00', '', $twVal); // Hilangkan desimal kalau bulat
                                @endphp
                                <strong>{{ (float)$val == (int)$val ? (int)$val : $val }} {{ $satuan }}</strong>
                            </td>
                            {{-- Output Target TW 1 - 4 Otomatis terbagi --}}
                            <td class="text-center">{{ $twVal }} {{ $satuan }}</td>
                            <td class="text-center">{{ $twVal }} {{ $satuan }}</td>
                            <td class="text-center">{{ $twVal }} {{ $satuan }}</td>
                            <td class="text-center">{{ $twVal }} {{ $satuan }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">{{ $no++ }}.</td>
                        <td>{{ $sasaran->sasaran }}</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- TABEL 2: ANGGARAN RENCANA AKSI --}}
    <table class="table-budget">
        <thead>
            <tr>
                <th style="text-align: left; width: 75%; border:none; padding-bottom: 5px;">Alokasi Anggaran Rencana Aksi (Per Program/Kegiatan/Sub Kegiatan)</th>
                <th style="text-align: right; width: 25%; border:none; padding-bottom: 5px;">Pagu Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAnggaran = 0; @endphp
            @foreach($pk->anggarans as $idx => $anggaran)
                @php $totalAnggaran += $anggaran->anggaran; @endphp
                <tr>
                    <td>{{ $idx + 1 }}. {{ $anggaran->subKegiatan ? $anggaran->subKegiatan->nama : preg_replace('/^[\d\.]+\s*/', '', $anggaran->nama_program_kegiatan) }}</td>
                    <td class="text-right">Rp {{ number_format($anggaran->anggaran, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="font-bold">
                <td class="text-center" style="padding-top: 10px;">JUMLAH TOTAL</td>
                <td class="text-right border-top-black" style="padding-top: 10px;">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- TABEL 3: TANDA TANGAN (Menggunakan pembagian kolom agar tidak terpotong) --}}
    <table class="table-signature">
        <tr>
            <td style="width: 60%;"></td> {{-- Kolom Kiri Kosong sebagai pendorong --}}
            <td style="width: 40%; text-align: center;">
                <div class="signature-date">Banjarmasin, {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->translatedFormat('d F Y') }}</div>
                <p class="font-bold uppercase" style="margin: 0;">{{ $jabatan->nama }}</p>
            </td>
        </tr>
        <tr class="row-space">
            <td></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                @if($pegawai)
                    <p class="name-underline">{{ $pegawai->nama }}</p>
                    <p style="margin: 0;">NIP. {{ $pegawai->nip }}</p>
                @else
                    <p class="name-underline">(Belum Ada Pejabat)</p>
                    <p style="margin: 0;">NIP. -</p>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>