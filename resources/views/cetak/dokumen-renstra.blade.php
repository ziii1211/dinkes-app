<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Matriks RENSTRA</title>
    <style>
        /* Pengaturan Kertas */
        @page { 
            size: A4 landscape; 
            margin: 1cm; 
        }
        
        /* Reset & Font Utama */
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 9pt; 
            color: #000; 
            background: #fff; 
            margin: 0; 
        }

        /* --- Header Laporan --- */
        .header-container {
            margin-bottom: 20px;
            width: 100%;
            /* Garis tebal di bawah header bisa ditambahkan jika perlu, 
               tapi di contoh PDF sepertinya tidak ada garis tebal header dokumen */
            /* border-bottom: 2px solid #000; */ 
            padding-bottom: 10px;
        }
        
        .unit-info {
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000; /* Hitam pekat sesuai PDF */
        }

        .main-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        /* --- Tabel Data --- */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: 1px solid #000; 
            table-layout: fixed; 
        }
        
        /* Header Tabel */
        table.data-table th { 
            border: 1px solid #000; 
            padding: 8px 4px; 
            vertical-align: middle; 
            text-align: center; 
            font-weight: bold; 
            /* Warna Header Gelap */
            background-color: #1e293b; 
            color: #ffffff; 
            font-size: 8.5pt;
            text-transform: uppercase;
        }
        
        /* Isi Tabel */
        table.data-table td { 
            border: 1px solid #000; /* Border hitam tegas */
            padding: 6px 5px; 
            vertical-align: top; 
            text-align: left; 
            font-size: 8.5pt;
            word-wrap: break-word;
        }

        /* Zebra Striping (Opsional, bisa dihapus jika ingin putih polos seperti PDF) */
        table.data-table tr:nth-child(even) td {
            background-color: #f8f9fa; 
        }

        /* --- Helper Styles --- */
        ul { margin: 0; padding-left: 12px; }
        li { margin-bottom: 3px; }
        .font-bold { font-weight: bold; }
        .text-center { text-align: center; }
        
        /* Kolom Program Khusus */
        .program-code { 
            font-weight: bold; 
            display: block; 
            font-size: 8pt;
            margin-bottom: 2px;
        }
        .program-name { 
            display: block; 
            text-transform: uppercase;
            font-size: 8pt;
        }
    </style>
</head>
<body>

    <!-- HEADER DOKUMEN -->
    <div class="header-container">
        <div class="unit-info">
            Unit Kerja: {{ $header['unit_kerja'] }} <br> 
            Periode: {{ $header['periode'] }}
        </div>
        <div class="main-title">
            MATRIKS RENSTRA - {{ $header['unit_kerja'] }} - {{ $header['periode'] }}
        </div>
    </div>

    <!-- TABEL DATA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 13%;">Tujuan</th>
                <th style="width: 14%;">Sasaran</th>
                <th style="width: 14%;">Outcome</th>
                <th style="width: 14%;">Output</th>
                <th style="width: 22%;">Indikator</th>
                <th style="width: 23%;">Program / Kegiatan / Sub Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            
            <!-- 1. TUJUAN -->
            @foreach($tujuans as $tujuan)
            <tr>
                <td class="font-bold">{{ $tujuan->tujuan ?? $tujuan->sasaran_rpjmd }}</td>
                <td></td><td></td><td></td>
                <td>
                    <!-- Indikator Tujuan (Dari Pohon Kinerja) -->
                    @if($tujuan->indikators_pohon && $tujuan->indikators_pohon->count() > 0)
                        <ul style="list-style-type: disc;">
                            @foreach($tujuan->indikators_pohon as $ind) 
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            <!-- 2. SASARAN -->
            @foreach($sasarans as $sasaran)
            <tr>
                <td></td>
                <td class="font-bold">{{ $sasaran->sasaran }}</td>
                <td></td><td></td>
                <td>
                    <!-- Indikator Sasaran (Dari Pohon Kinerja) -->
                    @if($sasaran->indikators_pohon && $sasaran->indikators_pohon->count() > 0)
                        <ul style="list-style-type: disc;">
                            @foreach($sasaran->indikators_pohon as $ind) 
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            <!-- 3. OUTCOME (PROGRAM) -->
            @foreach($outcomes as $outcome)
            <tr>
                <td></td><td></td>
                <td>{{ $outcome->outcome }}</td>
                <td></td>
                <td>
                    @if($outcome->indikators->count() > 0)
                        <ul style="list-style-type: none; padding: 0;">
                            @foreach($outcome->indikators as $ind) 
                                <li>• {{ $ind->keterangan }}</li> 
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td>
                    <span class="program-code">{{ $outcome->program->kode ?? '' }}</span>
                    <span class="program-name">{{ $outcome->program->nama ?? '' }}</span>
                </td>
            </tr>
            @endforeach

            <!-- 4. OUTPUT (KEGIATAN) -->
            @foreach($kegiatans as $kegiatan)
            <tr>
                <td></td><td></td><td></td>
                <td>{{ $kegiatan->output }}</td>
                <td class="text-center">-</td>
                <td>
                    <span class="program-code">{{ $kegiatan->kode }}</span>
                    <span class="program-name">{{ $kegiatan->nama }}</span>
                </td>
            </tr>
            @endforeach

            <!-- 5. SUB KEGIATAN -->
            @foreach($sub_kegiatans as $sub)
            <tr>
                <td></td><td></td><td></td>
                <td>{{ $sub->output ?? '-' }}</td>
                <td>
                    @if($sub->indikators && $sub->indikators->count() > 0)
                        <ul style="list-style-type: none; padding: 0;">
                            @foreach($sub->indikators as $ind) 
                                <li>• {{ $ind->keterangan }}</li> 
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td>
                    <span class="program-code">{{ $sub->kode }}</span>
                    <span class="program-name">{{ $sub->nama }}</span>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

</body>
</html>