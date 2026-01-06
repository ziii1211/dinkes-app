<table>
    <thead>
        <tr>
            <td colspan="10" style="text-align: center; font-weight: bold; font-size: 14px; height: 30px; vertical-align: middle;">
                LAPORAN PENGUKURAN KINERJA BULAN S/D BULAN {{ $namaBulan }} TAHUN {{ $tahun }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">NAMA SKPD</td>
            <td colspan="8" style="font-weight: bold;">: Dinas Kesehatan Provinsi Kalimantan Selatan</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">NAMA JABATAN</td>
            <td colspan="8" style="font-weight: bold;">: {{ $jabatan->nama ?? '-' }}</td>
        </tr>

        <tr>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">No.</th>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">Kinerja Utama</th>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">Indikator</th>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">Capaian Tahun Lalu</th>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">Satuan</th>
            <th colspan="3" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">TARGET DAN CAPAIAN</th>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold;">Target Akhir Renstra</th>
            <th rowspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold; word-wrap: break-word;">
                capaian kinerja bulan {{ ucfirst(strtolower($namaBulan)) }} <br> (realisasi:targetx100%)
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Target</th>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">Realisasi s.d Bulan Ini</th>
            <th style="border: 1px solid #000; text-align: center; font-weight: bold;">% Capaian</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($pk) && $pk->sasarans->count() > 0)
            @php $no = 1; @endphp
            @foreach($pk->sasarans as $sasaran)
                @php 
                    $jumlahIndikator = $sasaran->indikators->count();
                    $rowspan = $jumlahIndikator > 0 ? $jumlahIndikator : 1;
                @endphp

                @if($jumlahIndikator > 0)
                    @foreach($sasaran->indikators as $index => $indikator)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ $rowspan }}" style="border: 1px solid #000; text-align: center; vertical-align: top;">{{ $no++ }}</td>
                                <td rowspan="{{ $rowspan }}" style="border: 1px solid #000; vertical-align: top; text-align: left;">
                                    {{ $sasaran->sasaran ?? '-' }}
                                </td>
                            @endif

                            <td style="border: 1px solid #000; vertical-align: top; text-align: left;">
                                {{ $indikator->nama_indikator ?? $indikator->indikator ?? '-' }}
                            </td>
                            
                            @php
                                $colTarget = 'target_' . $tahun;
                                $target = $indikator->$colTarget ?? $indikator->target ?? 0;
                                $target = (float) str_replace(',', '.', (string)$target);

                                $listRealisasi = isset($realisasiData[$indikator->id]) ? $realisasiData[$indikator->id] : collect([]);
                                $realisasiSd = $listRealisasi->sum(function($item) {
                                    return (float) str_replace(',', '.', (string)$item->realisasi);
                                });
                                $capaianSd = ($target > 0) ? ($realisasiSd / $target * 100) : 0;
                            @endphp

                            <td style="border: 1px solid #000; text-align: center;">-</td>
                            <td style="border: 1px solid #000; text-align: center;">{{ $indikator->satuan }}</td>
                            <td style="border: 1px solid #000; text-align: center;">{{ $target }}</td>
                            <td style="border: 1px solid #000; text-align: center;">{{ $realisasiSd }}</td>
                            <td style="border: 1px solid #000; text-align: center;">{{ number_format($capaianSd, 2) }}%</td>
                            <td style="border: 1px solid #000; text-align: center;">100</td>
                            <td style="border: 1px solid #000; text-align: center;">
                                {{ number_format($capaianSd, 2) }}%
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="border: 1px solid #000; text-align: center;">{{ $no++ }}</td>
                        <td style="border: 1px solid #000;">{{ $sasaran->sasaran ?? '-' }}</td>
                        <td style="border: 1px solid #000; color: red;">- Belum ada indikator -</td>
                        <td colspan="7" style="border: 1px solid #000;"></td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="10" style="border: 1px solid #000; text-align: center; padding: 10px;">Data Kinerja Belum Tersedia</td>
            </tr>
        @endif

        @if(isset($rencanaAksis) && $rencanaAksis->count() > 0)
            <tr>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: center;">Aktifitas yang berhubungan dengan Indikator</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: center;">Capaian Aktifitas Tahun Lalu</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: center;">Satuan Indikator Aktifitas</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: center;">Target aktifitas</td>
                <td style="border: 1px solid #000; font-weight: bold; text-align: center;">realisasi aktifitas</td>
                <td colspan="3" style="border: 1px solid #000; font-weight: bold; text-align: center;">capaian aktifitas</td>
            </tr>

            @foreach($rencanaAksis as $aksi)
                @php
                    $targetAksi = (float) str_replace(',', '.', (string)$aksi->target);
                    $listRealisasiAksi = isset($realisasiAksiData[$aksi->id]) ? $realisasiAksiData[$aksi->id] : collect([]);
                    $realisasiAksiSd = $listRealisasiAksi->sum(function($item) {
                        return (float) str_replace(',', '.', (string)$item->realisasi);
                    });
                    $capaianAksiSd = ($targetAksi > 0) ? ($realisasiAksiSd / $targetAksi * 100) : 0;
                @endphp
                <tr>
                    <td style="border: 1px solid #000;"></td>
                    <td style="border: 1px solid #000;"></td>
                    <td style="border: 1px solid #000; vertical-align: top;">{{ $aksi->nama_aksi }}</td>
                    <td style="border: 1px solid #000; text-align: center;">-</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $aksi->satuan }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $targetAksi }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $realisasiAksiSd }}</td>
                    <td colspan="3" style="border: 1px solid #000; text-align: center;">{{ number_format($capaianAksiSd, 2) }}%</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

