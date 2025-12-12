<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cetak Matriks RENSTRA</title>
    <style>
        @page { size: A4 landscape; margin: 1cm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; background: #fff; margin: 0; }
        .header-container { margin-bottom: 20px; width: 100%; padding-bottom: 10px; }
        .unit-info { text-align: left; font-size: 9pt; font-weight: bold; margin-bottom: 5px; }
        .main-title { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-top: 15px; margin-bottom: 15px; letter-spacing: 1px; }
        table.data-table { width: 100%; border-collapse: collapse; border: 1px solid #000; table-layout: fixed; }
        table.data-table th { border: 1px solid #000; padding: 8px 4px; vertical-align: middle; text-align: center; font-weight: bold; background-color: #1e293b; color: #ffffff; font-size: 8.5pt; text-transform: uppercase; }
        table.data-table td { border: 1px solid #000; padding: 6px 5px; vertical-align: top; text-align: left; font-size: 8.5pt; word-wrap: break-word; }
        ul { margin: 0; padding-left: 15px; }
        li { margin-bottom: 3px; }
        .font-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .label-type { font-size: 7pt; font-weight: bold; display: block; margin-bottom: 2px; color: #555; }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="unit-info">
            Unit Kerja: {{ $header['unit_kerja'] }} <br> 
            Periode: {{ $header['periode'] }}
        </div>
        <div class="main-title">
            MATRIKS RENSTRA - {{ $header['unit_kerja'] }} - {{ $header['periode'] }}
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tujuan</th>
                <th style="width: 15%;">Sasaran</th>
                <th style="width: 15%;">Outcome (Program)</th>
                <th style="width: 15%;">Output (Kegiatan)</th>
                <th style="width: 20%;">Indikator Kinerja</th>
                <th style="width: 20%;">Sub Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            
            @foreach($tujuans as $tujuan)
            <tr>
                <td class="font-bold">{{ $tujuan->nama_pohon }}</td>
                <td></td><td></td><td></td>
                <td>
                    @if($tujuan->indikators->count() > 0)
                        <ul>
                            @foreach($tujuan->indikators as $ind) 
                                {{-- REVISI: HANYA TAMPILKAN NAMA --}}
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            @foreach($sasarans as $sasaran)
            <tr>
                <td></td>
                <td class="font-bold">{{ $sasaran->nama_pohon }}</td>
                <td></td><td></td>
                <td>
                    @if($sasaran->indikators->count() > 0)
                        <ul>
                            @foreach($sasaran->indikators as $ind) 
                                {{-- REVISI: HANYA TAMPILKAN NAMA --}}
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            @foreach($outcomes as $outcome)
            <tr>
                <td></td><td></td>
                <td class="font-bold">
                    <span class="label-type">PROGRAM</span>
                    {{ $outcome->nama_pohon }}
                </td>
                <td></td>
                <td>
                    @if($outcome->indikators->count() > 0)
                        <ul>
                            @foreach($outcome->indikators as $ind) 
                                {{-- REVISI: HANYA TAMPILKAN NAMA --}}
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            @foreach($kegiatans as $kegiatan)
            <tr>
                <td></td><td></td><td></td>
                <td class="font-bold">
                    <span class="label-type">KEGIATAN</span>
                    {{ $kegiatan->nama_pohon }}
                </td>
                <td>
                    @if($kegiatan->indikators->count() > 0)
                        <ul>
                            @foreach($kegiatan->indikators as $ind) 
                                {{-- REVISI: HANYA TAMPILKAN NAMA --}}
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td></td>
            </tr>
            @endforeach

            @foreach($sub_kegiatans as $sub)
            <tr>
                <td></td><td></td><td></td><td></td>
                <td>
                    @if($sub->indikators->count() > 0)
                        <ul>
                            @foreach($sub->indikators as $ind) 
                                {{-- REVISI: HANYA TAMPILKAN NAMA --}}
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    <span class="label-type">SUB KEGIATAN</span>
                    {{ $sub->nama_pohon }}
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

</body>
</html>