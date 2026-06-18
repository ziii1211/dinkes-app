<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan_Grafik_{{ $namaBulan }}_{{ $tahun }}</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        /* INSTRUKSI UTAMA: Paksa printer/PDF untuk menggunakan format Landscape */
        @page { 
            size: A4 landscape; 
            margin: 1.5cm; 
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            margin: 0; 
            color: #000; 
            background-color: #fff;
        }
        
        /* KOP SURAT */
        .header { 
            display: flex; 
            align-items: center; 
            border-bottom: 3px solid black; 
            padding-bottom: 10px; 
            margin-bottom: 2px; 
        }
        .header-inner-border {
            border-top: 1px solid black;
            margin-bottom: 20px;
        }
        .header img { 
            width: 90px; 
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
            letter-spacing: 1px;
        }
        .header-text h1 { 
            margin: 0; 
            font-size: 22px; 
            font-weight: bold; 
            letter-spacing: 1px;
        }
        .header-text p { 
            margin: 3px 0 0 0; 
            font-size: 11px; 
        }
        
        /* JUDUL LAPORAN */
        .title { 
            text-align: center; 
            font-size: 14pt; 
            font-weight: bold; 
            text-decoration: underline; 
            margin-top: 10px; 
            margin-bottom: 5px;
        }
        .subtitle { 
            text-align: center; 
            font-size: 12pt; 
            margin-bottom: 20px; 
        }
        
        /* GRAFIK CONTAINER */
        /* Di landscape, kita bisa pakai lebar 100% dengan tenang */
        .chart-container { 
            width: 100%; 
            margin: 0 auto 20px auto; 
            padding: 0;
            border: none;
            box-shadow: none;
        }

        /* INTERPRETASI DATA */
        .explanation-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 5px;
        }
        .explanation-box { 
            text-align: justify; 
            font-size: 12pt; 
            line-height: 1.5; 
            margin-bottom: 30px; 
            text-indent: 40px; 
        }
        
        /* TANDA TANGAN */
        /* Supaya tanda tangan tidak terpisah ke halaman baru jika mepet */
        .signature { 
            width: 100%; 
            margin-top: 30px; 
            page-break-inside: avoid;
        }
        .signature table { 
            width: 100%; 
            border: none; 
        }
        .signature td { 
            border: none; 
            font-size: 12pt;
        }

        /* RESET UNTUK CETAK */
        @media print {
            body { margin: 0; }
            .chart-container { margin-bottom: 15px; }
            /* Memastikan background warna tercetak jika browser di-setting tidak print background */
            * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
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
        <div style="width: 90px;"></div>
    </div>
    <div class="header-inner-border"></div>

    <div class="title">LAPORAN ANALISIS GRAFIK PENCAPAIAN KINERJA</div>
    <div class="subtitle">PERIODE BULAN {{ strtoupper($namaBulan) }} TAHUN {{ $tahun }}</div>

    <div class="chart-container">
        <div id="pdfChart"></div>
    </div>

    <div class="explanation-title">Interpretasi Data:</div>
    <div class="explanation-box">
        {{ $penjelasan }}
    </div>

    <div class="signature">
        <table>
            <tr>
                <td style="width: 60%;"></td> 
                <td style="width: 40%; text-align: center;">
                    Banjarmasin, {{ $tanggal_cetak }} <br>
                    Kepala Dinas Kesehatan<br>
                    Provinsi Kalimantan Selatan
                    <br><br><br><br><br>
                    <strong><u>Dr. Diauddin, M.Kes</u></strong><br>
                    NIP. 19770923 200604 1 015
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Tangkap data dari controller
        var categories = {!! $categories !!};
        var seriesData = {!! $seriesData !!};
        
        var numItems = categories.length;
        
        // Hitung tinggi grafik
        // Di format Landscape, kertas lebih pendek tingginya. 
        // Maksimal ideal height kita turunkan jadi 400px agar tidak lompat halaman.
        var idealHeight = Math.max(300, numItems * 35);
        if (idealHeight > 420) {
            idealHeight = 420; 
        }

        var labelFontSize = numItems > 20 ? '9px' : '11px';
        var titleFontSize = '12px';
        var barThickness = numItems > 20 ? '80%' : '45%';
        var dataLabelOffset = 20;

        // Konfigurasi ApexCharts Profesional
        var options = {
            series: [{
                name: 'Capaian Kinerja (%)',
                data: seriesData
            }],
            chart: {
                type: 'bar',
                height: idealHeight,
                animations: { enabled: false }, 
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 0,
                    horizontal: true,
                    distributed: false, 
                    barHeight: barThickness,
                    dataLabels: { position: 'top' } // Angka diletakkan di ujung kanan bar
                }
            },
            colors: ['#1f497d'], // Biru dongker formal
            dataLabels: {
                enabled: true,
                offsetX: dataLabelOffset, 
                style: { 
                    fontSize: labelFontSize, 
                    colors: ['#000'],
                    fontFamily: 'Times New Roman, serif',
                },
                formatter: function (val) { return val + "%"; }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#000']
            },
            xaxis: {
                categories: categories,
                // Skala dimaksimalkan ke 100 agar ujung grafik tidak terpotong
                max: 100, 
                // Tick amount diset agar angka X axis rapi (0, 20, 40, 60, 80, 100)
                tickAmount: 5, 
                title: { 
                    text: 'Persentase Capaian (%)',
                    style: { fontSize: titleFontSize, fontWeight: 'bold', fontFamily: 'Times New Roman, serif' },
                    offsetY: 5 // Geser sedikit ke bawah agar tidak tabrakan dengan angka
                },
                labels: { 
                    style: { fontSize: labelFontSize, fontFamily: 'Times New Roman, serif' },
                    formatter: function(val) { return Math.round(val) } // Bulatkan angka X axis
                }
            },
            yaxis: {
                labels: {
                    // Karena kertas kita sekarang landscape, ruang nama jabatan bisa lebih panjang
                    maxWidth: 450, 
                    style: {
                        fontSize: labelFontSize,
                        fontFamily: 'Times New Roman, serif',
                        fontWeight: 'normal'
                    }
                }
            },
            grid: {
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } },
                borderColor: '#666',
                strokeDashArray: 3 
            },
            legend: { show: false }
        };

        // Render Grafik
        var chart = new ApexCharts(document.querySelector("#pdfChart"), options);
        chart.render().then(() => {
            // Jeda 800ms agar grafik selesai animasi drawing sebelum trigger window print
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>