<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Visualisasi Pohon Kinerja - Dinas Kesehatan</title>
    <style>
        @page { 
            size: a4 landscape;
            margin: 8mm 10mm 8mm 10mm; 
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 7.5pt; 
            color: #1f2937; 
            line-height: 1.25;
        }
        
        /* Kop Surat Resmi */
        .table-kop { 
            width: 100%; 
            border-bottom: 2px solid #000; 
            padding-bottom: 4px; 
            margin-bottom: 6px; 
            border-collapse: collapse;
        }
        .table-kop td { 
            border: none; 
            vertical-align: middle; 
        }
        .table-kop .logo-col {
            width: 55px;
            text-align: center;
        }
        .table-kop .logo-img { 
            width: 45px; 
            height: auto; 
        }
        .table-kop .header-title { 
            text-align: center; 
        }
        .table-kop .header-title h1 { 
            font-size: 11pt; 
            font-weight: bold; 
            margin: 0 0 2px 0; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111827;
        }
        .table-kop .header-title h2 { 
            font-size: 9pt; 
            font-weight: bold; 
            margin: 0 0 1px 0; 
            color: #1f2937;
        }
        .table-kop .header-title h3 { 
            font-size: 8pt; 
            font-weight: normal; 
            margin: 0; 
            color: #4b5563;
        }

        /* Line Connectors */
        .stem-vertical { 
            width: 2px; 
            height: 10px; 
            background-color: #64748b; 
            margin: 0 auto; 
        }

        /* Kartu Kinerja (Sesuai Desain Menu Visualisasi) */
        .card-root { 
            border: 2px solid #dc3545; 
            border-radius: 4px; 
            overflow: hidden; 
            background: #ffffff; 
            width: 82%; 
            margin: 0 auto; 
            page-break-inside: avoid;
        }
        .card-root .header { 
            background-color: #dc3545; 
            color: #ffffff; 
            font-weight: bold; 
            padding: 4px 8px; 
            text-transform: uppercase; 
            font-size: 8.5pt; 
            text-align: center;
        }

        .card-l1 { 
            border: 1.5px solid #0d6efd; 
            border-radius: 4px; 
            overflow: hidden; 
            background: #ffffff; 
            width: 96%; 
            margin: 0 auto; 
            page-break-inside: avoid;
        }
        .card-l1 .header { 
            background-color: #0d6efd; 
            color: #ffffff; 
            font-weight: bold; 
            padding: 3px 5px; 
            text-transform: uppercase; 
            font-size: 7.5pt; 
            text-align: center;
        }

        .card-l1-single { 
            border: 2px solid #0d6efd; 
            border-radius: 4px; 
            overflow: hidden; 
            background: #ffffff; 
            margin: 0 auto; 
            page-break-inside: avoid;
        }
        .card-l1-single .header { 
            background-color: #0d6efd; 
            color: #ffffff; 
            font-weight: bold; 
            padding: 4px 8px; 
            text-transform: uppercase; 
            font-size: 8pt; 
            text-align: center;
        }

        .card-l2 { 
            border: 1.5px solid #f59e0b; 
            border-radius: 4px; 
            overflow: hidden; 
            background: #ffffff; 
            width: 96%; 
            margin: 0 auto; 
            page-break-inside: avoid;
        }
        .card-l2 .header { 
            background-color: #f59e0b; 
            color: #111827; 
            font-weight: bold; 
            padding: 3px 6px; 
            text-transform: uppercase; 
            font-size: 7.5pt; 
            text-align: center;
        }

        /* Tabel Isi Kinerja */
        .table-content { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 7pt; 
            text-align: left; 
        }
        .table-content th { 
            background-color: #f1f5f9; 
            color: #334155; 
            padding: 2.5px 4px; 
            border: 1px solid #cbd5e1; 
            font-weight: bold; 
            text-align: center; 
            font-size: 7pt;
            text-transform: uppercase;
        }
        .table-content td { 
            padding: 2.5px 4px; 
            border: 1px solid #e2e8f0; 
            vertical-align: top; 
            color: #1e293b;
        }

        .page-break { 
            page-break-after: always; 
        }
    </style>
