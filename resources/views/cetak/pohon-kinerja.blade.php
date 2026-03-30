<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Visualisasi Pohon Kinerja</title>
    <style>
        @page { margin: 30px 40px; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 10pt; 
            color: #1f2937; 
        }
        
        /* Kop Surat */
        .kop-surat { width: 100%; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat td { border: none; vertical-align: middle; }
        .kop-surat .logo { width: 80px; height: auto; }
        .kop-surat .judul-dokumen { text-align: center; }
        .kop-surat h1 { font-size: 15pt; font-weight: bold; margin: 0 0 5px 0; letter-spacing: 1px; }
        .kop-surat h2 { font-size: 12pt; font-weight: bold; margin: 0 0 3px 0; }
        .kop-surat h3 { font-size: 11pt; font-weight: normal; margin: 0; }
        
        /* Bagan Pohon Vertikal (CSS) */
        .tree-wrapper { margin-top: 20px; }
        
        .node-container { position: relative; margin-bottom: 10px; }
        
        /* Kotak Kinerja */
        .node-box {
            width: 85%; /* Lebar kotak memanjang agar muat text */
            border: 2px solid #2563eb; 
            border-radius: 8px; 
            background-color: #ffffff;
            page-break-inside: avoid;
        }
        
        /* Judul Jabatan (Header Kotak) */
        .node-header {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: bold;
            padding: 8px 12px;
            font-size: 11pt;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        /* Isi Kinerja dan Indikator */
        .node-body { padding: 10px 12px; }
        .kinerja-item { margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed #e5e7eb; }
        .kinerja-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .kinerja-title { font-weight: bold; color: #111827; margin-bottom: 4px; }
        
        .indikator-list { margin: 0; padding-left: 20px; font-size: 9.5pt; color: #4b5563; }
        .indikator-list li { margin-bottom: 3px; }

        /* Garis Vertikal dan Horizontal (Connecting Lines) */
        .children-container {
            border-left: 2px solid #93c5fd; /* Garis vertikal ke bawah */
            margin-left: 30px;              /* Jarak indentasi */
            padding-left: 20px;             /* Jarak dari garis ke kotak anak */
            padding-top: 10px;
        }
        
        .connector-horizontal {
            position: absolute;
            left: -22px; 
            top: 20px;   
            width: 20px; 
            border-top: 2px solid #93c5fd; /* Garis horizontal ke kotak */
        }
        
        /* Level 0 khusus (Root) */
        .root-box { border-color: #1e3a8a; }
        .root-header { background-color: #1e3a8a; }
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('logo pemprov.png') }}" class="logo" alt="Logo Pemprov">
            </td>
            <td width="70%" class="judul-dokumen">
                <h1>LAPORAN VISUALISASI POHON KINERJA</h1>
                <h2>{{ strtoupper($header['unit_kerja'] ?? 'DINAS KESEHATAN') }}</h2>
                <h3>PERIODE TAHUN {{ $header['periode'] ?? '2025 - 2029' }}</h3>
            </td>
            <td width="15%"></td>
        </tr>
    </table>

    <div class="tree-wrapper">
        {{-- FUNGSI REKURSIF RENDER BAGAN KOTAK DI DALAM BLADE --}}
        @php
            $renderTree = function($nodes, $level = 0) use (&$renderTree) {
                $html = '';
                foreach($nodes as $node) {
                    // Penentuan warna berdasarkan level (Root lebih gelap)
                    $isRoot = ($level === 0);
                    $boxClass = $isRoot ? 'node-box root-box' : 'node-box';
                    $headerClass = $isRoot ? 'node-header root-header' : 'node-header';
                    
                    // Container Node
                    $html .= '<div class="node-container">';
                    
                    // Tambahkan garis horizontal menyamping jika ini adalah anak (bukan root)
                    if(!$isRoot) {
                        $html .= '<div class="connector-horizontal"></div>';
                    }

                    // Render Kotaknya
                    $html .= '<div class="'.$boxClass.'">';
                    
                    // Header Jabatan
                    $jabatanText = $node->jabatan ?: 'Tanpa Jabatan / Jabatan Kosong';
                    $html .= '<div class="'.$headerClass.'">'.$jabatanText.'</div>';
                    
                    // Body / Kinerja & Indikator
                    $html .= '<div class="node-body">';
                    
                    if(is_array($node->kinerja_items) && count($node->kinerja_items) > 0) {
                        foreach($node->kinerja_items as $k) {
                            $html .= '<div class="kinerja-item">';
                            $html .= '<div class="kinerja-title">🎯 '.($k['kinerja_utama'] ?: 'Kinerja belum diisi').'</div>';
                            
                            // Render Indikatornya
                            if(isset($k['indikators']) && is_array($k['indikators']) && count($k['indikators']) > 0) {
                                $html .= '<ul class="indikator-list">';
                                foreach($k['indikators'] as $ind) {
                                    $nama = $ind['nama'] ?? '-';
                                    $nilai = $ind['nilai'] ?? '-';
                                    $satuan = $ind['satuan'] ?? '';
                                    $html .= '<li>Indikator: '.$nama.' <strong>(Target: '.$nilai.' '.$satuan.')</strong></li>';
                                }
                                $html .= '</ul>';
                            } else {
                                $html .= '<div style="font-size:9pt; color:#9ca3af; margin-left:20px;">Belum ada indikator</div>';
                            }
                            $html .= '</div>';
                        }
                    } else {
                        $html .= '<div style="font-size:9.5pt; color:#6b7280; font-style:italic;">Data kinerja kosong...</div>';
                    }

                    $html .= '</div>'; // End node-body
                    $html .= '</div>'; // End node-box

                    // Jika Node ini punya anak, panggil fungsi lagi (Rekursif) dan masukkan ke dalam Container Garis
                    if($node->children->count() > 0) {
                        $html .= '<div class="children-container">';
                        $html .= $renderTree($node->children, $level + 1);
                        $html .= '</div>';
                    }

                    $html .= '</div>'; // End node-container
                }
                return $html;
            };
        @endphp

        {{-- JALANKAN FUNGSINYA DI SINI --}}
        @if($tree->count() > 0)
            {!! $renderTree($tree) !!}
        @else
            <div style="text-align: center; color: #6b7280; font-style: italic; margin-top: 50px;">
                Belum ada data Visualisasi Pohon Kinerja yang dibuat.
            </div>
        @endif

    </div>

</body>
</html>