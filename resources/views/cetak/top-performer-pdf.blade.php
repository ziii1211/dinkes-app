<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Top Performer</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; line-height: 1.5; padding: 20px;}
        .table-kop { width: 100%; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 30px; border-collapse: collapse; }
        .table-kop td { border: none; vertical-align: middle; }
        .logo-img { width: 80px; height: auto; }
        .header-title { font-size: 14pt; font-weight: bold; text-align: center; text-transform: uppercase; line-height: 1.3; }
        
        .content-title { text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 5px; text-decoration: underline;}
        .subtitle { text-align: center; font-size: 12pt; margin-bottom: 40px; font-weight: bold;}
        
        .box-performer { border: 2px dashed #000; padding: 25px; margin-bottom: 30px; text-align: center; background-color: #fdfdfd;}
        .kegiatan-name { font-size: 15pt; font-weight: bold; color: #1a1a1a; text-transform: uppercase; margin: 20px 0;}
        .label-top { font-size: 14pt; font-weight: bold; letter-spacing: 2px;}
        
        .stats-table { width: 85%; margin: 0 auto; border-collapse: collapse; }
        .stats-table td { padding: 10px; border: 1px solid #ccc; font-size: 11pt;}
        .stats-table td:first-child { font-weight: bold; width: 60%; background-color: #f5f5f5;}
        .stats-table td:last-child { text-align: right; font-weight: bold; color: #000; width: 40%; font-size: 12pt;}
        
        .reason-box { margin-top: 20px; text-align: justify; padding: 20px; border-left: 5px solid #000; background-color: #f9f9f9;}
        .reason-title { font-weight: bold; margin-bottom: 15px; font-size: 12pt; text-transform: uppercase;}
    </style>
</head>
<body>
    {{-- KOP SURAT BERLOGO --}}
    <table class="table-kop">
        <tr>
            <td style="width: 15%; text-align: center;">
                <img src="{{ public_path('logo pemprov.png') }}" class="logo-img" alt="Logo Pemprov">
            </td>
            <td style="width: 85%;">
                <div class="header-title">
                    PEMERINTAH PROVINSI KALIMANTAN SELATAN<br>
                    DINAS KESEHATAN<br>
                </div>
                <div style="text-align: center; font-size: 10pt; font-weight: normal; margin-top: 5px;">
                    Jalan Belitung Darat No. 118 Banjarmasin 70116<br>
                    Telepon (0511) 3354387 - 3355661, Email: dinkes@kalselprov.go.id
                </div>
            </td>
        </tr>
    </table>

    <div class="content-title">LAPORAN TOP PERFORMER KINERJA</div>
    <div class="subtitle">TAHUN ANGGARAN {{ $tahun }}</div>

    <div style="text-align: justify; margin-bottom: 25px; text-indent: 40px;">
        Berdasarkan hasil rekapitulasi evaluasi dan pengukuran kinerja melalui sistem E-Monev Dinas Kesehatan Provinsi Kalimantan Selatan, 
        berikut ini merupakan hasil penetapan capaian predikat kinerja tertinggi (Top Performer) untuk unit kerja 
        <b>{{ $selectedJabatan ? $selectedJabatan->nama : 'DI LINGKUNGAN DINAS KESEHATAN (KESELURUHAN)' }}</b> pada Tahun Anggaran {{ $tahun }}.
    </div>

    <div class="box-performer">
        <div class="label-top">★ PREDIKAT TOP PERFORMER ★</div>
        <div class="kegiatan-name">"{{ $topPerformer['nama_kegiatan'] }}"</div>
        
        <table class="stats-table">
            <tr>
                <td>Realisasi Pencapaian Fisik</td>
                <td>{{ $topPerformer['persen_fisik'] }} %</td>
            </tr>
            <tr>
                <td>Efisiensi Serapan Anggaran (Keuangan)</td>
                <td>{{ $topPerformer['persen_keu'] }} %</td>
            </tr>
            <tr>
                <td style="border-top: 2px solid #000; background-color: #fff;">Skor Kinerja Komposit (Rata-Rata)</td>
                <td style="border-top: 2px solid #000; font-size: 14pt;">{{ $topPerformer['score'] }}</td>
            </tr>
        </table>
    </div>

    <div class="reason-box">
        <div class="reason-title">Alasan dan Justifikasi Penilaian:</div>
        <div style="line-height: 1.6; font-size: 11pt;">
            {!! $topPerformer['alasan'] !!}
        </div>
    </div>

    <br><br>
    
    {{-- TANDA TANGAN DINAMIS --}}
    @php
        $ttdJabatan = '';
        $ttdNama = '.....................................';
        $ttdNip = '.....................................';

        if ($selectedJabatan) {
            $ttdJabatan = $selectedJabatan->nama;
            $pegawai = \App\Models\Pegawai::where('jabatan_id', $selectedJabatan->id)->first();
            if ($pegawai) {
                $ttdNama = $pegawai->nama;
                $ttdNip = $pegawai->nip;
            }
        } else {
            $ttdJabatan = 'KEPALA DINAS KESEHATAN';
            $kadis = \App\Models\Jabatan::whereNull('parent_id')->first();
            if($kadis) {
                $pegawai = \App\Models\Pegawai::where('jabatan_id', $kadis->id)->first();
                if($pegawai) {
                    $ttdNama = $pegawai->nama;
                    $ttdNip = $pegawai->nip;
                }
            }
        }
    @endphp

    <table width="100%" style="page-break-inside: avoid; border: none; margin-top: 40px;">
        <tr>
            <td width="50%" style="border: none;"></td>
            <td width="50%" class="text-center" style="font-size: 11pt; border: none; text-align: center;">
                Banjarmasin, ........................ {{ $tahun }}<br>
                <span style="font-weight: bold; text-transform: uppercase;">{{ $ttdJabatan }}</span><br>
                PROVINSI KALIMANTAN SELATAN
                <br><br><br><br><br>
                <u style="font-weight: bold;">{{ $ttdNama }}</u><br>
                NIP. {{ $ttdNip }}
            </td>
        </tr>
    </table>
</body>
</html>