<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data Pegawai</title>
    <style>
        @page { size: A4 portrait; margin: 1.5cm 2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5pt; color: #000; line-height: 1.3; }
        
        /* KOP SURAT */
        .table-kop { width: 100%; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .table-kop td { border: none; vertical-align: middle; }
        .logo-img { width: 80px; height: auto; }
        .header-title { font-size: 13pt; font-weight: bold; text-align: center; text-transform: uppercase; line-height: 1.3; }
        
        /* ALAMAT KOP SURAT */
        .header-address { font-size: 10pt; font-weight: normal; text-transform: none; display: block; margin-top: 4px; }

        .sub-judul { text-align: center; font-weight: bold; font-size: 11.5pt; margin-bottom: 5px; text-transform: uppercase; }
        .filter-teks { text-align: center; font-size: 10.5pt; margin-bottom: 20px; font-style: italic; }

        /* TABEL UTAMA (Tanpa Warna) */
        .table-main { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-main th, .table-main td { border: 1px solid #000; padding: 7px 5px; vertical-align: middle; }
        .table-main th { text-align: center; font-weight: bold; } /* Background dihapus */
        
        /* TANDA TANGAN */
        .table-signature { width: 100%; border-collapse: collapse; page-break-inside: avoid; }
        .table-signature td { border: none; text-align: center; padding: 0; vertical-align: top; }
        .row-space td { height: 80px; }
        .name-underline { font-weight: bold; text-decoration: underline; margin: 0 0 3px 0; }

        /* UTILITIES */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
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
                    PEMERINTAH PROVINSI KALIMANTAN SELATAN<br>
                    DINAS KESEHATAN
                    <span class="header-address">Belitung Darat No.118, Belitung Utara, Banjarmasin Bar., Kota Banjarmasin, Kalimantan Selatan 70116, Indonesia,Nomor telepon
:(0511) 354443</span>
                </div>
            </td>
            <td style="width: 15%;"></td>
        </tr>
    </table>

    <div class="sub-judul">DAFTAR REKAPITULASI PEGAWAI</div>
    
    @if($filterJabatan)
        <div class="filter-teks">Tingkat Jabatan: {{ $filterJabatan->nama }}</div>
    @else
        {{-- Spasi kosong sebagai pengganti "Keseluruhan Data SKPD" yang dihapus --}}
        <div style="margin-bottom: 20px;"></div>
    @endif

    <table class="table-main">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Pegawai</th>
                <th style="width: 20%;">NIP</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 35%;">Jabatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawais as $i => $pegawai)
                <tr>
                    {{-- Semua data dibuat Rata Tengah (text-center) agar rapi --}}
                    <td class="text-center">{{ $i + 1 }}.</td>
                    <td class="text-center">{{ $pegawai->nama }}</td>
                    <td class="text-center">{{ $pegawai->nip }}</td>
                    <td class="text-center">{{ $pegawai->status ?? '-' }}</td>
                    <td class="text-center">{{ $pegawai->jabatan->nama ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="table-signature">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <div style="margin-bottom: 5px;">Banjarmasin, {{ $hariIni }}</div>
                <div style="font-weight: bold;">KEPALA DINAS</div>
            </td>
        </tr>
        <tr class="row-space">
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                @if($kepalaDinas && $kepalaDinas->pegawai)
                    <p class="name-underline">{{ $kepalaDinas->pegawai->nama }}</p>
                    <p style="margin: 0;">NIP. {{ $kepalaDinas->pegawai->nip }}</p>
                @else
                    <p class="name-underline">(Belum ada pejabat)</p>
                    <p style="margin: 0;">NIP. -</p>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>