</head>
<body>

    @php
        $root = $tree->first();
        $level1 = $root ? $root->children->values() : collect([]);
    @endphp

    @if($root)
        {{-- ========================================================= --}}
        {{-- HALAMAN 1: BAGAN UTAMA LANDSCAPE (LEVEL 0 KE LEVEL 1)     --}}
        {{-- ========================================================= --}}
        <table class="table-kop">
            <tr>
                <td class="logo-col">
                    <img src="{{ public_path('logo pemprov.png') }}" class="logo-img" alt="Logo Pemprov">
                </td>
                <td class="header-title">
                    <h1>LAPORAN VISUALISASI POHON KINERJA</h1>
                    <h2>{{ strtoupper($header['unit_kerja'] ?? 'DINAS KESEHATAN PROVINSI KALIMANTAN SELATAN') }}</h2>
                    <h3>BAGAN UTAMA EKSEKUTIF (LEVEL 0 &bull; KEPALA DINAS KE LEVEL 1 &bull; SEKRETARIS &amp; KEPALA BIDANG)</h3>
                </td>
                <td style="width: 55px;"></td>
            </tr>
        </table>

        {{-- KARTU LEVEL 0 (ROOT - KEPALA DINAS) DI TENGAH ATAS --}}
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td align="center">
                    <div class="card-root">
                        <div class="header">{{ $root->jabatan ?: 'Kepala Dinas' }} &bull; LEVEL 0 (ULTIMATE OUTCOME)</div>
                        <table class="table-content">
                            <thead>
                                <tr>
                                    <th style="width: 44%;">Kinerja Utama</th>
                                    <th style="width: 40%;">Indikator Kinerja</th>
                                    <th style="width: 16%;">Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rootItems = is_array($root->kinerja_items) ? $root->kinerja_items : [];
                                @endphp
                                @forelse($rootItems as $k)
                                    @php
                                        $inds = $k['indikators'] ?? [];
                                        $rs = max(1, count($inds));
                                    @endphp
                                    @if(count($inds) > 0)
                                        @foreach($inds as $i => $ind)
                                            <tr>
                                                @if($i === 0)
                                                    <td rowspan="{{ $rs }}" style="font-weight: bold; background-color: #fafafa;">{{ $k['kinerja_utama'] }}</td>
                                                @endif
                                                <td>&bull; {{ $ind['nama'] ?? '-' }}</td>
                                                <td style="text-align: center; font-weight: bold; color: #1e3a8a;">{{ ($ind['nilai'] ?? '-') . ' ' . ($ind['satuan'] ?? '') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td style="font-weight: bold;">{{ $k['kinerja_utama'] }}</td>
                                            <td style="color: #9ca3af; font-style: italic;">Belum ada indikator</td>
                                            <td style="text-align: center;">-</td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: #9ca3af; font-style: italic;">Belum ada data kinerja</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <div class="stem-vertical"></div>
                </td>
            </tr>
        </table>

        {{-- CABANG LEVEL 1 SEJAJAR MENDATAR (HORIZONTAL LANDSCAPE COLUMNS) --}}
        @if($level1->count() > 0)
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <tr>
                    @php $colWidth = 100 / $level1->count(); @endphp
                    @foreach($level1 as $l1)
                        <td style="width: {{ $colWidth }}%; vertical-align: top; padding: 0 2px;">
                            <div class="card-l1">
                                <div class="header">{{ $l1->jabatan }}</div>
                                <table class="table-content">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">Kinerja Utama</th>
                                            <th style="width: 50%;">Indikator &amp; Target</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $l1Items = is_array($l1->kinerja_items) ? $l1->kinerja_items : [];
                                        @endphp
                                        @forelse($l1Items as $k)
                                            @php
                                                $inds = $k['indikators'] ?? [];
                                                $rs = max(1, count($inds));
                                            @endphp
                                            @if(count($inds) > 0)
                                                @foreach($inds as $i => $ind)
                                                    <tr>
                                                        @if($i === 0)
                                                            <td rowspan="{{ $rs }}" style="font-weight: 600;">{{ $k['kinerja_utama'] }}</td>
                                                        @endif
                                                        <td>
                                                            &bull; {{ $ind['nama'] ?? '-' }}<br>
                                                            <strong style="color: #1e3a8a;">({{ ($ind['nilai'] ?? '-') . ' ' . ($ind['satuan'] ?? '') }})</strong>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td style="font-weight: 600;">{{ $k['kinerja_utama'] }}</td>
                                                    <td style="color: #9ca3af; font-style: italic;">-</td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="2" style="text-align: center; color: #9ca3af; font-style: italic;">Data kinerja kosong</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    @endforeach
                </tr>
            </table>
        @endif

        {{-- ========================================================= --}}
        {{-- HALAMAN BERIKUTNYA: PENJABARAN CASCADING PER BIDANG       --}}
        {{-- ========================================================= --}}
        @php
            // Filter hanya Bidang yang memiliki turunan (anak)
            $bidangsWithChildren = $level1->filter(function($b) {
                return isset($b->children) && $b->children->count() > 0;
            })->values();

            // Kelompokkan 1 atau 2 bidang per halaman agar muat lapang & mendatar
            $pagedGroups = [];
            $tempGroup = [];
            $tempSubCount = 0;

            foreach($bidangsWithChildren as $bidang) {
                $subCount = $bidang->children->count();
                // Jika sudah ada bidang lain dan ditambah bidang ini jadi > 4 sub-kegiatan, buat halaman baru
                if(!empty($tempGroup) && ($tempSubCount + $subCount > 4 || $subCount >= 3)) {
                    $pagedGroups[] = $tempGroup;
                    $tempGroup = [$bidang];
                    $tempSubCount = $subCount;
                } else {
                    $tempGroup[] = $bidang;
                    $tempSubCount += $subCount;
                    if($subCount >= 3) {
                        $pagedGroups[] = $tempGroup;
                        $tempGroup = [];
                        $tempSubCount = 0;
                    }
                }
            }
            if(!empty($tempGroup)) {
                $pagedGroups[] = $tempGroup;
            }
        @endphp

        @foreach($pagedGroups as $pageIndex => $group)
            <div class="page-break"></div>

            <table class="table-kop">
                <tr>
                    <td class="logo-col">
                        <img src="{{ public_path('logo pemprov.png') }}" class="logo-img" alt="Logo Pemprov">
                    </td>
                    <td class="header-title">
                        <h1>LAPORAN VISUALISASI POHON KINERJA</h1>
                        <h2>{{ strtoupper($header['unit_kerja'] ?? 'DINAS KESEHATAN PROVINSI KALIMANTAN SELATAN') }}</h2>
                        <h3>PENJABARAN CASCADING BIDANG (LEVEL 1 &bull; KEPALA BIDANG KE LEVEL 2 &bull; SEKSI / SUBBAG)</h3>
                    </td>
                    <td style="width: 55px;"></td>
                </tr>
            </table>

            @foreach($group as $bIndex => $bidang)
                @php
                    $subNodes = $bidang->children->values();
                    $subCount = max(1, $subNodes->count());
                    $singleCardWidth = (count($group) > 1) ? '82%' : '65%';
                @endphp

                <div style="margin-bottom: {{ count($group) > 1 ? '12px' : '0' }};">
                    {{-- KARTU BIDANG (PARENT LEVEL 1) --}}
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td align="center">
                                <div class="card-l1-single" style="width: {{ $singleCardWidth }};">
                                    <div class="header">{{ $bidang->jabatan }} &bull; LEVEL 1 (INTERMEDIATE OUTCOME)</div>
                                    <table class="table-content">
                                        <thead>
                                            <tr>
                                                <th style="width: 44%;">Kinerja Utama</th>
                                                <th style="width: 40%;">Indikator Kinerja</th>
                                                <th style="width: 16%;">Target</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $bItems = is_array($bidang->kinerja_items) ? $bidang->kinerja_items : [];
                                            @endphp
                                            @forelse($bItems as $k)
                                                @php
                                                    $inds = $k['indikators'] ?? [];
                                                    $rs = max(1, count($inds));
                                                @endphp
                                                @if(count($inds) > 0)
                                                    @foreach($inds as $i => $ind)
                                                        <tr>
                                                            @if($i === 0)
                                                                <td rowspan="{{ $rs }}" style="font-weight: 600; background-color: #fafafa;">{{ $k['kinerja_utama'] }}</td>
                                                            @endif
                                                            <td>&bull; {{ $ind['nama'] ?? '-' }}</td>
                                                            <td style="text-align: center; font-weight: bold; color: #1e3a8a;">{{ ($ind['nilai'] ?? '-') . ' ' . ($ind['satuan'] ?? '') }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td style="font-weight: 600;">{{ $k['kinerja_utama'] }}</td>
                                                        <td style="color: #9ca3af; font-style: italic;">Belum ada indikator</td>
                                                        <td style="text-align: center;">-</td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="3" style="text-align: center; color: #9ca3af; font-style: italic;">Data kinerja kosong</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div class="stem-vertical"></div>
                            </td>
                        </tr>
                    </table>

                    {{-- KARTU-KARTU SEKSI/SUBBAG (ANAK LEVEL 2) SEJAJAR MENDATAR --}}
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            @php $subColWidth = 100 / $subCount; @endphp
                            @foreach($subNodes as $sub)
                                <td style="width: {{ $subColWidth }}%; vertical-align: top; padding: 0 4px;">
                                    <div class="card-l2">
                                        <div class="header">{{ $sub->jabatan }} &bull; LEVEL 2 (OUTPUT)</div>
                                        <table class="table-content">
                                            <thead>
                                                <tr>
                                                    <th style="width: 44%;">Kinerja Utama</th>
                                                    <th style="width: 40%;">Indikator</th>
                                                    <th style="width: 16%;">Target</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $sItems = is_array($sub->kinerja_items) ? $sub->kinerja_items : [];
                                                @endphp
                                                @forelse($sItems as $k)
                                                    @php
                                                        $inds = $k['indikators'] ?? [];
                                                        $rs = max(1, count($inds));
                                                    @endphp
                                                    @if(count($inds) > 0)
                                                        @foreach($inds as $i => $ind)
                                                            <tr>
                                                                @if($i === 0)
                                                                    <td rowspan="{{ $rs }}" style="font-weight: 600; background-color: #fafafa;">{{ $k['kinerja_utama'] }}</td>
                                                                @endif
                                                                <td>&bull; {{ $ind['nama'] ?? '-' }}</td>
                                                                <td style="text-align: center; font-weight: bold; color: #1e3a8a;">{{ ($ind['nilai'] ?? '-') . ' ' . ($ind['satuan'] ?? '') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td style="font-weight: 600;">{{ $k['kinerja_utama'] }}</td>
                                                            <td style="color: #9ca3af; font-style: italic;">-</td>
                                                            <td style="text-align: center;">-</td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="3" style="text-align: center; color: #9ca3af; font-style: italic;">Data kinerja kosong</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
            @endforeach
        @endforeach

    @else
        <div style="text-align: center; color: #6b7280; font-style: italic; margin-top: 40px; padding: 20px; border: 1px dashed #d1d5db; border-radius: 8px;">
            Belum ada data Visualisasi Pohon Kinerja yang dibuat.
        </div>
    @endif

</body>
</html>