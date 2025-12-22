<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Matriks Renstra {{ $unit_kerja }}</title>
    <style>
        /* 1. SETUP HALAMAN */
        @page {
            margin: 1cm 1.5cm; /* Margin agak tipis biar muat banyak */
            size: A4 landscape; /* Wajib Landscape */
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt; /* Ukuran font standar */
            color: #000;
        }

        /* 2. KOP SURAT (Opsional: Jika ingin persis laporan, bisa simple header) */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }
        .header-table td {
            border: none;
            vertical-align: middle;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 11pt;
            text-align: center;
            margin-top: 5px;
        }

        /* 3. TABEL DATA (INTI TAMPILAN) */
        .data-table {
            width: 100%;
            border-collapse: collapse; /* Garis menyatu rapi */
            margin-bottom: 10px;
        }
        
        .data-table th, .data-table td {
            border: 1px solid #000; /* Garis hitam solid */
            padding: 5px;
            vertical-align: top; /* Teks selalu mulai dari atas */
            line-height: 1.4; /* Jarak antar baris teks biar enak dibaca */
        }

        /* HEADER TABEL */
        .data-table thead th {
            background-color: #E8E8E8; /* Abu-abu terang persis dokumen resmi */
            color: #000;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase; /* Huruf KAPITAL semua */
            font-size: 9pt; /* Header sedikit lebih kecil biar muat */
            padding: 8px 2px;
        }

        /* ISI TABEL */
        .col-text { font-weight: normal; }
        .col-bold { font-weight: bold; }
        
        /* List Indikator */
        ul {
            margin: 0;
            padding-left: 12px; /* Bullet tidak terlalu menjorok */
        }
        li {
            margin-bottom: 2px;
        }

        /* Kode Program/Kegiatan */
        .kode-text {
            font-size: 8pt;
            margin-top: 4px;
            color: #333;
        }

        /* 4. TANDA TANGAN */
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    {{-- HEADER DOKUMEN --}}
    <div class="header-title">MATRIKS RENCANA STRATEGIS (RENSTRA)</div>
    <div class="header-title">{{ $unit_kerja }}</div>
    <div class="header-subtitle">PERIODE: {{ $periode }}</div>

    <br>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="12%">TUJUAN</th>
                <th width="12%">SASARAN</th>
                <th width="13%">OUTCOME /<br>PROGRAM</th>
                <th width="13%">OUTPUT /<br>KEGIATAN</th>
                <th width="30%">INDIKATOR KINERJA</th> {{-- Kolom Indikator paling lebar --}}
                <th width="20%">PROGRAM / KEGIATAN /<br>SUB KEGIATAN</th>
            </tr>
        </thead>
        <tbody>
            
            {{-- 1. TUJUAN --}}
            @foreach($tujuans as $tujuan)
            <tr>
                <td class="col-bold">{{ $tujuan->tujuan ?? $tujuan->sasaran_rpjmd }}</td>
                <td></td><td></td><td></td>
                <td>
                    @if(isset($tujuan->indikators_from_pohon) && $tujuan->indikators_from_pohon->count() > 0)
                        <ul>
                            @foreach($tujuan->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            {{-- 2. SASARAN (Termasuk Baris Virtual Level 3) --}}
            @foreach($sasarans as $sasaran)
            <tr>
                <td></td>
                <td class="col-bold">
                    {{-- Nama Sasaran tampil jika bukan baris virtual --}}
                    {{ $sasaran->sasaran ?? '' }}
                </td>
                <td></td><td></td>
                <td>
                    @if(isset($sasaran->indikators_from_pohon) && $sasaran->indikators_from_pohon->count() > 0)
                        <ul>
                            @foreach($sasaran->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            {{-- 3. OUTCOME (PROGRAM) --}}
            @foreach($outcomes as $outcome)
            <tr>
                <td></td><td></td>
                <td>
                    {{ $outcome->outcome ?? '' }}
                </td>
                <td></td>
                <td>
                    @if(isset($outcome->indikators_from_pohon) && $outcome->indikators_from_pohon->count() > 0)
                        <ul>
                            @foreach($outcome->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    {{-- Tampilkan Nama Program --}}
                    @if(!empty($outcome->program->nama))
                        <div class="col-bold">{{ $outcome->program->nama }}</div>
                        <div class="kode-text">Kode: {{ $outcome->program->kode ?? '-' }}</div>
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- 4. KEGIATAN (OUTPUT) --}}
            @foreach($kegiatans as $kegiatan)
            <tr>
                <td></td><td></td><td></td>
                <td>
                    {{ $kegiatan->output ?? '' }}
                </td>
                <td>
                    @if(isset($kegiatan->indikators_from_pohon) && $kegiatan->indikators_from_pohon->count() > 0)
                        <ul>
                            @foreach($kegiatan->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    {{-- Tampilkan Nama Kegiatan --}}
                    @if(!empty($kegiatan->nama))
                        <div class="col-text">{{ $kegiatan->nama }}</div>
                        <div class="kode-text">Kode: {{ $kegiatan->kode ?? '-' }}</div>
                    @endif
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
                    @else - @endif
                </td>
                <td>
                    @if(isset($sub->indikators_from_pohon) && $sub->indikators_from_pohon->count() > 0)
                        <ul>
                            @foreach($sub->indikators_from_pohon as $ind)
                                <li>{{ $ind->nama_indikator }}</li>
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    {{-- Tampilkan Nama Sub Kegiatan --}}
                    <div>{{ $sub->nama ?? '' }}</div>
                    <div class="kode-text">Kode: {{ $sub->kode ?? '' }}</div>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <table class="signature-table">
        <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                Banjarmasin, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
                <br>
                Kepala {{ ucwords(strtolower($unit_kerja)) }}
                <br><br><br><br><br>
                <strong>( ........................................... )</strong><br>
                NIP. ...........................................
            </td>
        </tr>
    </table>

</body>
</html>