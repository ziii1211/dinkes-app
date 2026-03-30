<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan')</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; margin: 0; padding: 0; }
        .kop-surat { width: 100%; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 80px; height: auto; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .header-text { margin: 0; line-height: 1.2; }
        .judul-laporan { text-align: center; font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; }
        /* Style untuk tabel data nanti */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 5px; text-align: left; }
        .table-data th { background-color: #f2f2f2; text-align: center; }
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
           <td width="15%" class="text-center">
                <img src="{{ public_path('logo pemprov.png') }}" class="logo" alt="Logo Kalsel">
            </td>
            <td width="85%" class="text-center">
                <h3 class="header-text">PEMERINTAH PROVINSI KALIMANTAN SELATAN</h3>
                <h1 class="header-text text-bold" style="font-size: 22px;">DINAS KESEHATAN</h1>
                <p class="header-text" style="font-size: 11px; margin-top: 5px;">
                    Jalan Belitung Darat No. 118, Banjarmasin 70116<br>
                    Telepon: (0511) 3354311, Email: dinkes@kalselprov.go.id<br>
                    Website: dinkes.kalselprov.go.id
                </p>
            </td>
        </tr>
    </table>

    <div class="judul-laporan">
        @yield('judul_laporan')
    </div>

    <div class="konten-laporan">
        @yield('konten')
    </div>

    <table style="width: 100%; margin-top: 40px;">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                Banjarmasin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Kepala Dinas Kesehatan<br>
                Provinsi Kalimantan Selatan
                <br><br><br><br>
                <span class="text-bold" style="text-decoration: underline;">Dr. Diauddin, M.Kes</span><br>
                NIP. 1970XXXXXXXX X X XXX
            </td>
        </tr>
    </table>

</body>
</html>