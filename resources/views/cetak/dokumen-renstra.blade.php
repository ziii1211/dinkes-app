<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Matriks Renstra - {{ $header['unit_kerja'] ?? 'Dinas Kesehatan' }}</title>
    <style>
        /* Paksa ke format Landscape untuk Matriks agar kolom lebih lega */
        @page { 
            size: A4 landscape; 
            margin: 1.5cm; 
        }
        body { 
            font-family: 'Times New Roman', Times, serif; /* Font resmi pemerintahan */
            font-size: 11pt; 
            color: #000; 
            line-height: 1.3;
        }
        
        /* Gaya Kop Surat */
        .kop-surat {
            width: 100%;
            border-bottom: 4px double #000; /* Garis bawah ganda untuk kesan resmi */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat td {
            border: none;
            vertical-align: middle;
        }
        .kop-surat .logo {
            width: 85px;
            height: auto;
        }
        .kop-surat .judul-dokumen {
            text-align: center;
        }
        .kop-surat h1 { 
            font-size: 16pt; 
            font-weight: bold; 
            margin: 0 0 5px 0; 
            letter-spacing: 1px;
        }
        .kop-surat h2 { 
            font-size: 14pt; 
            font-weight: bold; 
            margin: 0 0 3px 0; 
        }
        .kop-surat h3 {
            font-size: 12pt;
            font-weight: normal;
            margin: 0;
        }
        
        /* Gaya Tabel Utama */
        .table-data { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px;
        }
        .table-data th, .table-data td { 
            border: 1px solid #000; 
            padding: 8px; 
            vertical-align: top; 
        }
        .table-data th { 
            background-color: #e0e0e0; /* Warna abu-abu resmi */
            text-align: center; 
            font-weight: bold; 
            font-size: 11pt;
            text-transform: uppercase;
        }
        .bg-section { 
            background-color: #f2f2f2; 
            font-weight: bold; 
            font-size: 11pt; 
        }
        .text-center { text-align: center; }
        
        /* Merapikan List Indikator */
        ul { margin: 0; padding-left: 20px; }
        li { margin-bottom: 4px; text-align: left; }
        .mb-2 { margin-bottom: 8px; }

        /* Tanda Tangan */
        .signature { 
            width: 100%; 
            margin-top: 40px; 
            page-break-inside: avoid; /* Mencegah TTD terpotong beda halaman */
        }
        .signature table { 
            width: 100%; 
            border: none; 
        }
        .signature td { 
            border: none; 
            font-size: 11pt;
        }
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('logo pemprov.png') }}" class="logo" alt="Logo Pemprov">
            </td>
            <td width="70%" class="judul-dokumen">
                <h1>MATRIKS DOKUMEN RENCANA STRATEGIS (RENSTRA)</h1>
                <h2>{{ strtoupper($header['unit_kerja'] ?? 'DINAS KESEHATAN') }}</h2>
                <h3>PERIODE TAHUN {{ $header['periode'] ?? '2025 - 2029' }}</h3>
            </td>
            <td width="15%"></td>
        </tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Tingkat Kinerja / Uraian</th>
                <th width="35%">Indikator Kinerja</th>
                <th width="25%">Target & Satuan</th>
            </tr>
        </thead>
        <tbody>
            
            {{-- ================= A. TUJUAN ================= --}}
            <tr><td colspan="4" class="bg-section">A. TUJUAN</td></tr>
            @forelse($tujuans as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->deskripsi ?? $item->nama ?? $item->tujuan ?? '-' }}</td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->indikator ?? $ind->nama_indikator ?? $ind->keterangan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->target ?? $ind->target_2025 ?? '-' }} {{ $ind->satuan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="font-style: italic;">Data Tujuan belum tersedia</td></tr>
            @endforelse

            {{-- ================= B. SASARAN ================= --}}
            <tr><td colspan="4" class="bg-section">B. SASARAN</td></tr>
            @forelse($sasarans as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->deskripsi ?? $item->nama ?? $item->sasaran ?? '-' }}</td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->indikator ?? $ind->nama_indikator ?? $ind->keterangan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->target ?? $ind->target_2025 ?? '-' }} {{ $ind->satuan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="font-style: italic;">Data Sasaran belum tersedia</td></tr>
            @endforelse

            {{-- ================= C. OUTCOME ================= --}}
            <tr><td colspan="4" class="bg-section">C. OUTCOME (PROGRAM)</td></tr>
            @forelse($outcomes as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                    <div class="mb-2"><strong>Program:</strong><br> {{ $item->program->nama ?? 'Nama Program Belum Diisi' }}</div>
                    <div><strong>Outcome:</strong><br> {{ $item->deskripsi ?? $item->nama ?? $item->outcome ?? '-' }}</div>
                </td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->indikator ?? $ind->nama_indikator ?? $ind->keterangan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->target ?? $ind->target_2025 ?? '-' }} {{ $ind->satuan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="font-style: italic;">Data Outcome belum tersedia</td></tr>
            @endforelse

            {{-- ================= D. KEGIATAN ================= --}}
            <tr><td colspan="4" class="bg-section">D. KEGIATAN</td></tr>
            @forelse($kegiatans as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->deskripsi ?? $item->nama ?? $item->kegiatan ?? '-' }}</td>
                <td>
                    @if($item->outputs && $item->outputs->count() > 0)
                        <ul>
                        @foreach($item->outputs as $output)
                            @foreach($output->indikators as $ind)
                                <li>{{ $ind->indikator ?? $ind->nama_indikator ?? $ind->keterangan ?? '-' }}</li>
                            @endforeach
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    @if($item->outputs && $item->outputs->count() > 0)
                        <ul>
                        @foreach($item->outputs as $output)
                            @foreach($output->indikators as $ind)
                                <li>{{ $ind->target ?? $ind->target_2025 ?? '-' }} {{ $ind->satuan ?? '-' }}</li>
                            @endforeach
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="font-style: italic;">Data Kegiatan belum tersedia</td></tr>
            @endforelse

            {{-- ================= E. SUB KEGIATAN ================= --}}
            <tr><td colspan="4" class="bg-section">E. SUB KEGIATAN</td></tr>
            @forelse($sub_kegiatans as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->deskripsi ?? $item->nama ?? $item->sub_kegiatan ?? '-' }}</td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->indikator ?? $ind->nama_indikator ?? $ind->keterangan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
                <td>
                    @if($item->indikators && $item->indikators->count() > 0)
                        <ul>
                        @foreach($item->indikators as $ind)
                            <li>{{ $ind->target ?? $ind->target_2025 ?? '-' }} {{ $ind->satuan ?? '-' }}</li>
                        @endforeach
                        </ul>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="font-style: italic;">Data Sub Kegiatan belum tersedia</td></tr>
            @endforelse

        </tbody>
    </table>

    <div class="signature">
        <table>
            <tr>
                <td style="width: 60%;"></td> 
                <td style="width: 40%; text-align: center;">
                    Banjarmasin, {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->translatedFormat('d F Y') }} <br>
                    Kepala Dinas Kesehatan<br>
                    Provinsi Kalimantan Selatan
                    <br><br><br><br><br>
                    <strong><u>Dr. Diauddin, M.Kes</u></strong><br>
                    NIP. 19770923 200604 1 015
                </td>
            </tr>
        </table>
    </div>

</body>
</html>