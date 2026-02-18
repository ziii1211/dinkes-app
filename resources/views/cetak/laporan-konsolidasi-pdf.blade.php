<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Realisasi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header-table { width: 100%; margin-bottom: 20px; font-weight: bold; font-size: 11px; }
        .header-table td { padding: 2px; vertical-align: top; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 4px; vertical-align: top; word-wrap: break-word; }
        .main-table thead th { text-transform: uppercase; font-size: 8px; text-align: center; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .align-middle { vertical-align: middle; }
        /* Lebar Kolom */
        .col-kode { width: 8%; } .col-uraian { width: 23%; } .col-indikator { width: 14%; }
        .col-satuan { width: 5%; } .col-target { width: 5%; } .col-pagu { width: 10%; }
        .col-real-keu { width: 10%; } .col-real-fisik { width: 4%; }
        .col-cap-keu { width: 4%; } .col-cap-fisik { width: 4%; } .col-sisa { width: 13%; }
        .indent-keg { padding-left: 12px !important; } .indent-sub { padding-left: 24px !important; }
        .page-break { page-break-after: always; } tr { page-break-inside: avoid; }
    </style>
</head>

<body>

    {{-- HELPER PHP --}}
    @php
        $parseNum = function($val) {
            if(is_int($val) || is_float($val)) return (float)$val;
            $strVal = (string)($val ?? '0');
            $strVal = str_replace('.', '', $strVal); 
            $strVal = str_replace(',', '.', $strVal);
            $clean = preg_replace('/[^0-9\.]/', '', $strVal);
            return (float) ($clean ?: 0);
        };

        $formatPersen = function($val) {
            if($val == 0) return '0%';
            return number_format((float)$val, 2, ',', '.') . '%';
        };
    @endphp

    {{-- HEADER --}}
    <table class="header-table">
        <tr><td width="15%">Kode SKPD</td><td width="2%">:</td><td width="83%">1.02.0.00.0.00.01.0000</td></tr>
        <tr><td>Nama SKPD</td><td>:</td><td>DINAS KESEHATAN</td></tr>
        <tr><td colspan="3" style="height: 10px;"></td></tr>
        <tr>
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
            $persenFisikProg = $progAnggaran->realisasi_fisik ?? 0; 
            $sisaProg = $paguProg - $realisasiProg;
            @endphp

            <tr>
                <td class="text-center align-middle">{{ $prog->kode }}</td>
                <td class="align-middle"><span>{{ strtoupper($prog->nama ?? $prog->nama_program) }}</span></td>
                <td class="text-center align-middle">-</td><td class="text-center align-middle">-</td><td class="text-center align-middle">-</td>
                <td class="text-right align-middle">{{ number_format($paguProg, 0, ',', '.') }}</td>
                <td class="text-right align-middle">{{ number_format($realisasiProg, 0, ',', '.') }}</td>
                <td class="text-center align-middle">-</td>
                <td class="text-center align-middle">{{ $formatPersen($persenKeuProg) }}</td>
                <td class="text-center align-middle">{{ $formatPersen($persenFisikProg) }}</td>
                <td class="text-right align-middle">{{ number_format($sisaProg, 0, ',', '.') }}</td>
            </tr>

            @foreach($group['kegiatans'] as $kegId => $kegData)
            @php
            $keg = $kegData['kegiatan'];
            $kegAnggaran = $kegData['anggaran'] ?? null;
            $paguKeg = $kegAnggaran->pagu_anggaran ?? 0;
            $realisasiKeg = $kegAnggaran->pagu_realisasi ?? 0;
            $persenKeuKeg = ($paguKeg > 0) ? ($realisasiKeg / $paguKeg * 100) : 0;
            $persenFisikKeg = $kegAnggaran->realisasi_fisik ?? 0; 
            $sisaKeg = $paguKeg - $realisasiKeg;
            @endphp

            <tr>
                <td class="text-center align-middle">{{ $keg->kode }}</td>
                <td class="align-middle indent-keg"><span>{{ $keg->nama ?? $keg->nama_kegiatan }}</span></td>
                <td class="text-center align-middle">-</td><td class="text-center align-middle">-</td><td class="text-center align-middle">-</td>
                <td class="text-right align-middle">{{ number_format($paguKeg, 0, ',', '.') }}</td>
                <td class="text-right align-middle">{{ number_format($realisasiKeg, 0, ',', '.') }}</td>
                <td class="text-center align-middle">-</td>
                <td class="text-center align-middle">{{ $formatPersen($persenKeuKeg) }}</td>
                <td class="text-center align-middle">{{ $formatPersen($persenFisikKeg) }}</td>
                <td class="text-right align-middle">{{ number_format($sisaKeg, 0, ',', '.') }}</td>
            </tr>

            @foreach($kegData['details'] as $detail)
            @php
            $sub = $detail;
            $targetSub = $sub->target ?? 0;
            $paguSub = $sub->pagu_anggaran ?? 0;
            $realisasiKeuSub = $sub->pagu_realisasi ?? 0;
            $realisasiFisikSub = $sub->realisasi_fisik ?? 0;
            $persenKeuSub = ($paguSub > 0) ? ($realisasiKeuSub / $paguSub * 100) : 0;
            $persenFisikSub = ($targetSub > 0) ? ($realisasiFisikSub / $targetSub * 100) : 0;
            $persenFisikSub = min($persenFisikSub, 100);
            $sisaSub = $paguSub - $realisasiKeuSub;
            $namaSub = $sub->subKegiatan->nama ?? 'Sub Kegiatan';
            @endphp
            <tr>
                <td class="text-center align-middle">{{ $sub->kode }}</td>
                <td class="align-middle indent-sub"><span>{{ $namaSub }}</span></td>
                <td class="align-middle" style="font-size: 8px;">{!! nl2br(e($sub->sub_output ?? '-')) !!}</td>
                <td class="text-center align-middle">{{ $sub->satuan_unit ?? '-' }}</td>
                <td class="text-center align-middle">{{ $targetSub }}</td>
                <td class="text-right align-middle">{{ number_format($paguSub, 0, ',', '.') }}</td>
                <td class="text-right align-middle">{{ number_format($realisasiKeuSub, 0, ',', '.') }}</td>
                <td class="text-center align-middle">{{ $realisasiFisikSub }}</td>
                <td class="text-center align-middle">{{ $formatPersen($persenKeuSub) }}</td>
                <td class="text-center align-middle">{{ $formatPersen($persenFisikSub) }}</td>
                <td class="text-right align-middle">{{ number_format($sisaSub, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @endforeach
            @empty
            <tr><td colspan="11" class="text-center" style="padding: 20px;">Belum ada data.</td></tr>
            @endforelse
        </tbody>

        @php
        $sumPagu = 0; $sumRealisasi = 0; $sumSisa = 0;
        $totalPersenKeu = 0; $totalPersenFisik = 0; $countProg = 0;

        foreach($data as $group) {
            $p = $group['anggaran']->pagu_anggaran ?? 0;
            $r = $group['anggaran']->pagu_realisasi ?? 0;
            $sumPagu += $p; $sumRealisasi += $r; $sumSisa += ($p - $r);
            
            $persenKeu = ($p > 0) ? ($r / $p * 100) : 0;
            $totalPersenKeu += $persenKeu;
            
            $persenFisik = $group['anggaran']->realisasi_fisik ?? 0;
            $totalPersenFisik += $persenFisik;
            $countProg++;
        }
        $avgPersenKeu = ($countProg > 0) ? ($totalPersenKeu / $countProg) : 0;
        $avgPersenFisik = ($countProg > 0) ? ($totalPersenFisik / $countProg) : 0;
        @endphp

        <tfoot>
            <tr style="text-transform: uppercase;">
                <td colspan="5" class="text-center align-middle" style="font-size: 10px; padding: 6px; font-weight: bold;">TOTAL KESELURUHAN</td>
                <td class="text-right align-middle" style="font-size: 10px;">{{ number_format($sumPagu, 0, ',', '.') }}</td>
                <td class="text-right align-middle" style="font-size: 10px;">{{ number_format($sumRealisasi, 0, ',', '.') }}</td>
                <td class="text-center align-middle">-</td>
                <td class="text-center align-middle" style="font-size: 10px;">{{ $formatPersen($avgPersenKeu) }}</td>
                <td class="text-center align-middle" style="font-size: 10px;">{{ $formatPersen($avgPersenFisik) }}</td>
                <td class="text-right align-middle" style="font-size: 10px;">{{ number_format($sumSisa, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN DINAMIS --}}
    @php
        // 1. Ambil Variabel Selected Jabatan (Prioritas dari Controller)
        $ttdJabatanModel = $selectedJabatan ?? null;

        // 2. Jika Kosong (Misal saat Cetak Semua / Kepala Dinas), Coba Ambil dari Request
        if (!$ttdJabatanModel) {
            $reqJabatanId = request('jabatan_id');
            $reqTtdId = request('ttd_id'); // Ini parameter khusus Kepala Dinas dari InputData.php
            $idToFind = $reqJabatanId ?: $reqTtdId;

            if ($idToFind) {
                $ttdJabatanModel = \App\Models\Jabatan::find($idToFind);
            }
        }

        // 3. Set Default Kosong
        $ttdJabatan = '';
        $ttdInstansi = '';
        $ttdNama = '';
        $ttdNip = '';

        // 4. Jika Jabatan Ditemukan, Cari Pegawainya
        if ($ttdJabatanModel) {
            $ttdJabatan = $ttdJabatanModel->nama;
            $ttdInstansi = 'Provinsi Kalimantan Selatan'; // Default Instansi

            // Cari Pegawai yang menjabat
            $pegawai = \App\Models\Pegawai::where('jabatan_id', $ttdJabatanModel->id)->first();
            
            if ($pegawai) {
                $ttdNama = $pegawai->nama;
                $ttdNip = $pegawai->nip;
            } else {
                $ttdNama = '.....................................';
                $ttdNip = '.....................................';
            }
        }
    @endphp

    <br><br>
    {{-- TAMPILKAN TANDA TANGAN HANYA JIKA ADA DATA PEJABAT --}}
    @if($ttdJabatanModel)
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
    @endif

</body>
</html>