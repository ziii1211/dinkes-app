<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Perjanjian Kinerja</title>
    <style>
        /* SETUP HALAMAN CETAK */
        @page { 
            size: A4 portrait; 
            margin: 2cm 2.5cm; /* Atas-Bawah 2cm, Kiri-Kanan 2.5cm */
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11pt; 
            color: #000; 
            background: #fff; 
            margin: 0; 
            padding: 0;
            line-height: 1.3;
        }

        /* UTILITIES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .no-print { display: none; }

        /* HEADER SURAT */
        .header-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            text-transform: uppercase;
            line-height: 1.5;
        }

        /* TABEL UTAMA (SASARAN) */
        .table-main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-main th, 
        .table-main td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .table-main th {
            background-color: #eee; /* Sedikit abu saat di layar, putih saat print biasanya */
            text-align: center;
            font-weight: bold;
        }

        /* TABEL ANGGARAN (BORDERLESS) */
        .table-budget {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 40px;
        }
        .table-budget td {
            padding: 4px 5px;
            vertical-align: top;
            border: none; /* Tidak ada garis */
        }
        .border-top-black {
            border-top: 1px solid #000 !important;
        }

        /* TANDA TANGAN (MENGGUNAKAN TABEL AGAR RAPI) */
        .table-signature {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            page-break-inside: avoid; /* Jangan terpotong halaman */
        }
        .table-signature td {
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }
        .sign-space {
            height: 80px; /* Ruang untuk tanda tangan */
        }
        .name-underline {
            font-weight: bold;
            text-decoration: underline;
        }

        /* TOOLBAR TOMBOL */
        .toolbar {
            position: fixed; 
            top: 0; left: 0; right: 0; 
            background: #333; 
            padding: 15px; 
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .btn {
            background: #007bff; color: white; border: none; padding: 10px 20px; 
            cursor: pointer; border-radius: 5px; font-family: sans-serif; 
            text-decoration: none; display: inline-block; font-weight: bold;
            font-size: 14px;
        }
        .btn:hover { background: #0056b3; }
        .btn-back { background: #6c757d; margin-right: 10px; }
        .btn-back:hover { background: #5a6268; }

        /* PRINT MEDIA QUERY */
        @media print {
            .toolbar { display: none !important; }
            body { padding: 0; background-color: #fff; }
            .table-main th { background-color: transparent !important; }
        }
    </style>
</head>
<body>

    <div class="toolbar no-print">
        <a href="javascript:history.back()" class="btn btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn">🖨️ Cetak Dokumen</button>
    </div>

    <div style="margin-top: 50px;"> <div class="header-title">
            PERJANJIAN KINERJA TAHUN {{ $pk->tahun }}<br>
            {{ $jabatan->nama }}<br>
            DINAS KESEHATAN<br>
            PROVINSI KALIMANTAN SELATAN
        </div>

        <table class="table-main">
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
                        $countIndikator = $sasaran->indikators->count();
                        $rowspan = $countIndikator > 0 ? $countIndikator : 1; 
                    @endphp
                    
                    @if($countIndikator > 0)
                        @foreach($sasaran->indikators as $index => $ind)
                            <tr>
                                {{-- Kolom No & Sasaran hanya muncul di baris pertama (Rowspan) --}}
                                @if($index === 0)
                                    <td rowspan="{{ $rowspan }}" class="text-center">{{ $no++ }}.</td>
                                    <td rowspan="{{ $rowspan }}">{{ $sasaran->sasaran }}</td>
                                @endif

                                {{-- Indikator & Target --}}
                                <td style="padding-left: 10px;">{{ $ind->nama_indikator }}</td>
                                <td class="text-center">
                                    @php 
                                        $colTarget = 'target_' . $pk->tahun;
                                        $val = $ind->$colTarget ?? $ind->target; 
                                        // Bersihkan .00 jika desimalnya 0
                                        $val = (float)$val == (int)$val ? (int)$val : $val;
                                    @endphp
                                    {{ $val }} {{ $ind->satuan }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- Jika tidak ada indikator --}}
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

        <table class="table-budget">
            <thead>
                <tr>
                    <th style="text-align: left; width: 70%; border:none; padding-bottom: 10px;">Program/Kegiatan/Sub Kegiatan</th>
                    <th style="text-align: right; width: 30%; border:none; padding-bottom: 10px;">Anggaran</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAnggaran = 0; @endphp
                @foreach($pk->anggarans as $idx => $anggaran)
                    @php $totalAnggaran += $anggaran->anggaran; @endphp
                    <tr>
                        <td>
                            {{ $idx + 1 }}. 
                            @if($anggaran->subKegiatan)
                                {{ $anggaran->subKegiatan->nama }}
                            @else
                                {{-- Regex Clean: Hapus angka di depan nama program agar rapi --}}
                                {{ preg_replace('/^[\d\.]+\s*/', '', $anggaran->nama_program_kegiatan) }}
                            @endif
                        </td>
                        <td class="text-right">
                            Rp {{ number_format($anggaran->anggaran, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                
                <tr style="font-weight: bold;">
                    <td class="text-center" style="padding-top: 10px;">JUMLAH</td>
                    <td class="text-right border-top-black" style="padding-top: 10px;">
                        Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table-signature">
            <tr>
                <td style="width: 50%;">
                    <p class="font-bold">PIHAK KEDUA,</p>
                    <div class="sign-space"></div> @if($pegawai)
                        <p class="name-underline uppercase">{{ $pegawai->nama }}</p>
                        <p>NIP. {{ $pegawai->nip }}</p>
                    @else
                        <p class="name-underline">(Belum Ada Pejabat)</p>
                        <p>NIP. -</p>
                    @endif
                </td>

                <td style="width: 50%;">
                    <p class="font-bold">PIHAK PERTAMA,</p>
                    <div class="sign-space"></div> @if($is_kepala_dinas)
                        {{-- Jika Jabatan Kepala Dinas, Pihak Pertama biasanya Gubernur/Sekda --}}
                        <p class="name-underline uppercase">H. MUHIDIN</p>
                        {{-- <p>NIP. ...</p> (Opsional jika Gubernur) --}}
                    @elseif($atasan_pegawai)
                        <p class="name-underline uppercase">{{ $atasan_pegawai->nama }}</p>
                        <p>NIP. {{ $atasan_pegawai->nip }}</p>
                    @else
                        <p class="name-underline">(Atasan Belum Diset)</p>
                        <p>NIP. -</p>
                    @endif
                </td>
            </tr>
        </table>

    </div>

</body>
</html>