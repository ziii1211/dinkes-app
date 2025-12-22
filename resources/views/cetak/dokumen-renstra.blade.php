<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Matriks Renstra {{ $header['unit_kerja'] ?? 'DINAS KESEHATAN' }}</title>
    <style>
        /* 1. SETUP HALAMAN */
        @page {
            margin: 1cm 1cm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt; 
            color: #000;
            line-height: 1.2;
        }

        /* 2. HEADER DOKUMEN */
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* 3. TABEL DATA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .data-table th, .data-table td {
            border: 1px solid #000; 
            padding: 5px;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* HEADER TABEL (Tetap Bold karena ini Judul Kolom) */
        .data-table thead th {
            background-color: #1a2c42; /* Navy Blue */
            color: #ffffff; 
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
            text-transform: capitalize;
            padding: 8px 5px;
        }

        /* Setting Print */
        .data-table thead { display: table-header-group; }
        .data-table tfoot { display: table-footer-group; }
        .data-table tr { page-break-inside: avoid; }

        /* LIST INDIKATOR */
        ul { margin: 0; padding-left: 15px; list-style-type: -; }
        li { margin-bottom: 3px; }

        /* STYLE KHUSUS KOLOM PROGRAM (UPDATED: TIDAK BOLD) */
        .kode-text {
            /* font-weight dihapus agar tidak bold */
            margin-bottom: 3px;
            display: block;
        }
        .nama-program-text {
            /* font-weight dihapus agar tidak bold */
            text-transform: uppercase; /* Biasanya nama program tetap kapital, tapi tidak bold */
        }

    </style>
</head>
<body>

    {{-- JUDUL DOKUMEN --}}
    <div class="header-title">
        Matriks RENSTRA - {{ $header['unit_kerja'] ?? 'DINAS KESEHATAN' }} - {{ $header['periode'] ?? '2025 - 2029' }}
    </div>

    {{-- TABEL --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="12%">Tujuan</th>
                <th width="12%">Sasaran</th>
                <th width="14%">Outcome</th>
                <th width="14%">Output</th>
                <th width="24%">Indikator</th>
                <th width="24%">Program / Kegiatan / Sub Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            
            {{-- 1. TUJUAN --}}
            @foreach($tujuans as $tujuan)
            <tr>
                <td>{{ $tujuan->tujuan ?? $tujuan->sasaran_rpjmd }}</td>
                <td></td><td></td><td></td>
                {{-- Indikator Tujuan --}}
                <td>
                    @if(isset($tujuan->indikators_from_pohon) && count($tujuan->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($tujuan->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            {{-- 2. SASARAN --}}
            @foreach($sasarans as $sasaran)
            <tr>
                <td></td>
                <td>{{ $sasaran->sasaran ?? '' }}</td>
                <td></td><td></td>
                {{-- Indikator Sasaran --}}
                <td>
                    @if(isset($sasaran->indikators_from_pohon) && count($sasaran->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($sasaran->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            {{-- 3. OUTCOME (PROGRAM) --}}
            @foreach($outcomes as $outcome)
            <tr>
                <td></td><td></td>
                <td>{{ $outcome->outcome ?? '' }}</td>
                <td></td>
                {{-- Indikator Outcome --}}
                <td>
                    @if(isset($outcome->indikators_from_pohon) && count($outcome->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($outcome->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                {{-- Kolom Program (SUDAH TIDAK BOLD) --}}
                <td>
                    @if(isset($outcome->program))
                        <span class="kode-text">{{ $outcome->program->kode ?? '' }}</span>
                        <span class="nama-program-text">{{ $outcome->program->nama ?? '' }}</span>
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- 4. KEGIATAN (OUTPUT) --}}
            @foreach($kegiatans as $kegiatan)
            <tr>
                <td></td><td></td><td></td>
                <td>{{ $kegiatan->output ?? '' }}</td>
                {{-- Indikator Kegiatan --}}
                <td>
                    @if(isset($kegiatan->indikators_from_pohon) && count($kegiatan->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($kegiatan->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                {{-- Kolom Kegiatan (SUDAH TIDAK BOLD) --}}
                <td>
                    <span class="kode-text">{{ $kegiatan->kode ?? '' }}</span>
                    <span>{{ $kegiatan->nama ?? '' }}</span>
                </td>
            </tr>
            @endforeach

            {{-- 5. SUB KEGIATAN --}}
            @foreach($sub_kegiatans as $sub)
            <tr>
                <td></td><td></td><td></td>
                <td>
                    @if(!empty($sub->output))
                        Output: {{ $sub->output }}
                    @endif
                </td>
                {{-- Indikator Sub --}}
                <td>
                    @if(isset($sub->indikators_from_pohon) && count($sub->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($sub->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                {{-- Kolom Sub Kegiatan (SUDAH TIDAK BOLD) --}}
                <td>
                    <span class="kode-text">{{ $sub->kode ?? '' }}</span>
                    <span>{{ $sub->nama ?? '' }}</span>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

</body>
</html>