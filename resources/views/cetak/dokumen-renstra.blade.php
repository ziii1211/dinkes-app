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
        .program-code { font-weight: bold; display: block; font-size: 8pt; margin-bottom: 2px; }
        .program-name { display: block; text-transform: uppercase; font-size: 8pt; }
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
                <th style="width: 15%;">Outcome</th>
                <th style="width: 15%;">Output</th>
                <th style="width: 20%;">Indikator</th>
                <th style="width: 20%;">Program / Kegiatan / Sub Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            
            @foreach($tujuans as $tujuan)
            <tr>
                <td class="font-bold">{{ $tujuan->tujuan ?? $tujuan->sasaran_rpjmd }}</td>
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

            @foreach($sasarans as $sasaran)
            <tr>
                <td></td>
                <td class="font-bold">{{ $sasaran->sasaran }}</td>
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

            @foreach($outcomes as $outcome)
            <tr>
                <td></td><td></td>
                <td class="font-bold">{{ $outcome->outcome }}</td>
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
                    <span class="program-code">{{ $outcome->program->kode ?? '' }}</span>
                    <span class="program-name">{{ $outcome->program->nama ?? '' }}</span>
                </td>
            </tr>
            @endforeach

            @foreach($kegiatans as $kegiatan)
            <tr>
                <td></td><td></td><td></td>
                <td class="font-bold">{{ $kegiatan->output }}</td>
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
                    <span class="program-code">{{ $kegiatan->kode }}</span>
                    <span class="program-name">{{ $kegiatan->nama }}</span>
                </td>
            </tr>
            @endforeach

            @foreach($sub_kegiatans as $sub)
            <tr>
                <td></td><td></td><td></td>
                <td>{{ $sub->output ?? '-' }}</td>
                <td>
                    {{-- PRIORITAS 1: DATA INPUT MANUAL --}}
                    @if($sub->indikators->isNotEmpty())
                        <ul style="font-weight: bold; color: #000;">
                            @foreach($sub->indikators as $ind) 
                                <li>{{ $ind->keterangan }}</li> 
                            @endforeach
                        </ul>
                    
                    {{-- PRIORITAS 2: DATA DARI POHON KINERJA --}}
                    @elseif(isset($sub->indikators_from_pohon) && $sub->indikators_from_pohon->count() > 0)
                        <ul>
                            @foreach($sub->indikators_from_pohon as $ind) 
                                <li>{{ $ind->nama_indikator }}</li> 
                            @endforeach
                        </ul>
                    
                    @else
                        -
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