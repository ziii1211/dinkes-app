<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Top Performer</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 0;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', Times, serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            background-image: radial-gradient(#f1f5f9 1px, transparent 1px);
            background-size: 20px 20px;
        }
        /* Bingkai Sertifikat Mewah */
        .certificate-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 6px double #b45309; /* Warna Gold Tua */
            padding: 40px;
            box-sizing: border-box;
        }
        .inner-border {
            border: 1px solid #cbd5e1;
            height: 100%;
            padding: 30px;
            box-sizing: border-box;
            position: relative;
        }
        /* Ornamen Sudut Klasik */
        .corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #b45309;
        }
        .top-left { top: -5px; left: -5px; border-right: none; border-bottom: none; }
        .top-right { top: -5px; right: -5px; border-left: none; border-bottom: none; }
        .bottom-left { bottom: -5px; left: -5px; border-right: none; border-top: none; }
        .bottom-right { bottom: -5px; right: -5px; border-left: none; border-top: none; }

        /* Header Instansi */
        .kop-instansi {
            text-align: center;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin-bottom: 40px;
        }
        .kop-instansi h4 {
            margin: 0;
            font-size: 14px;
            letter-spacing: 2px;
            color: #475569;
            text-transform: uppercase;
        }
        .kop-instansi h2 {
            margin: 5px 0 0 0;
            font-size: 24px;
            color: #1e3a8a; /* Navy Blue */
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .line-separator {
            margin: 15px auto;
            width: 60%;
            height: 2px;
            background: linear-gradient(to right, transparent, #b45309, transparent);
        }

        /* Judul Dokumen Utama */
        .main-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .main-title h1 {
            font-size: 32px;
            margin: 0;
            color: #b45309;
            font-weight: normal;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .main-title p {
            margin: 8px 0 0 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #64748b;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* Deskripsi Penerima */
        .award-to {
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #475569;
            margin-bottom: 10px;
            font-style: italic;
        }
        .recipient-name {
            text-align: center;
            margin-bottom: 25px;
        }
        .recipient-name h2 {
            font-size: 28px;
            color: #1e3a8a;
            margin: 0;
            display: inline-block;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 5px;
        }

        /* Nilai / Skor Box */
        .score-badge {
            text-align: center;
            margin: 20px auto;
        }
        .score-badge span {
            font-family: Arial, sans-serif;
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 8px 25px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 20px;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Rincian Alasan Penetapan (Revisian Utama Dospem) */
        .reason-container {
            margin: 30px auto;
            width: 85%;
            font-family: 'Georgia', serif;
            font-size: 15px;
            line-height: 1.7;
            text-align: center;
            color: #334155;
            background-color: #fff7ed; /* Soft Orange Tint */
            border: 1px solid #ffedd5;
            padding: 20px 25px;
            border-radius: 8px;
        }
        .reason-container strong {
            color: #b45309;
            display: block;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Footer & Tanda Tangan */
        .footer-table {
            width: 100%;
            margin-top: 60px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #334155;
        }
        .signature-title {
            margin-bottom: 65px;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #334155;
            margin-top: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="certificate-border">
    <div class="inner-border">
        <div class="corner top-left"></div>
        <div class="corner top-right"></div>
        <div class="corner bottom-left"></div>
        <div class="corner bottom-right"></div>

        <div class="kop-instansi">
            <h4>Pemerintah Provinsi Kalimantan Selatan</h4>
            <h2>Dinas Kesehatan</h2>
            <div class="line-separator"></div>
        </div>

        <div class="main-title">
            <h1>Piagam Penghargaan</h1>
            <p>Top Performer Kinerja</p>
        </div>

        <div class="award-to">Diberikan Kepada Jabatan:</div>
        
        <div class="recipient-name">
            <h2>{{ $selectedJabatan ? $selectedJabatan->nama : 'Sekretaris' }}</h2>
        </div>

        <div class="score-badge">
            <span>PERIODE EVALUASI TAHUN {{ $tahun ?? request('tahun', date('Y')) }}</span>
        </div>

        <div class="reason-container">
            <strong>Alasan & Pertimbangan Hukum Penetapan:</strong>
            @if(isset($alasan) && $alasan != '')
                "{!! $alasan !!}"
            @elseif(request('alasan'))
                "{!! urldecode(request('alasan')) !!}"
            @else
                "Ditetapkan sebagai Top Performer berdasarkan rekapitulasi penilaian objektif sistem SAKIP, mencatatkan rata-rata realisasi capaian program kerja tertinggi di atas rata-rata unit kerja lainnya pada periode berjalan."
            @endif
        </div>

        <table class="footer-table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 55%; text-align: center; vertical-align: bottom; font-style: italic; color: #64748b; font-size: 11px; padding-bottom: 5px;">
                    
                    <div style="margin-bottom: 8px;">
                        <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(100)->generate('Sertifikat Sah: ' . $namaKadis . ' - Tahun ' . $tahun)) }}" alt="QR Code">
                    </div>

                    *Dokumen ini diterbitkan sah secara digital<br>
                    serta terverifikasi oleh Sistem Aplikasi SAKIP Terpadu.
                </td>
                
                <td style="text-align: center; width: 45%;">
                    <div class="signature-title">
                        Banjarmasin, {{ $tanggalCetak }}<br>
                        <strong>Kepala Dinas Kesehatan</strong>
                    </div>
                    <div>
                        <span class="signature-line"></span><br>
                        <span style="font-weight: bold; text-transform: uppercase; display: block; margin-top: 5px;">{{ $namaKadis }}</span>
                        <span style="font-size: 12px; color: #64748b;">{{ $pangkatKadis }} / NIP. {{ $nipKadis }}</span>
                    </div>
                </td>
            </tr>
        </table>

    </div>
</div>

</body>
</html>