<table>
    <thead>
        {{-- JUDUL LAPORAN --}}
        <tr>
            <th colspan="6" style="font-weight: bold; font-size: 14px;">
                UNIT KERJA: {{ $header['unit_kerja'] ?? ($unit_kerja ?? 'DINAS KESEHATAN') }}
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: bold; font-size: 12px;">
                PERIODE: {{ $header['periode'] ?? ($periode ?? '2025 - 2029') }}
            </th>
        </tr>
        <tr>
            <th colspan="6"></th> {{-- Jeda Baris Kosong --}}
        </tr>
        
        {{-- HEADER KOLOM --}}
        <tr>
            <th style="font-weight: bold; text-align: center; background-color: #1A2C42; color: #ffffff;">TUJUAN</th>
            <th style="font-weight: bold; text-align: center; background-color: #1A2C42; color: #ffffff;">SASARAN</th>
            <th style="font-weight: bold; text-align: center; background-color: #1A2C42; color: #ffffff;">OUTCOME</th>
            <th style="font-weight: bold; text-align: center; background-color: #1A2C42; color: #ffffff;">OUTPUT</th>
            <th style="font-weight: bold; text-align: center; background-color: #1A2C42; color: #ffffff;">INDIKATOR</th>
            <th style="font-weight: bold; text-align: center; background-color: #1A2C42; color: #ffffff;">PROGRAM / KEGIATAN</th>
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
                    @foreach($tujuan->indikators_from_pohon as $ind)
                        - {{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}<br>
                    @endforeach
                @elseif($tujuan instanceof \Illuminate\Database\Eloquent\Model && $tujuan->pohonKinerja)
                    @foreach($tujuan->pohonKinerja->indikators as $ind)
                        - {{ $ind->nama_indikator }}<br>
                    @endforeach
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
                    @foreach($sasaran->indikators_from_pohon as $ind)
                        - {{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}<br>
                    @endforeach
                @elseif($sasaran instanceof \Illuminate\Database\Eloquent\Model && $sasaran->pohonKinerja)
                    @foreach($sasaran->pohonKinerja->indikators as $ind)
                        - {{ $ind->nama_indikator }}<br>
                    @endforeach
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
                    @foreach($outcome->indikators_from_pohon as $ind)
                        - {{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}<br>
                    @endforeach
                @elseif($outcome instanceof \Illuminate\Database\Eloquent\Model && $outcome->pohonKinerja)
                    @foreach($outcome->pohonKinerja->indikators as $ind)
                        - {{ $ind->nama_indikator }}<br>
                    @endforeach
                @endif
            </td>
            {{-- Program --}}
            <td>
                @if(isset($outcome->program) && !empty($outcome->program->nama))
                    [{{ $outcome->program->kode ?? '-' }}] {{ $outcome->program->nama }}
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
                    @foreach($kegiatan->indikators_from_pohon as $ind)
                        - {{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}<br>
                    @endforeach
                @elseif($kegiatan instanceof \Illuminate\Database\Eloquent\Model && $kegiatan->pohonKinerja)
                    @foreach($kegiatan->pohonKinerja->indikators as $ind)
                        - {{ $ind->nama_indikator }}<br>
                    @endforeach
                @endif
            </td>
            {{-- Nama Kegiatan --}}
            <td>
                @if(!empty($kegiatan->nama))
                    [{{ $kegiatan->kode ?? '-' }}] {{ $kegiatan->nama }}
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
                @endif
            </td>
            {{-- Indikator Sub Kegiatan --}}
            <td>
                @if(isset($sub->indikators_from_pohon) && count($sub->indikators_from_pohon) > 0)
                    @foreach($sub->indikators_from_pohon as $ind)
                        - {{ is_object($ind) ? ($ind->nama_indikator ?? '-') : $ind }}<br>
                    @endforeach
                @elseif($sub instanceof \Illuminate\Database\Eloquent\Model && $sub->pohonKinerja)
                    @foreach($sub->pohonKinerja->indikators as $ind)
                        - {{ $ind->nama_indikator }}<br>
                    @endforeach
                @endif
            </td>
            {{-- Nama Sub Kegiatan --}}
            <td>
                [{{ $sub->kode ?? '-' }}] {{ $sub->nama ?? '' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>