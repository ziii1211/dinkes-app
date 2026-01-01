<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        /* Kita minimalkan style CSS karena styling utama dilakukan via Export Class PHP */
        .text-center { text-align: center; vertical-align: middle; }
        .text-top { vertical-align: top; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    {{-- BARIS 1: Kosong --}}
    <table><tr><td></td></tr></table>

    {{-- BARIS 2: JUDUL --}}
    <table>
        <tr>
            <td colspan="10" style="text-align: center; font-weight: bold; font-size: 12pt;">
                LAPORAN PENGUKURAN KINERJA BULAN S/D BULAN {{ $namaBulan }} TAHUN {{ $tahun }}
            </td>
        </tr>
    </table>

    {{-- BARIS 3: Kosong --}}
    <table><tr><td></td></tr></table>

    {{-- BARIS 4 & 5: IDENTITAS --}}
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
    <table border="1"> {{-- Border=1 membantu excel mendeteksi grid dasar --}}
        <thead>
            <tr>
                <th rowspan="2" width="5">No.</th>
                <th rowspan="2" width="30">Kinerja Utama</th>
                <th rowspan="2" width="35">Indikator</th>
                <th rowspan="2" width="15">Capaian Tahun Lalu</th>
                <th rowspan="2" width="15">Satuan Indikator Kinerja Utama</th>
                <th colspan="3">TARGET DAN CAPAIAN</th>
                <th rowspan="2" width="15">Target Akhir Renstra</th>
                <th rowspan="2" width="20">capaian kinerja bulan {{ $namaBulanKecil }}<br/>(realisasi:targetx100%)</th>
            </tr>
            <tr>
                <th width="12">Target</th>
                <th width="12">Realisasi s.d Bulan ini</th>
                <th width="12">% Capaian</th>
            </tr>
        </thead>
        <tbody>
            @if($pk && $pk->sasarans->count() > 0)
                @php $no = 1; @endphp
                @foreach($pk->sasarans as $sasaran)
                    {{-- Loop Indikator --}}
                    @foreach($sasaran->indikators as $index => $ind)
                        <tr>
                            {{-- No & Kinerja Utama (Grouping) --}}
                            @if($index === 0)
                                @php $rowspan = $sasaran->indikators->count(); @endphp
                                <td align="center" rowspan="{{ $rowspan }}" valign="top">{{ $no++ }}.</td>
                                <td rowspan="{{ $rowspan }}" valign="top">{{ $sasaran->sasaran }}</td>
                            @endif

                            {{-- Data Indikator --}}
                            <td valign="top">- {{ $ind->nama_indikator }}</td>
                            <td align="center" valign="top">100</td> {{-- Dummy --}}
                            <td align="center" valign="top">{{ $ind->satuan }}</td>
                            <td align="center" valign="top">{{ $ind->target_tahunan }}</td>
                            <td align="center" valign="top">{{ $ind->realisasi_bulan ?? '0' }}</td>
                            <td align="center" valign="top">
                                @if(isset($ind->capaian_bulan) && is_numeric($ind->capaian_bulan))
                                    {{ round($ind->capaian_bulan, 2) }}
                                @else 0 @endif
                            </td>
                            <td align="center" valign="top">{{ $ind->target_2030 ?? $ind->target_tahunan }}</td>
                            <td align="center" valign="top">
                                @if(isset($ind->capaian_bulan) && is_numeric($ind->capaian_bulan))
                                    {{ round($ind->capaian_bulan, 2) }}
                                @else 0 @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @else
                <tr>
                    <td colspan="10" align="center">Data Kinerja tidak ditemukan</td>
                </tr>
            @endif

            {{-- BAGIAN AKTIFITAS / RENCANA AKSI --}}
            {{-- Header Aktifitas --}}
            <tr>
                <td></td>
                <td></td>
                <td style="font-weight: bold; font-style: italic;">Aktifitas yang berhubungan dengan Indikator</td>
                <td align="center" style="font-weight: bold; font-style: italic;">Capaian Aktifitas Tahun Lalu</td>
                <td align="center" style="font-weight: bold; font-style: italic;">Satuan Indikator Aktifitas</td>
                <td align="center" style="font-weight: bold; font-style: italic;">Target aktifitas</td>
                <td align="center" style="font-weight: bold; font-style: italic;">realisasi aktifitas</td>
                <td align="center" style="font-weight: bold; font-style: italic;">capaian aktifitas</td>
                <td></td>
                <td></td>
            </tr>

            {{-- Loop Rencana Aksi --}}
            @if(isset($rencanaAksis) && count($rencanaAksis) > 0)
                @foreach($rencanaAksis as $aksi)
                    <tr>
                        <td></td>
                        <td></td>
                        <td valign="top">- {{ $aksi->nama_aksi }}</td>
                        <td align="center" valign="top">-</td>
                        <td align="center" valign="top">{{ $aksi->satuan }}</td>
                        <td align="center" valign="top">{{ $aksi->target }}</td>
                        
                        @php
                            $realisasiAksi = $aksi->realisasi_bulan ?? 0;
                            $capaianAksi = 0;
                            if(is_numeric($aksi->target) && $aksi->target > 0 && is_numeric($realisasiAksi)){
                                $capaianAksi = ($realisasiAksi / $aksi->target) * 100;
                            }
                        @endphp

                        <td align="center" valign="top">{{ $realisasiAksi }}</td>
                        <td align="center" valign="top">{{ round($capaianAksi, 2) }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            @else
                 <tr>
                    <td></td>
                    <td></td>
                    <td style="color: gray; font-style: italic;">(Tidak ada aktivitas terdaftar)</td>
                    <td colspan="7"></td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- BAGIAN PENJELASAN (DI LUAR TABEL DATA) --}}
    <table>
        <tr><td colspan="10"></td></tr>
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
            <td colspan="5" align="center">{{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td colspan="5" align="center">Yang Melaporkan</td>
            <td colspan="5" align="center">Yang Melaporkan</td>
        </tr>
        <tr>
            <td colspan="5" align="center">Sekretaris,</td>
            <td colspan="5" align="center">{{ $jabatan->nama }}</td>
        </tr>
        <tr>
            <td colspan="5" align="center">Dinas Kesehatan Provinsi Kalimantan Selatan</td>
            <td colspan="5" align="center">Dinas Kesehatan Provinsi Kalimantan Selatan</td>
        </tr>
        <tr>
            <td colspan="10" height="60"></td>
        </tr>
        <tr>
            <td colspan="5" align="center" style="font-weight: bold; text-decoration: underline;">(Nama Atasan)</td>
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
            <td colspan="5" align="center">
                NIP. {{ $jabatan->pegawai->nip ?? '..........................' }}
            </td>
        </tr>
    </table>
    
    <br>
    
    {{-- KETERANGAN --}}
    <table>
        <tr><td colspan="10" style="font-style: italic;">keterangan:</td></tr>
        <tr><td colspan="10" style="font-style: italic;">"- indikator kinerja dimasukkan semua dalam satu kolom, disesuaikan dengan kinerja utama"</td></tr>
        <tr><td colspan="10" style="font-style: italic;">"- untuk uraian aktifitas per indikator kinerja, sesuai dengan diisi dalam edialog (ren aksi)"</td></tr>
    </table>
</body>
</html>