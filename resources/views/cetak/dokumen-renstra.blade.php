<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Matriks Renstra {{ $header['unit_kerja'] ?? ($unit_kerja ?? 'DINAS KESEHATAN') }}</title>
    <style>
        /* 1. SETUP HALAMAN */
        @page {
            margin: 1cm 1cm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000;
            line-height: 1.3;
        }

        /* 2. HEADER */
        .header-wrapper {
            margin-bottom: 10px;
            border-bottom: 3px solid #000;
            padding-bottom: 5px;
        }
        .header-text {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
        }

        /* 3. TABEL DATA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .data-table thead { display: table-header-group; }
        .data-table tfoot { display: table-footer-group; }
        .data-table tr { page-break-inside: avoid; }

        .data-table thead th {
            background-color: #F2F2F2;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 8.5pt;
            text-transform: uppercase;
            height: 35px;
        }

        /* CONTENT STYLES */
        .col-bold { font-weight: bold; }
        ul { margin: 0; padding-left: 12px; }
        li { margin-bottom: 2px; }
        .kode-text { font-weight: bold; margin-bottom: 2px; display: block; }
        .nama-program { text-transform: uppercase; font-weight: bold; }
        .text-italic { font-style: italic; color: #444; }
    </style>
</head>
<body>

    {{-- HEADER DOKUMEN --}}
    <div class="header-wrapper">
        <div class="header-text">
            Unit Kerja: {{ $header['unit_kerja'] ?? ($unit_kerja ?? 'DINAS KESEHATAN') }} &nbsp;|&nbsp; 
            Periode: {{ $header['periode'] ?? ($periode ?? '2025 - 2029') }}
        </div>
    </div>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="13%">TUJUAN</th>
                <th width="13%">SASARAN</th>
                <th width="15%">OUTCOME</th>
                <th width="15%">OUTPUT</th>
                <th width="20%">INDIKATOR</th>
                <th width="24%">PROGRAM / KEGIATAN /<br>SUB KEGIATAN</th>
            </tr>
        </thead>
        <tbody>
            
            {{-- 1. TUJUAN --}}
            @foreach($tujuans as $tujuan)
            <tr>
                <td class="col-bold">{{ $tujuan->tujuan ?? $tujuan->sasaran_rpjmd }}</td>
                <td></td><td></td><td></td>
                <td>
                    {{-- Cek Indikator (Support Model Eloquent & Livewire stdClass) --}}
                    @if(isset($tujuan->indikators_from_pohon) && count($tujuan->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($tujuan->indikators_from_pohon as $ind)
                                <li>{{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}</li>
                            @endforeach
                        </ul>
                    @elseif($tujuan instanceof \Illuminate\Database\Eloquent\Model && $tujuan->pohonKinerja && $tujuan->pohonKinerja->indikators->count() > 0)
                         {{-- Fallback ke Relasi jika properti manual tidak ada --}}
                        <ul>
                            @foreach($tujuan->pohonKinerja->indikators as $ind)
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
                <td class="col-bold">{{ $sasaran->sasaran ?? '' }}</td>
                <td></td><td></td>
                <td>
                    @if(isset($sasaran->indikators_from_pohon) && count($sasaran->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($sasaran->indikators_from_pohon as $ind)
                                <li>{{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}</li>
                            @endforeach
                        </ul>
                    @elseif($sasaran instanceof \Illuminate\Database\Eloquent\Model && $sasaran->pohonKinerja && $sasaran->pohonKinerja->indikators->count() > 0)
                        <ul>
                            @foreach($sasaran->pohonKinerja->indikators as $ind)
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
                <td>
                    @if(isset($outcome->indikators_from_pohon) && count($outcome->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($outcome->indikators_from_pohon as $ind)
                                <li>{{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}</li>
                            @endforeach
                        </ul>
                    @elseif($outcome instanceof \Illuminate\Database\Eloquent\Model && $outcome->pohonKinerja && $outcome->pohonKinerja->indikators->count() > 0)
                        <ul>
                            @foreach($outcome->pohonKinerja->indikators as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td>
                    {{-- Cek apakah properti program ada (Livewire pakai stdClass kadang beda struktur) --}}
                    @if(isset($outcome->program) && !empty($outcome->program->nama))
                        <div class="kode-text">{{ $outcome->program->kode ?? '' }}</div>
                        <div class="nama-program">{{ $outcome->program->nama }}</div>
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- 4. KEGIATAN (OUTPUT) --}}
            @foreach($kegiatans as $kegiatan)
            <tr>
                <td></td><td></td><td></td>
                <td>{{ $kegiatan->output ?? '' }}</td>
                <td>
                    @if(isset($kegiatan->indikators_from_pohon) && count($kegiatan->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($kegiatan->indikators_from_pohon as $ind)
                                <li>{{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}</li>
                            @endforeach
                        </ul>
                    @elseif($kegiatan instanceof \Illuminate\Database\Eloquent\Model && $kegiatan->pohonKinerja && $kegiatan->pohonKinerja->indikators->count() > 0)
                        <ul>
                            @foreach($kegiatan->pohonKinerja->indikators as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td>
                    @if(!empty($kegiatan->nama))
                        <div class="kode-text">{{ $kegiatan->kode ?? '' }}</div>
                        <div>{{ $kegiatan->nama }}</div>
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- 5. SUB KEGIATAN --}}
            @foreach($sub_kegiatans as $sub)
            <tr>
                <td></td><td></td><td></td>
                <td>
                     @if(!empty($sub->output)) Output: {{ $sub->output }} @endif
                </td>
                <td>
                    @if(isset($sub->indikators_from_pohon) && count($sub->indikators_from_pohon) > 0)
                        <ul>
                            @foreach($sub->indikators_from_pohon as $ind)
                                <li>{{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}</li>
                            @endforeach
                        </ul>
                    @elseif($sub instanceof \Illuminate\Database\Eloquent\Model && $sub->pohonKinerja && $sub->pohonKinerja->indikators->count() > 0)
                        <ul>
                            @foreach($sub->pohonKinerja->indikators as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td>
                    <div class="kode-text">{{ $sub->kode ?? '' }}</div>
                    <div>{{ $sub->nama ?? '' }}</div>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <table style="width: 100%; margin-top: 40px; page-break-inside: avoid;">
        <tr>
            <td width="60%"></td>
            <td width="40%" align="center">
                Banjarmasin, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                Kepala Dinas Kesehatan<br>
                Provinsi Kalimantan Selatan
                <br><br><br><br><br>
                <strong>( ........................................... )</strong><br>
                NIP. ...........................................
            </td>
        </tr>
    </table>

</body>
</html>