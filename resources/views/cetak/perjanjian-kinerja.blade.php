<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Perjanjian Kinerja</title>
    <style>
        @page { size: A4 portrait; margin: 2cm; }
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; margin: 0; padding: 20px; }
        .no-print { display: none; }
        
        /* Header */
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .text-lg { font-size: 14pt; line-height: 1.5; }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11pt; }
        th, td { border: 1px solid #000; padding: 8px; vertical-align: top; }
        th { background-color: #fff; text-align: center; font-weight: bold; }
        
        /* Signature */
        .signature-container { display: flex; justify-content: space-between; margin-top: 50px; page-break-inside: avoid; }
        .signature-box { width: 40%; text-align: center; }
        .signature-name { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .signature-nip { font-size: 10pt; }

        /* Toolbar Button */
        .toolbar {
            position: fixed; top: 0; left: 0; right: 0; background: #333; padding: 10px; text-align: center;
        }
        .btn {
            background: #007bff; color: white; border: none; padding: 8px 15px; cursor: pointer; border-radius: 4px; font-family: sans-serif; text-decoration: none; display: inline-block;
        }
        .btn-back { background: #6c757d; margin-right: 10px; }
        
        @media print {
            .toolbar { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Toolbar untuk Tombol -->
    <div class="toolbar">
        <a href="javascript:history.back()" class="btn btn-back">Kembali</a>
        <button onclick="window.print()" class="btn">Cetak</button>
    </div>

    <div style="margin-top: 30px;">
        <div class="text-center font-bold text-lg uppercase mb-4">
            PERJANJIAN KINERJA TAHUN {{ $pk->tahun }}<br>
            {{ $jabatan->nama }}<br>
            DINAS KESEHATAN<br>
            PROVINSI KALIMANTAN SELATAN
        </div>

        <!-- TABEL 1: SASARAN & INDIKATOR -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">KINERJA UTAMA</th>
                    <th style="width: 45%;">INDIKATOR KINERJA</th>
                    <th style="width: 15%;">TARGET</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($pk->sasarans as $sasaran)
                    @php 
                        $rowspan = $sasaran->indikators->count() > 0 ? $sasaran->indikators->count() : 1; 
                    @endphp
                    
                    @if($sasaran->indikators->count() > 0)
                        @foreach($sasaran->indikators as $index => $ind)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ $rowspan }}" class="text-center">{{ $no++ }}.</td>
                                    <td rowspan="{{ $rowspan }}">{{ $sasaran->sasaran }}</td>
                                @endif
                                <td>{{ $ind->nama_indikator }}</td>
                                <td class="text-center">
                                    @php 
                                        $colTarget = 'target_' . $pk->tahun;
                                        $val = $ind->$colTarget ?? $ind->target; 
                                    @endphp
                                    {{ $val }} {{ $ind->satuan }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center">{{ $no++ }}.</td>
                            <td>{{ $sasaran->sasaran }}</td>
                            <td>-</td>
                            <td class="text-center">-</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- TABEL 2: ANGGARAN -->
        <table style="margin-top: 20px; border: none;">
            <thead>
                <tr>
                    <th style="border: none; text-align: left; padding-left: 0; width: 70%;">Program/Kegiatan/Sub Kegiatan</th>
                    <th style="border: none; text-align: right; width: 30%;">Anggaran</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAnggaran = 0; @endphp
                @foreach($pk->anggarans as $idx => $anggaran)
                    @php $totalAnggaran += $anggaran->anggaran; @endphp
                    <tr>
                        <td style="border: none; padding: 4px 0;">
                            {{ $idx + 1 }}. 
                            @if($anggaran->subKegiatan)
                                {{ $anggaran->subKegiatan->nama }}
                            @else
                                {{ $anggaran->nama_program_kegiatan }}
                            @endif
                        </td>
                        <td style="border: none; text-align: right; padding: 4px 0;">
                            Rp {{ number_format($anggaran->anggaran, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td style="border: none; text-align: center; padding-top: 10px;">JUMLAH</td>
                    <td style="border: none; text-align: right; padding-top: 10px; border-top: 1px solid #000;">
                        Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="signature-container">
            <!-- PIHAK KEDUA (Pejabat Yang Bersangkutan) -->
            <div class="signature-box">
                <p class="font-bold mb-4">PIHAK KEDUA,</p>
                <br><br><br>
                @if($pegawai)
                    <p class="signature-name uppercase">{{ $pegawai->nama }}</p>
                    <p class="signature-nip">NIP. {{ $pegawai->nip }}</p>
                @else
                    <p class="signature-name">(Belum Ada Pejabat)</p>
                @endif
            </div>

            <!-- PIHAK PERTAMA (Atasan) -->
            <div class="signature-box">
                <p class="font-bold">PIHAK PERTAMA,</p>
                <br><br><br>
                @if($is_kepala_dinas)
                    <p class="signature-name uppercase">H. MUHIDIN</p>
                    <!-- Gubernur biasanya tidak pakai NIP di dokumen seperti ini, atau bisa dikosongkan -->
                @elseif($atasan_pegawai)
                    <p class="signature-name uppercase">{{ $atasan_pegawai->nama }}</p>
                    <p class="signature-nip">NIP. {{ $atasan_pegawai->nip }}</p>
                @else
                    <p class="signature-name">(Atasan Belum Diset)</p>
                @endif
            </div>
        </div>

    </div>

</body>
</html>