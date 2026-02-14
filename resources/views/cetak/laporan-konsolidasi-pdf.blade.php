<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Realisasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px; 
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 11px;
        }

        .header-table td {
            padding: 2px;
            vertical-align: top;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .main-table thead th {
            text-transform: uppercase;
            font-size: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .align-middle { vertical-align: middle; }

        /* Pengaturan Lebar Kolom Proporsional */
        .col-kode { width: 8%; }
        .col-uraian { width: 23%; }
        .col-indikator { width: 14%; }
        .col-satuan { width: 5%; }
        .col-target { width: 5%; }
        .col-pagu { width: 10%; }
        .col-real-keu { width: 10%; }
        .col-real-fisik { width: 4%; }
        .col-cap-keu { width: 4%; }
        .col-cap-fisik { width: 4%; }
        .col-sisa { width: 13%; }

        .indent-keg { padding-left: 12px !important; }
        .indent-sub { padding-left: 24px !important; }

        .page-break { page-break-after: always; }
        tr { page-break-inside: avoid; }
    </style>
</head>

<body>

    {{-- HELPER PHP UNTUK RUMUS --}}
    @php
        $parseNum = function($val) {
            if(is_int($val) || is_float($val)) return (float)$val;
            $strVal = (string)($val ?? '0');
            $strVal = str_replace('.', '', $strVal);
            $strVal = str_replace(',', '.', $strVal);
            $clean = preg_replace('/[^0-9\.]/', '', $strVal);
            return (float) ($clean ?: 0);
        };

        $hitungPersen = function($pembilang, $penyebut) use ($parseNum) {
            $a = $parseNum($pembilang);
            $b = $parseNum($penyebut);
            $hasil = ($b > 0) ? ($a / $b) * 100 : 0;
            return min($hasil, 100);
        };

        // HELPER FORMAT ANGKA TANPA .00
        $formatClean = function($val) {
            return (float)number_format($val, 2);
        };
    @endphp

    {{-- HEADER LAPORAN --}}
    <table class="header-table">
        <tr>
            <td width="15%">Kode SKPD</td>
            <td width="2%">:</td>
            <td width="83%">1.02.0.00.0.00.01.0000</td>
        </tr>
        <tr>
            <td>Nama SKPD</td>
            <td>:</td>
            <td>DINAS KESEHATAN</td>
        </tr>
        <tr>
            <td colspan="3" style="height: 10px;"></td>
        </tr>
        <tr>
            {{-- JUDUL DIPECAH JADI DUA BARIS DENGAN <br> AGAR RAPI --}}
            <td colspan="3" class="text-center" style="font-size: 14px; line-height: 1.2;">
                LAPORAN REALISASI KEUANGAN DAN FISIK<br>
                BULAN {{ strtoupper($laporan->bulan) }} TAHUN ANGGARAN {{ $laporan->tahun }}
            </td>
        </tr>
    </table>

    {{-- TABEL UTAMA --}}
    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" class="col-kode">Kode</th>
                <th rowspan="2" class="col-uraian">Program / Kegiatan / Sub Kegiatan</th>
                <th rowspan="2" class="col-indikator">Indikator</th>
                <th rowspan="2" class="col-satuan">Satuan</th>
                <th rowspan="2" class="col-target">Target</th>
                <th rowspan="2" class="col-pagu">Pagu<br>Anggaran</th>
                <th colspan="2">Realisasi S/D {{ $laporan->bulan }}</th>
                <th colspan="2">% Capaian</th>
                <th rowspan="2" class="col-sisa">Sisa<br>Anggaran</th>
            </tr>
            <tr>
                <th class="col-real-keu">Keuangan (Rp)</th>
                <th class="col-real-fisik">Fisik</th>
                <th class="col-cap-keu">Keu</th>
                <th class="col-cap-fisik">Fisik</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $progId => $group)
            @php
            $prog = $group['program'];
            $progAnggaran = $group['anggaran'] ?? null;

            $paguProg = $progAnggaran->pagu_anggaran ?? 0;
            $realisasiProg = $progAnggaran->pagu_realisasi ?? 0;
            
            $persenKeuProg = ($paguProg > 0) ? ($realisasiProg / $paguProg * 100) : 0;
            $sisaProg = $paguProg - $realisasiProg;

            // --- HITUNG RATA-RATA FISIK PROGRAM (LOGIKA SAMA DENGAN INPUT DATA) ---
            $totalPersenFisik_Prog = 0;
            $jumlahSub_Prog = 0;

            foreach($group['kegiatans'] as $kegDataLoop) {
                if(isset($kegDataLoop['details'])) {
                    foreach($kegDataLoop['details'] as $detailLoop) {
                        $tf = $detailLoop->target ?? 0;
                        $rf = $detailLoop->realisasi_fisik ?? 0;
                        // Hitung % per sub kegiatan
                        $pf = ($tf > 0) ? ($rf / $tf * 100) : 0;
                        $pf = min($pf, 100); // Mentok 100%

                        $totalPersenFisik_Prog += $pf;
                        $jumlahSub_Prog++;
                    }
                }
            }
            $avgFisikProg = ($jumlahSub_Prog > 0) ? ($totalPersenFisik_Prog / $jumlahSub_Prog) : 0;
            // --------------------------------------
            @endphp

            {{-- BARIS PROGRAM --}}
            <tr>
                <td class="text-center align-middle">{{ $prog->kode }}</td>
                <td class="align-middle">
                    <span>{{ strtoupper($prog->nama ?? $prog->nama_program) }}</span>
                </td>
                <td class="text-center align-middle">-</td>
                <td class="text-center align-middle">-</td>
                <td class="text-center align-middle">-</td>
                <td class="text-right align-middle">{{ number_format($paguProg, 0, ',', '.') }}</td>
                <td class="text-right align-middle">{{ number_format($realisasiProg, 0, ',', '.') }}</td>
                
                {{-- Realisasi Fisik Program: Tetap Strip --}}
                <td class="text-center align-middle">-</td>
                
                <td class="text-center align-middle">{{ number_format($persenKeuProg, 0) }}%</td>
                
                {{-- % Capaian Fisik Program: Tampilkan Rata-rata --}}
                <td class="text-center align-middle">{{ $formatClean($avgFisikProg) }}%</td>
                
                <td class="text-right align-middle">{{ number_format($sisaProg, 0, ',', '.') }}</td>
            </tr>

            {{-- LOOP KEGIATAN --}}
            @foreach($group['kegiatans'] as $kegId => $kegData)
            @php
            $keg = $kegData['kegiatan'];
            $kegAnggaran = $kegData['anggaran'] ?? null;

            $paguKeg = $kegAnggaran->pagu_anggaran ?? 0;
            $realisasiKeg = $kegAnggaran->pagu_realisasi ?? 0;

            $persenKeuKeg = ($paguKeg > 0) ? ($realisasiKeg / $paguKeg * 100) : 0;
            $sisaKeg = $paguKeg - $realisasiKeg;

            // --- HITUNG RATA-RATA FISIK KEGIATAN ---
            $totalPersenFisik_Keg = 0;
            $jumlahSub_Keg = 0;

            if(isset($kegData['details'])) {
                foreach($kegData['details'] as $detailLoop) {
                    $tf = $detailLoop->target ?? 0;
                    $rf = $detailLoop->realisasi_fisik ?? 0;
                    $pf = ($tf > 0) ? ($rf / $tf * 100) : 0;
                    $pf = min($pf, 100);

                    $totalPersenFisik_Keg += $pf;
                    $jumlahSub_Keg++;
                }
            }
            $avgFisikKeg = ($jumlahSub_Keg > 0) ? ($totalPersenFisik_Keg / $jumlahSub_Keg) : 0;
            // ---------------------------------------
            @endphp

            {{-- BARIS KEGIATAN --}}
            <tr>
                <td class="text-center align-middle">{{ $keg->kode }}</td>
                <td class="align-middle indent-keg">
                    <span>{{ $keg->nama ?? $keg->nama_kegiatan }}</span>
                </td>
                <td class="text-center align-middle">-</td>
                <td class="text-center align-middle">-</td>
                <td class="text-center align-middle">-</td>
                <td class="text-right align-middle">{{ number_format($paguKeg, 0, ',', '.') }}</td>
                <td class="text-right align-middle">{{ number_format($realisasiKeg, 0, ',', '.') }}</td>
                
                {{-- Realisasi Fisik Kegiatan: Tetap Strip --}}
                <td class="text-center align-middle">-</td>
                
                <td class="text-center align-middle">{{ number_format($persenKeuKeg, 0) }}%</td>
                
                {{-- % Capaian Fisik Kegiatan: Tampilkan Rata-rata --}}
                <td class="text-center align-middle">{{ $formatClean($avgFisikKeg) }}%</td>
                
                <td class="text-right align-middle">{{ number_format($sisaKeg, 0, ',', '.') }}</td>
            </tr>

            {{-- LOOP SUB KEGIATAN --}}
            @foreach($kegData['details'] as $detail)
            @php
            $sub = $detail;
            
            $targetSub = $sub->target ?? 0;
            $paguSub = $sub->pagu_anggaran ?? 0;
            $realisasiKeuSub = $sub->pagu_realisasi ?? 0;
            $realisasiFisikSub = $sub->realisasi_fisik ?? 0;

            $persenKeuSub = ($paguSub > 0) ? ($realisasiKeuSub / $paguSub * 100) : 0;
            $persenFisikSub = ($targetSub > 0) ? ($realisasiFisikSub / $targetSub * 100) : 0;
            $persenFisikSub = min($persenFisikSub, 100); // Mentok 100%

            $sisaSub = $paguSub - $realisasiKeuSub;

            $namaSub = $sub->subKegiatan->nama ?? 'Sub Kegiatan';
            @endphp
            <tr>
                <td class="text-center align-middle">{{ $sub->kode }}</td>
                <td class="align-middle indent-sub">
                    <span>{{ $namaSub }}</span>
                </td>
                <td class="align-middle" style="font-size: 8px;">{!! nl2br(e($sub->sub_output ?? '-')) !!}</td>
                <td class="text-center align-middle">{{ $sub->satuan_unit ?? '-' }}</td>
                <td class="text-center align-middle">{{ $targetSub }}</td>
                <td class="text-right align-middle">{{ number_format($paguSub, 0, ',', '.') }}</td>
                <td class="text-right align-middle">{{ number_format($realisasiKeuSub, 0, ',', '.') }}</td>
                
                {{-- Realisasi Fisik Sub Kegiatan: Tampilkan Nilai --}}
                <td class="text-center align-middle">{{ $realisasiFisikSub }}</td>
                
                <td class="text-center align-middle">{{ number_format($persenKeuSub, 0) }}%</td>
                
                {{-- % Capaian Fisik Sub Kegiatan --}}
                <td class="text-center align-middle">{{ $formatClean($persenFisikSub) }}%</td>
                
                <td class="text-right align-middle">{{ number_format($sisaSub, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            @endforeach
            @empty
            <tr>
                <td colspan="11" class="text-center" style="padding: 20px;">Belum ada data.</td>
            </tr>
            @endforelse
        </tbody>

        {{-- FOOTER TOTAL --}}
        @php
        // --- LOGIKA HITUNG MANUAL UNTUK FOOTER AGAR AKURAT ---
        
        $sumPagu = 0;
        $sumRealisasi = 0;
        $sumSisa = 0;
        
        $totalPersenKeu = 0; // Menampung total % keuangan Program
        $countProg = 0;      // Menghitung jumlah Program

        $totalPersenFisikSub = 0; // Menampung total % fisik SUB KEGIATAN
        $countSub = 0;            // Menghitung jumlah Sub Kegiatan

        foreach($data as $group) {
            // 1. Hitung Total Uang (Berdasarkan Anggaran Program biar konsisten)
            $p = $group['anggaran']->pagu_anggaran ?? 0;
            $r = $group['anggaran']->pagu_realisasi ?? 0;
            $s = $p - $r;
            
            $sumPagu += $p;
            $sumRealisasi += $r;
            $sumSisa += $s;

            // 2. Hitung Rata-rata Keuangan (Berdasarkan Program)
            $persenKeu = ($p > 0) ? ($r / $p * 100) : 0;
            $totalPersenKeu += $persenKeu;
            $countProg++;

            // 3. Hitung Rata-rata Fisik (Berdasarkan SUB KEGIATAN biar tidak 0)
            foreach($group['kegiatans'] as $kegData) {
                foreach($kegData['details'] as $detailSub) {
                    $tSub = $detailSub->target ?? 0;
                    $rSub = $detailSub->realisasi_fisik ?? 0;
                    
                    // Hitung % Fisik per Sub Kegiatan
                    $persenFisik = ($tSub > 0) ? ($rSub / $tSub * 100) : 0;
                    // Batasi maksimal 100% jika perlu (opsional)
                    $persenFisik = min($persenFisik, 100);

                    $totalPersenFisikSub += $persenFisik;
                    $countSub++;
                }
            }
        }

        // HASIL RATA-RATA
        $avgPersenKeu = ($countProg > 0) ? ($totalPersenKeu / $countProg) : 0;
        $avgPersenFisik = ($countSub > 0) ? ($totalPersenFisikSub / $countSub) : 0;
        @endphp

        <tfoot>
            <tr style="text-transform: uppercase;">
                <td colspan="5" class="text-center align-middle" style="font-size: 10px; padding: 6px; font-weight: bold;">TOTAL KESELURUHAN</td>
                
                {{-- Pagu Anggaran (SUM) --}}
                <td class="text-right align-middle" style="font-size: 10px;">{{ number_format($sumPagu, 0, ',', '.') }}</td>
                
                {{-- Realisasi Keuangan (SUM) --}}
                <td class="text-right align-middle" style="font-size: 10px;">{{ number_format($sumRealisasi, 0, ',', '.') }}</td>
                
                <td class="text-center align-middle">-</td>
                
                {{-- % Capaian Keuangan (RATA-RATA PROGRAM) --}}
                <td class="text-center align-middle" style="font-size: 10px;">{{ $formatClean($avgPersenKeu) }}%</td>
                
                {{-- % Capaian Fisik (RATA-RATA SUB KEGIATAN) --}}
                <td class="text-center align-middle" style="font-size: 10px;">{{ $formatClean($avgPersenFisik) }}%</td>
                
                {{-- Sisa Anggaran (SUM) --}}
                <td class="text-right align-middle" style="font-size: 10px;">{{ number_format($sumSisa, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN DINAMIS --}}
    @php
    $ttdJabatan = 'Kepala Dinas Kesehatan';
    $ttdInstansi = 'Provinsi Kalimantan Selatan';
    $ttdNama = 'Hj. RAUDATUL JANNAH, SKM, M.Kes';
    $ttdNip = '19680815 198903 2 008';

    if(isset($selectedJabatan) && $selectedJabatan) {
        $ttdJabatan = $selectedJabatan->nama;
        $pegawai = \App\Models\Pegawai::where('jabatan_id', $selectedJabatan->id)->first();
        if($pegawai) {
            $ttdNama = $pegawai->nama;
            $ttdNip = $pegawai->nip;
        } else {
            $ttdNama = '.....................................';
            $ttdNip = '.....................................';
        }
    }
    @endphp

    <br><br>
    <table width="100%" style="page-break-inside: avoid; border: none;">
        <tr>
            <td width="60%" style="border: none;"></td>
            <td width="40%" class="text-center" style="font-size: 11px; border: none;">
                Banjarmasin, ........................ {{ $laporan->bulan }} {{ $laporan->tahun }}<br>
                {{ $ttdJabatan }}<br>
                {{ $ttdInstansi }}
                <br><br><br><br><br>
                <u>{{ $ttdNama }}</u><br>
                NIP. {{ $ttdNip }}
            </td>
        </tr>
    </table>

</body>

</html>