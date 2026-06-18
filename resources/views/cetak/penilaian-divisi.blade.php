<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Evaluasi_{{ str_replace([' ', '/', '\\'], '_', $jabatan->nama) }}_{{ $tahun }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 20px;
            color: #000;
        }
        /* Kop Surat - margin & padding diperkecil agar lebih rapat ke judul */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid black;
            padding-bottom: 5px; 
            margin-bottom: 15px; 
        }
        .header img {
            width: 80px; 
            height: auto;
        }
        .header-text {
            flex: 1;
            text-align: center;
        }
        .header-text h3 {
            margin: 0;
            font-size: 16px;
            font-weight: normal;
        }
        .header-text h1 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
        }
        .header-text p {
            margin: 2px 0 0 0;
            font-size: 12px;
        }
        
        /* PERBAIKAN: Judul Laporan dibuat lebih rapi dan rata tengah */
        .title-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 auto 5px auto;
            line-height: 1.4;
        }
        .subtitle {
            font-size: 12px;
            font-weight: normal;
        }

        /* Tabel Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid black;
            padding: 6px;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        /* Tanda Tangan - Diratakan ke kanan */
        .signature {
            width: 100%;
            margin-top: 30px;
        }
        .signature table {
            width: 100%;
            border: none;
        }
        .signature td {
            border: none;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ asset('logo pemprov.png') }}" alt="Logo Kalsel">
        <div class="header-text">
            <h3>PEMERINTAH PROVINSI KALIMANTAN SELATAN</h3>
            <h1>DINAS KESEHATAN</h1>
            <p>Jalan Belitung Darat No. 118, Banjarmasin 70116</p>
            <p>Telepon: (0511) 3354311, Email: dinkes@kalselprov.go.id</p>
            <p>Website: dinkes.kalselprov.go.id</p>
        </div>
        <div style="width: 80px;"></div> </div>

    <div class="title-container">
        <div class="title">
            LAPORAN EVALUASI DAN PENILAIAN KINERJA<br>DIVISI {{ strtoupper($jabatan->nama) }}
        </div>
        <div class="subtitle">
            TAHUN EVALUASI: {{ $tahun }}
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Kinerja Utama (Sasaran)</th>
                <th style="width: 25%;">Indikator Kinerja</th>
                <th style="width: 8%;">Target</th>
                <th style="width: 8%;">Realisasi</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 19%;">Penjelasan / Kendala</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kinerja as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->kinerja_utama }}</td>
                    <td>{{ $row->nama_indikator }}</td>
                    <td class="text-center">{{ $row->target }} {{ $row->satuan }}</td>
                    <td class="text-center">
                        {{ $row->realisasi ? $row->realisasi . ' ' . $row->satuan : '-' }}
                    </td>
                    <td class="text-center">
                        @if($row->status == 'Tercapai')
                            <span style="color: green; font-weight: bold;">Tercapai</span>
                        @else
                            <span style="color: red; font-weight: bold;">Belum Tercapai</span>
                        @endif
                    </td>
                    <td>{{ $row->penjelasan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="font-style: italic;">Belum ada data perjanjian kinerja untuk divisi ini di tahun {{ $tahun }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <table>
            <tr>
                <td style="width: 60%;"></td> 
                <td style="width: 40%; text-align: center; font-size: 12px;">
                    Banjarmasin, {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->translatedFormat('d F Y') }} <br>
                    Kepala Dinas Kesehatan<br>
                    Provinsi Kalimantan Selatan
                    <br><br><br><br><br>
                    <strong><u>Dr. Diauddin, M.Kes</u></strong><br>
                    NIP. 197709232006041015
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.onload = function() {
            // Otomatis buka dialog print (Save as PDF)
            window.print();
            
            // Notifikasi muncul setelah jeda singkat
            setTimeout(function() {
                alert("File PDF sudah siap! Pastikan menyimpannya dengan nama yang sesuai.");
            }, 500);
        }
    </script>
</body>
</html>