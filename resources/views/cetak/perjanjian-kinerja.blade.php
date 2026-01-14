<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Perjanjian Kinerja</title>
    <style>
        /* 1. SETUP HALAMAN PDF */
        @page { 
            size: A4 portrait; 
            margin: 2.5cm 2.5cm; 
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11pt; 
            color: #000; 
            background: #fff; 
            margin: 0; 
            padding: 0;
            line-height: 1.3;
        }

        /* UTILITIES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* HEADER SURAT */
        .header-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            text-transform: uppercase;
            line-height: 1.5;
        }

        /* TABEL UTAMA (SASARAN) */
        .table-main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-main th, 
        .table-main td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .table-main th {
            background-color: #E8E8E8; 
            text-align: center;
            font-weight: bold;
        }

        /* TABEL ANGGARAN (BORDERLESS) */
        .table-budget {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 40px;
        }
        .table-budget td {
            padding: 4px 5px;
            vertical-align: top;
            border: none; 
        }
        .border-top-black {
            border-top: 1px solid #000 !important;
        }

        /* TANDA TANGAN */
        .table-signature {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Jarak dari tabel anggaran */
            page-break-inside: avoid; 
        }
        .table-signature td {
            border: none;
            text-align: center;
            vertical-align: top; /* Pastikan konten nempel atas */
            padding: 0;
        }
        /* Row khusus untuk spasi tanda tangan */
        .row-space td {
            height: 80px;
            vertical-align: middle;
        }
        .name-underline {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    {{-- JUDUL --}}
    <div class="header-title">
        PERJANJIAN KINERJA TAHUN {{ $pk->tahun }}<br>
        {{ $jabatan->nama }}<br>
        DINAS KESEHATAN<br>
        PROVINSI KALIMANTAN SELATAN
    </div>

    {{-- TABEL 1: SASARAN --}}
    <table class="table-main">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">KINERJA UTAMA</th>
                <th style="width: 45%;">INDIKATOR KINERJA</th>
                <th style="width: 15%;">TARGET</th>
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

                            <td style="padding-left: 10px;">{{ $ind->nama_indikator }}</td>
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
                        <td>-</td>
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
                <th style="text-align: left; width: 70%; border:none; padding-bottom: 10px;">Program/Kegiatan/Sub Kegiatan</th>
                <th style="text-align: right; width: 30%; border:none; padding-bottom: 10px;">Anggaran</th>
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
                <td class="text-center" style="padding-top: 10px;">JUMLAH</td>
                <td class="text-right border-top-black" style="padding-top: 10px;">
                    Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- TABEL 3: TANDA TANGAN (PERBAIKAN STRUKTUR) --}}
    <table class="table-signature">
        {{-- BARIS 1: JUDUL PIHAK & JABATAN (Akan expand sesuai teks terpanjang) --}}
        <tr>
            {{-- KIRI: PIHAK PERTAMA --}}
            <td style="width: 50%; padding-bottom: 0;">
                <p class="font-bold" style="margin-bottom: 5px;">PIHAK PERTAMA,</p>
                <p class="font-bold uppercase" style="margin-top: 0;">
                    {{ $jabatan->nama }}
                </p>
            </td>

            {{-- KANAN: PIHAK KEDUA --}}
            <td style="width: 50%; padding-bottom: 0;">
                <p class="font-bold" style="margin-bottom: 5px;">PIHAK KEDUA,</p>
                @if($is_kepala_dinas)
                    <p class="font-bold uppercase" style="margin-top: 0;">
                        GUBERNUR KALIMANTAN SELATAN
                    </p>
                @elseif(isset($atasan_jabatan) && $atasan_jabatan)
                    <p class="font-bold uppercase" style="margin-top: 0;">
                        {{ $atasan_jabatan->nama }}
                    </p>
                @else
                    <p class="font-bold uppercase" style="margin-top: 0;">
                        (JABATAN ATASAN)
                    </p>
                @endif
            </td>
        </tr>

        {{-- BARIS 2: SPASI TANDA TANGAN (Tinggi Fix, sejajar kiri kanan) --}}
        <tr class="row-space">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        {{-- BARIS 3: NAMA & NIP (Akan mulai di posisi vertikal yang sama) --}}
        <tr>
            {{-- KIRI: NAMA PIHAK PERTAMA --}}
            <td>
                @if($pegawai)
                    <p class="name-underline" style="margin: 0;">{{ $pegawai->nama }}</p>
                    <p style="margin: 0;">NIP. {{ $pegawai->nip }}</p>
                @else
                    <p class="name-underline" style="margin: 0;">(Belum Ada Pejabat)</p>
                    <p style="margin: 0;">NIP. -</p>
                @endif
            </td>

            {{-- KANAN: NAMA PIHAK KEDUA --}}
            <td>
                @if($is_kepala_dinas)
                    <p class="name-underline" style="margin: 0;">H. MUHIDIN</p>
                    {{-- <p style="margin: 0;">NIP. ...</p> --}}
                @elseif($atasan_pegawai)
                    <p class="name-underline" style="margin: 0;">{{ $atasan_pegawai->nama }}</p>
                    <p style="margin: 0;">NIP. {{ $atasan_pegawai->nip }}</p>
                @else
                    <p class="name-underline" style="margin: 0;">(Atasan Belum Diset)</p>
                    <p style="margin: 0;">NIP. -</p>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>