<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Keputusan_Kadis_{{ $tahun }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 20px; color: #000; }
        .header { display: flex; align-items: center; border-bottom: 3px solid black; padding-bottom: 5px; margin-bottom: 10px; }
        .header img { width: 80px; height: auto; }
        .header-text { flex: 1; text-align: center; }
        .header-text h3 { margin: 0; font-size: 16px; font-weight: normal; }
        .header-text h1 { margin: 0; font-size: 22px; font-weight: bold; }
        .header-text p { margin: 2px 0 0 0; font-size: 12px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .subtitle { text-align: center; font-size: 12px; margin-bottom: 20px; }
        
        .data-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid black; padding: 6px; vertical-align: top; }
        .data-table th { background-color: #f2f2f2; text-align: center; }
        
        .divisi-header { background-color: #e0e0e0; font-weight: bold; text-transform: uppercase; }
        .text-center { text-align: center; }
        
        .signature { width: 100%; margin-top: 30px; }
        .signature table { width: 100%; border: none; }
        .signature td { border: none; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="header">
        <img src="{{ asset('logo pemprov.png') }}" alt="Logo Kalsel">
        <div class="header-text">
            <h3>PEMERINTAH PROVINSI KALIMANTAN SELATAN</h3>
            <h1>DINAS KESEHATAN</h1>
            <p>Jalan Belitung Darat No. 118, Banjarmasin 70116</p>
            <p>Telepon: (0511) 3354311, Email: dinkes@kalselprov.go.id</p>
            <p>Website: dinkes.kalselprov.go.id</p>
        </div>
        <div style="width: 80px;"></div>
    </div>

    <!-- JUDUL -->
    <div class="title">LAPORAN RINGKASAN EVALUASI & KEPUTUSAN KEPALA DINAS</div>
    <div class="subtitle">TERHADAP INDIKATOR KINERJA DI BAWAH TARGET (BULAN {{ strtoupper($namaBulan) }} TAHUN {{ $tahun }})</div>

    <!-- TABEL DATA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Indikator Kinerja</th>
                <th style="width: 10%;">Target</th>
                <th style="width: 10%;">Realisasi</th>
                <th style="width: 20%;">Kendala Divisi</th>
                <th style="width: 30%;">Rekomendasi Keputusan Pimpinan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanGrouped as $nama_divisi => $items)
                <!-- Header Nama Divisi -->
                <tr>
                    <td colspan="6" class="divisi-header">DIVISI / JABATAN: {{ $nama_divisi }}</td>
                </tr>
                <!-- Looping Item Indikator per Divisi -->
                @foreach($items as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $row->nama_indikator }}<br><em style="font-size: 9px;">({{ $row->kinerja_utama }})</em></td>
                        <td class="text-center">{{ $row->target }} {{ $row->satuan }}</td>
                        <td class="text-center text-red-600 font-bold">
                            {{ $row->realisasi ? $row->realisasi . ' ' . $row->satuan : '0 (Kosong)' }}
                        </td>
                        <td>{{ $row->kendala }}</td>
                        <td style="font-weight: bold; color: #8b0000;">{{ $row->keputusan }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="font-style: italic;">Semua indikator pada seluruh divisi telah mencapai target 100% pada tahun {{ $tahun }}. Tidak ada evaluasi kritis yang diperlukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="signature">
        <table>
            <tr>
                <td style="width: 60%;"></td> 
                <td style="width: 40%; text-align: center; font-size: 12px;">
                    Banjarmasin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} <br>
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
            window.print();
            setTimeout(function() {
                alert("File PDF Laporan Keputusan Kepala Dinas telah siap.");
            }, 500);
        }
    </script>
</body>
</html>