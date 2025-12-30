<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .title { font-size: 14px; font-weight: bold; text-align: center; }
        .table-header { 
            font-weight: bold; 
            text-align: center; 
            vertical-align: middle;
            border: 1px solid #000000;
            background-color: #d1d5db; /* Abu-abu muda */
        }
        .table-cell { 
            border: 1px solid #000000; 
            vertical-align: top; 
            padding: 5px; 
        }
        .center { text-align: center; vertical-align: middle; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    {{-- BARIS 1: Kosong --}}
    <table><tr><td></td></tr></table>

    {{-- BARIS 2: JUDUL LAPORAN --}}
    <table>
        <tr>
            <td colspan="10" class="title" style="text-align: center; font-weight: bold; font-size: 14pt; height: 30px; vertical-align: middle;">
                LAPORAN PENGUKURAN KINERJA BULAN S/D BULAN {{ strtoupper($namaBulan) }} TAHUN {{ $tahun }}
            </td>
        </tr>
    </table>

    {{-- BARIS 3: Kosong --}}
    <table><tr><td></td></tr></table>

    {{-- BARIS 4 & 5: IDENTITAS SKPD --}}
    <table>
        <tr>
            <td colspan="2" style="font-weight: bold;">NAMA SKPD</td>
            <td colspan="8" style="font-weight: bold;">: Dinas Kesehatan Provinsi Kalimantan Selatan</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">NAMA JABATAN</td>
            <td colspan="8" style="font-weight: bold;">: {{ $jabatan->nama }}</td>
        </tr>
    </table>

    {{-- TABEL UTAMA --}}
    <table border="1">
        <thead>
            {{-- Header Baris 1 --}}
            <tr>
                <th rowspan="2" class="table-header" width="5">No.</th>
                <th rowspan="2" class="table-header" width="40">Kinerja Utama</th>
                <th rowspan="2" class="table-header" width="45">Indikator</th>
                <th rowspan="2" class="table-header" width="15">Capaian Tahun Lalu</th>
                <th rowspan="2" class="table-header" width="15">Satuan Indikator Kinerja Utama</th>
                <th colspan="3" class="table-header">TARGET DAN CAPAIAN</th>
                <th rowspan="2" class="table-header" width="15">Target Akhir Renstra</th>
                <th rowspan="2" class="table-header" width="20">capaian kinerja bulan {{ $namaBulan }}<br/>(realisasi:targetx100%)</th>
            </tr>
            {{-- Header Baris 2 --}}
            <tr>
                <th class="table-header" width="15">Target</th>
                <th class="table-header" width="15">Realisasi s.d Bulan ini</th>
                <th class="table-header" width="15">% Capaian</th>
            </tr>
        </thead>
        <tbody>
            @if($pk && $pk->sasarans->count() > 0)
                @php $no = 1; @endphp
                @foreach($pk->sasarans as $sasaran)
                    @php 
                        // Hitung jumlah indikator untuk rowspan
                        $rowspan = $sasaran->indikators->count(); 
                        if($rowspan == 0) $rowspan = 1;
                    @endphp

                    @foreach($sasaran->indikators as $index => $ind)
                        <tr>
                            {{-- Kolom No & Sasaran (Hanya muncul di baris pertama tiap sasaran) --}}
                            @if($index === 0)
                                <td class="table-cell center" rowspan="{{ $rowspan }}">{{ $no++ }}.</td>
                                <td class="table-cell" rowspan="{{ $rowspan }}">{{ $sasaran->sasaran }}</td>
                            @endif

                            {{-- Kolom Indikator --}}
                            <td class="table-cell">- {{ $ind->nama_indikator }}</td>

                            {{-- Capaian Tahun Lalu (Data Dummy/Placeholder) --}}
                            <td class="table-cell center">100</td> 

                            {{-- Satuan --}}
                            <td class="table-cell center">{{ $ind->satuan }}</td>

                            {{-- Target Tahunan --}}
                            <td class="table-cell center">{{ $ind->target_tahunan }}</td>

                            {{-- Realisasi --}}
                            <td class="table-cell center">{{ $ind->realisasi_bulan ?? '0' }}</td>

                            {{-- % Capaian --}}
                            <td class="table-cell center">
                                @if(isset($ind->capaian_bulan) && is_numeric($ind->capaian_bulan))
                                    {{ round((float)$ind->capaian_bulan, 2) }}
                                @else
                                    0
                                @endif
                            </td>

                            {{-- Target Akhir Renstra (Ambil target tahun terakhir atau dummy) --}}
                            <td class="table-cell center">{{ $ind->target_2030 ?? $ind->target_2029 ?? $ind->target_tahunan }}</td>

                            {{-- Capaian Kinerja (Sama dengan % Capaian) --}}
                            <td class="table-cell center">
                                @if(isset($ind->capaian_bulan) && is_numeric($ind->capaian_bulan))
                                    {{ round((float)$ind->capaian_bulan, 2) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    
                    {{-- Handle jika sasaran tidak punya indikator --}}
                    @if($sasaran->indikators->count() == 0)
                        <tr>
                            <td class="table-cell center">{{ $no++ }}.</td>
                            <td class="table-cell">{{ $sasaran->sasaran }}</td>
                            <td class="table-cell">-</td>
                            <td class="table-cell center">-</td>
                            <td class="table-cell center">-</td>
                            <td class="table-cell center">-</td>
                            <td class="table-cell center">-</td>
                            <td class="table-cell center">-</td>
                            <td class="table-cell center">-</td>
                            <td class="table-cell center">-</td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="10" class="table-cell center">Data Kinerja belum tersedia untuk periode ini.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- BAGIAN PENJELASAN & REKOMENDASI --}}
    <table>
        <tr><td colspan="10"></td></tr> {{-- Spasi --}}
        
        <tr>
            <td colspan="10" style="font-weight: bold;">Penjelasan per Indikator Kinerja</td>
        </tr>
        
        <tr>
            <td colspan="10" style="font-weight: bold;">Upaya :</td>
        </tr>
        <tr><td colspan="10">1. ....................................................................................................</td></tr>
        <tr><td colspan="10">2. ....................................................................................................</td></tr>

        <tr>
            <td colspan="10" style="font-weight: bold;">Hambatan :</td>
        </tr>
        <tr><td colspan="10">1. ....................................................................................................</td></tr>
        <tr><td colspan="10">2. ....................................................................................................</td></tr>

        <tr>
            <td colspan="10" style="font-weight: bold;">Rencana Tindak Lanjut :</td>
        </tr>
        <tr><td colspan="10">1. ....................................................................................................</td></tr>
        <tr><td colspan="10">2. ....................................................................................................</td></tr>
    </table>

    <br>

    {{-- TANDA TANGAN --}}
    <table>
        <tr>
            <td colspan="5"></td>
            <td colspan="5" align="center">Banjarmasin, {{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td colspan="5" align="center">Yang Melaporkan</td>
            <td colspan="5" align="center">Yang Melaporkan</td>
        </tr>
        <tr>
            {{-- KIRI: Contoh Sekretaris / Atasan --}}
            <td colspan="5" align="center">Sekretaris,</td> 
            {{-- KANAN: Jabatan Pemilik Laporan --}}
            <td colspan="5" align="center">{{ $jabatan->nama }}</td>
        </tr>
        <tr>
            <td colspan="5" align="center">Dinas Kesehatan Provinsi Kalimantan Selatan</td>
            <td colspan="5" align="center">Dinas Kesehatan Provinsi Kalimantan Selatan</td>
        </tr>
        <tr>
            <td colspan="10" height="60"></td> {{-- Spasi TTD --}}
        </tr>
        <tr>
            <td colspan="5" align="center" style="font-weight: bold; text-decoration: underline;">
                (Nama Atasan)
            </td>
            <td colspan="5" align="center" style="font-weight: bold; text-decoration: underline;">
                {{ $jabatan->pegawai->nama ?? '..........................' }}
            </td>
        </tr>
        <tr>
            <td colspan="5" align="center">Pembina (IV/a)</td>
            <td colspan="5" align="center">Penata Tk. I (III/d)</td>
        </tr>
        <tr>
            <td colspan="5" align="center">NIP. ..........................</td>
            <td colspan="5" align="center">NIP. {{ $jabatan->pegawai->nip ?? '..........................' }}</td>
        </tr>
    </table>

    <br>
    <table>
        <tr>
            <td colspan="10" style="font-style: italic;">keterangan:</td>
        </tr>
        <tr>
            <td colspan="10" style="font-style: italic;">"- indikator kinerja dimasukkan semua dalam satu kolom, disesuaikan dengan kinerja utama"</td>
        </tr>
    </table>
</body>
</html>