{{-- SECTION PENJELASAN --}}
<table>
    <tr></tr>
    <tr>
        <td colspan="10" style="font-weight: bold; border: 1px solid #fff; text-align: left; padding-left: 5px;">
            Penjelasan per Indikator Kinerja
        </td>
    </tr>
    
    <tr>
        <td colspan="10" style="font-weight: bold; border: 1px solid #fff; text-align: left; padding-left: 5px;">
            Upaya :
        </td>
    </tr>
    <tr>
        <td colspan="10" style="vertical-align: top; text-wrap: wrap; border: 1px solid #fff; text-align: left; padding-left: 5px;">
            @if(isset($penjelasans) && $penjelasans->count() > 0)
                @foreach($penjelasans as $index => $item)
                    @if($item->upaya)
                        {{ $index + 1 }}. {{ $item->upaya }}<br>
                    @endif
                @endforeach
            @else
                -
            @endif
        </td>
    </tr>

    <tr>
        <td colspan="10" style="font-weight: bold; border: 1px solid #fff; text-align: left; padding-left: 5px;">
            Hambatan :
        </td>
    </tr>
    <tr>
        <td colspan="10" style="vertical-align: top; text-wrap: wrap; border: 1px solid #fff; text-align: left; padding-left: 5px;">
            @if(isset($penjelasans) && $penjelasans->count() > 0)
                @foreach($penjelasans as $index => $item)
                    @if($item->hambatan)
                        {{ $index + 1 }}. {{ $item->hambatan }}<br>
                    @endif
                @endforeach
            @else
                -
            @endif
        </td>
    </tr>

    <tr>
        <td colspan="10" style="font-weight: bold; border: 1px solid #fff; text-align: left; padding-left: 5px;">
            Rencana Tindak Lanjut :
        </td>
    </tr>
    <tr>
        <td colspan="10" style="vertical-align: top; text-wrap: wrap; border: 1px solid #fff; text-align: left; padding-left: 5px;">
            @if(isset($penjelasans) && $penjelasans->count() > 0)
                @foreach($penjelasans as $index => $item)
                    @if($item->tindak_lanjut)
                        {{ $index + 1 }}. {{ $item->tindak_lanjut }}<br>
                    @endif
                @endforeach
            @else
                -
            @endif
        </td>
    </tr>
</table>

{{-- SECTION TANDA TANGAN DINAMIS --}}
{{-- Format: Kiri = Atasan (Parent/Gubernur), Kanan = Pelapor --}}
<table>
    <tr></tr>
    {{-- Baris Tanggal di Kanan --}}
    <tr>
        <td colspan="5"></td>
        {{-- UPDATE: Menggunakan variabel $hariIni untuk memastikan tanggal sesuai WITA --}}
        <td colspan="5" style="text-align: center;">Banjarmasin, {{ $hariIni }} {{ ucfirst(strtolower($namaBulan)) }} {{ $tahun }}</td>
    </tr>
    
    {{-- Baris "Mengetahui" dan "Yang Melaporkan" --}}
    <tr>
        <td colspan="5" style="text-align: center;">Mengetahui Atasan Langsung</td>
        <td colspan="5" style="text-align: center;">Yang Melaporkan,</td>
    </tr>

    {{-- Baris Nama Jabatan --}}
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold;">
            {{ $atasan->nama ?? '(Atasan Langsung)' }}
        </td>
        <td colspan="5" style="text-align: center; font-weight: bold;">
            {{ $jabatan->nama ?? 'Nama Jabatan' }}
        </td>
    </tr>

    {{-- Spasi Tanda Tangan --}}
    <tr>
        <td colspan="10" style="height: 60px;"></td>
    </tr>

    {{-- Baris Nama Pegawai --}}
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; text-decoration: underline;">
            {{ $atasan->pegawai->nama ?? '(Belum ada pejabat)' }}
        </td>
        <td colspan="5" style="text-align: center; font-weight: bold; text-decoration: underline;">
            {{ $jabatan->pegawai->nama ?? '(Belum ada pejabat)' }}
        </td>
    </tr>

    {{-- Baris Pangkat / Golongan --}}
    <tr>
        <td colspan="5" style="text-align: center;">
            {{ $atasan->pegawai->pangkat ?? '' }} {{ $atasan->pegawai->golongan ?? '' }}
        </td>
        <td colspan="5" style="text-align: center;">
            {{ $jabatan->pegawai->pangkat ?? '' }} {{ $jabatan->pegawai->golongan ?? '' }}
        </td>
    </tr>

    {{-- Baris NIP --}}
    <tr>
        <td colspan="5" style="text-align: center;">
            NIP. {{ $atasan->pegawai->nip ?? '-' }}
        </td>
        <td colspan="5" style="text-align: center;">
            NIP. {{ $jabatan->pegawai->nip ?? '-' }}
        </td>
    </tr>
</table>