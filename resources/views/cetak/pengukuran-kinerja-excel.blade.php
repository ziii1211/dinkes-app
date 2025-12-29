<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .title { font-size: 14px; font-weight: bold; text-align: center; }
        .subtitle { font-size: 12px; font-weight: bold; text-align: center; }
        .label { font-weight: bold; }
        .table-header { 
            background-color: #4B5563; 
            color: #ffffff; 
            font-weight: bold; 
            text-align: center; 
            vertical-align: middle;
            border: 1px solid #000000;
        }
        .table-cell { border: 1px solid #000000; vertical-align: top; padding: 5px; }
        .center { text-align: center; }
        .bg-gray { background-color: #f3f4f6; font-weight: bold; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    {{-- HEADER KOP --}}
    <table>
        <tr><td colspan="7" class="title">LAPORAN PENGUKURAN KINERJA</td></tr>
        <tr><td colspan="7" class="title">DINAS KESEHATAN</td></tr>
        <tr><td colspan="7"></td></tr> {{-- Spasi Kosong --}}
        
        <tr>
            <td colspan="2" class="label">Unit Kerja</td>
            <td colspan="5">: DINAS KESEHATAN</td>
        </tr>
        <tr>
            <td colspan="2" class="label">Nama Jabatan</td>
            <td colspan="5">: {{ $jabatan->nama }}</td>
        </tr>
        <tr>
            <td colspan="2" class="label">Periode</td>
            <td colspan="5">: {{ $namaBulan }} {{ $tahun }}</td>
        </tr>
        <tr><td colspan="7"></td></tr>
    </table>

    {{-- TABEL 1: PENGUKURAN KINERJA (IKU) --}}
    <table border="1">
        <thead>
            <tr>
                <th width="50" class="table-header">NO</th>
                <th width="300" class="table-header">SASARAN STRATEGIS / INDIKATOR</th>
                <th width="100" class="table-header">TARGET</th>
                <th width="100" class="table-header">REALISASI</th>
                <th width="100" class="table-header">CAPAIAN (%)</th>
                <th width="200" class="table-header">KETERANGAN/CATATAN</th>
                <th width="200" class="table-header">TANGGAPAN PIMPINAN</th>
            </tr>
        </thead>
        <tbody>
            @if($pk)
                @php $no = 1; @endphp
                @foreach($pk->sasarans as $sasaran)
                    {{-- Baris Sasaran (Grouping) --}}
                    <tr>
                        <td class="table-cell center bg-gray text-bold">{{ $no++ }}</td>
                        <td class="table-cell bg-gray text-bold" colspan="6">{{ $sasaran->sasaran }}</td>
                    </tr>
                    {{-- Baris Indikator --}}
                    @foreach($sasaran->indikators as $ind)
                        <tr>
                            <td class="table-cell"></td> {{-- Kosongkan No untuk indikator --}}
                            <td class="table-cell" style="padding-left: 20px;">- {{ $ind->nama_indikator }}</td>
                            <td class="table-cell center">{{ $ind->target_tahunan }} {{ $ind->satuan }}</td>
                            <td class="table-cell center">{{ $ind->realisasi_bulan ?? '-' }}</td>
                            <td class="table-cell center">
                                @if(isset($ind->capaian_bulan) && $ind->capaian_bulan !== '-')
                                    {{ $ind->capaian_bulan }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="table-cell">{{ $ind->catatan_bulan ?? '-' }}</td>
                            <td class="table-cell">{{ $ind->tanggapan_bulan ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="table-cell center">Data PK tidak ditemukan</td>
                </tr>
            @endif
        </tbody>
    </table>

    <br>

    {{-- TABEL 2: RENCANA AKSI --}}
    <table border="1">
        <thead>
            <tr>
                <th colspan="6" style="background-color: #e5e7eb; font-weight: bold; text-align: left; border: 1px solid #000;">RENCANA AKSI</th>
            </tr>
            <tr>
                <th width="300" class="table-header">KEGIATAN / AKSI</th>
                <th width="100" class="table-header">TARGET</th>
                <th width="100" class="table-header">SATUAN</th>
                <th width="100" class="table-header">REALISASI</th>
                <th width="100" class="table-header">CAPAIAN (%)</th>
                <th width="200" class="table-header">KET</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rencanaAksis as $aksi)
                <tr>
                    <td class="table-cell">{{ $aksi->nama_aksi }}</td>
                    <td class="table-cell center">{{ $aksi->target }}</td>
                    <td class="table-cell center">{{ $aksi->satuan }}</td>
                    <td class="table-cell center">{{ $aksi->realisasi_bulan ?? '-' }}</td>
                    <td class="table-cell center">
                        @if($aksi->capaian_bulan !== null)
                            {{ $aksi->capaian_bulan }}%
                        @else
                            -
                        @endif
                    </td>
                    <td class="table-cell"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="table-cell center">Belum ada Rencana Aksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br><br>

    {{-- TANDA TANGAN --}}
    <table>
        <tr>
            <td colspan="5"></td>
            <td colspan="2" align="center">Banjarmasin, {{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="2" align="center">Pejabat Penilai Kinerja</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="2" align="center" height="60"></td> {{-- Spasi Tanda Tangan --}}
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="2" align="center" style="font-weight: bold; text-decoration: underline;">
                {{ $jabatan->pegawai->nama ?? '..........................' }}
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="2" align="center">
                NIP. {{ $jabatan->pegawai->nip ?? '..........................' }}
            </td>
        </tr>
    </table>
</body>
</html>