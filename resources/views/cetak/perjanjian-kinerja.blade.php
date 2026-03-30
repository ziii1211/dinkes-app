<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Perjanjian Kinerja</title>
    <style>
        /* 1. SETUP HALAMAN PDF */
        @page { 
            size: A4 portrait; 
            margin: 2cm 2.5cm; /* Margin atas-bawah 2cm, kiri-kanan 2.5cm */
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11pt; 
            color: #000; 
            background: #fff; 
            margin: 0; 
            padding: 0;
            line-height: 1.4; /* Line height agar teks tidak terlalu mepet */
        }

        /* UTILITIES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* HEADER SURAT (KOP) */
        .table-kop {
            width: 100%;
            border-bottom: 4px double #000; /* Garis kop surat lebih tebal/resmi */
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .table-kop td {
            border: none;
            vertical-align: middle;
        }
        .logo-img {
            width: 85px; 
            height: auto;
        }
        .header-title {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.3;
        }

        /* TABEL UTAMA (SASARAN) */
        .table-main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-main th, 
        .table-main td {
            border: 1px solid #000;
            padding: 8px 10px; /* Padding ditambah agar lega */
            vertical-align: middle;
        }
        .table-main th {
            background-color: #E8E8E8; 
            text-align: center;
            font-weight: bold;
            font-size: 10.5pt;
        }
        .table-main td {
            font-size: 10.5pt;
            text-align: justify; /* Teks rata kiri-kanan */
        }
        .table-main td.text-center {
            text-align: center;
        }

        /* TABEL ANGGARAN (BORDERLESS) */
        .table-budget {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .table-budget td {
            padding: 6px 8px;
            vertical-align: bottom;
            border: none; 
            font-size: 11pt;
        }
        .border-top-black {
            border-top: 1px solid #000 !important;
        }

        /* TANDA TANGAN */
        .table-signature {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px; 
            page-break-inside: avoid; /* Mencegah ttd terpotong ke halaman 2 */
        }
        .table-signature td {
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }
        .signature-title {
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .signature-jabatan {
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            height: 40px; /* Menyamakan tinggi baris jabatan */
        }
        /* Row khusus untuk spasi tanda tangan */
        .row-space td {
            height: 90px; /* Area tanda tangan diperluas */
        }
        .name-underline {
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 3px 0;
        }
        .nip-text {
            margin: 0;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT BERLOGO --}}
    <table class="table-kop">
        <tr>
            <td style="width: 15%; text-align: center;">
                <img src="{{ public_path('logo pemprov.png') }}" class="logo-img" alt="Logo Pemprov">
            </td>
            <td style="width: 70%;">
                <div class="header-title">
                    PERJANJIAN KINERJA TAHUN {{ $pk->tahun }}<br>
                    {{ $jabatan->nama }}<br>
                    DINAS KESEHATAN<br>
                    PROVINSI KALIMANTAN SELATAN
                </div>
            </td>
            <td style="width: 15%;">
                {{-- Ruang kosong penyeimbang --}}
            </td>
        </tr>
    </table>

    {{-- TABEL 1: SASARAN --}}
    <table class="table-main">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">KINERJA UTAMA</th>
                <th style="width: 45%;">INDIKATOR KINERJA</th>
                <th style="width: 20%;">TARGET</th>
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
                                    $val = (float)$val == (int)$val ? (int)$val : $val;
                                @endphp
                                {{ $val }} {{ $ind->satuan }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">{{ $no++ }}.</td>
                        <td>{{ $sasaran->sasaran }}</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- TABEL 2: ANGGARAN --}}
    <table class="table-budget">
        <thead>
            <tr>
                <th style="text-align: left; width: 75%; border:none; padding-bottom: 10px;">Program/Kegiatan/Sub Kegiatan</th>
                <th style="text-align: right; width: 25%; border:none; padding-bottom: 10px;">Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAnggaran = 0; @endphp
            @foreach($pk->anggarans as $idx => $anggaran)
                @php $totalAnggaran += $anggaran->anggaran; @endphp
                <tr>
                    <td>
                        {{ $idx + 1 }}. 
                        @if($anggaran->subKegiatan)
                            {{ $anggaran->subKegiatan->nama }}
                        @else
                            {{ preg_replace('/^[\d\.]+\s*/', '', $anggaran->nama_program_kegiatan) }}
                        @endif
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($anggaran->anggaran, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            
            <tr style="font-weight: bold;">
                <td class="text-center" style="padding-top: 15px;">JUMLAH</td>
                <td class="text-right border-top-black" style="padding-top: 15px;">
                    Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- TABEL 3: TANDA TANGAN --}}
    <table class="table-signature">
        {{-- BARIS 1: JUDUL PIHAK & JABATAN --}}
        <tr>
            {{-- KIRI: PIHAK KEDUA (BAWAHAN) --}}
            <td style="width: 50%;">
                <p class="signature-title">PIHAK KEDUA,</p>
                <div class="signature-jabatan">
                    {{ $jabatan->nama }}
                </div>
            </td>

            {{-- KANAN: PIHAK PERTAMA (ATASAN) --}}
            <td style="width: 50%;">
                <p class="signature-title">PIHAK PERTAMA,</p>
                <div class="signature-jabatan">
                    @if($is_kepala_dinas)
                        GUBERNUR KALIMANTAN SELATAN
                    @elseif(isset($atasan_jabatan) && $atasan_jabatan)
                        {{ $atasan_jabatan->nama }}
                    @else
                        (JABATAN ATASAN)
                    @endif
                </div>
            </td>
        </tr>

        {{-- BARIS 2: SPASI TANDA TANGAN --}}
        <tr class="row-space">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        {{-- BARIS 3: NAMA & NIP --}}
        <tr>
            {{-- KIRI: NAMA PIHAK KEDUA --}}
            <td>
                @if($pegawai)
                    <p class="name-underline">{{ $pegawai->nama }}</p>
                    <p class="nip-text">NIP. {{ $pegawai->nip }}</p>
                @else
                    <p class="name-underline">(Belum Ada Pejabat)</p>
                    <p class="nip-text">NIP. -</p>
                @endif
            </td>

            {{-- KANAN: NAMA PIHAK PERTAMA --}}
            <td>
                @if($is_kepala_dinas)
                    <p class="name-underline">H. MUHIDIN</p>
                @elseif($atasan_pegawai)
                    <p class="name-underline">{{ $atasan_pegawai->nama }}</p>
                    <p class="nip-text">NIP. {{ $atasan_pegawai->nip }}</p>
                @else
                    <p class="name-underline">(Atasan Belum Diset)</p>
                    <p class="nip-text">NIP. -</p>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>