<?php

namespace App\Traits;

use App\Models\TopsisKriteria;
use Illuminate\Support\Str;

trait TopsisCalculator
{
    protected function calculateTopsisCharts($rawPerformance)
    {
        $jabatanData = [];
        
        foreach ($rawPerformance as $row) {
            $target = (float) str_replace(',', '.', $row->target_tahun);
            $realisasi = (float) str_replace(',', '.', $row->realisasi);
            $arah = strtolower(trim($row->arah ?? ''));
            $isNegative = in_array($arah, ['menurun', 'turun', 'negative', 'negatif', 'min']);

            if ($target > 0) {
                if ($isNegative) {
                    $rawCapaian = ((2 * $target) - $realisasi) / $target * 100;
                } else {
                    $rawCapaian = ($realisasi / $target) * 100;
                }
                
                $cappedCapaian = $rawCapaian > 100 ? 100 : ($rawCapaian < 0 ? 0 : $rawCapaian);

                if (!isset($jabatanData[$row->jabatan])) {
                    $jabatanData[$row->jabatan] = [
                        'total_capaian' => 0,
                        'count' => 0,
                        'isu_kritis' => 0,
                        'tercapai' => 0,
                        'min_capaian' => 100
                    ];
                }

                $jabatanData[$row->jabatan]['total_capaian'] += $cappedCapaian;
                $jabatanData[$row->jabatan]['count']++;
                
                if ($cappedCapaian < 50) $jabatanData[$row->jabatan]['isu_kritis']++;
                if ($cappedCapaian >= 100) $jabatanData[$row->jabatan]['tercapai']++;
                if ($cappedCapaian < $jabatanData[$row->jabatan]['min_capaian']) {
                    $jabatanData[$row->jabatan]['min_capaian'] = $cappedCapaian;
                }
            }
        }

        $matrixX = [];
        foreach ($jabatanData as $jabatan => $data) {
            if ($data['count'] > 0) {
                $matrixX[$jabatan] = [
                    'C1' => $data['total_capaian'] / $data['count'],
                    'C2' => $data['isu_kritis'],
                    'C3' => $data['tercapai'],
                    'C4' => $data['min_capaian'] == 100 && $data['count'] == 0 ? 0 : $data['min_capaian']
                ];
            }
        }

        $sumSq = ['C1' => 0, 'C2' => 0, 'C3' => 0, 'C4' => 0];
        foreach($matrixX as $vals) {
            $sumSq['C1'] += pow($vals['C1'], 2);
            $sumSq['C2'] += pow($vals['C2'], 2);
            $sumSq['C3'] += pow($vals['C3'], 2);
            $sumSq['C4'] += pow($vals['C4'], 2);
        }

        $matrixR = [];
        foreach($matrixX as $jab => $vals) {
            $matrixR[$jab] = [
                'C1' => $sumSq['C1'] > 0 ? $vals['C1'] / sqrt($sumSq['C1']) : 0,
                'C2' => $sumSq['C2'] > 0 ? $vals['C2'] / sqrt($sumSq['C2']) : 0,
                'C3' => $sumSq['C3'] > 0 ? $vals['C3'] / sqrt($sumSq['C3']) : 0,
                'C4' => $sumSq['C4'] > 0 ? $vals['C4'] / sqrt($sumSq['C4']) : 0,
            ];
        }

        $dbKriteria = \App\Models\TopsisKriteria::all()->keyBy('kode');
        $weights = [
            'C1' => $dbKriteria->has('C1') ? $dbKriteria['C1']->bobot : 0.25,
            'C2' => $dbKriteria->has('C2') ? $dbKriteria['C2']->bobot : 0.25,
            'C3' => $dbKriteria->has('C3') ? $dbKriteria['C3']->bobot : 0.25,
            'C4' => $dbKriteria->has('C4') ? $dbKriteria['C4']->bobot : 0.25,
        ];
        $types = [
            'C1' => $dbKriteria->has('C1') ? $dbKriteria['C1']->tipe : 'benefit',
            'C2' => $dbKriteria->has('C2') ? $dbKriteria['C2']->tipe : 'cost',
            'C3' => $dbKriteria->has('C3') ? $dbKriteria['C3']->tipe : 'benefit',
            'C4' => $dbKriteria->has('C4') ? $dbKriteria['C4']->tipe : 'benefit',
        ];

        $matrixV = [];
        foreach($matrixR as $jab => $vals) {
            $matrixV[$jab] = [
                'C1' => $vals['C1'] * $weights['C1'],
                'C2' => $vals['C2'] * $weights['C2'],
                'C3' => $vals['C3'] * $weights['C3'],
                'C4' => $vals['C4'] * $weights['C4'],
            ];
        }

        $A_plus = [];
        $A_min = [];
        
        foreach(['C1', 'C2', 'C3', 'C4'] as $kode) {
             $A_plus[$kode] = $types[$kode] == 'cost' ? 99999 : -99999;
             $A_min[$kode] = $types[$kode] == 'cost' ? -99999 : 99999;
        }

        if (count($matrixV) > 0) {
            foreach($matrixV as $vals) {
                foreach(['C1', 'C2', 'C3', 'C4'] as $kode) {
                     if ($types[$kode] == 'cost') {
                         $A_plus[$kode] = min($A_plus[$kode], $vals[$kode]);
                         $A_min[$kode] = max($A_min[$kode], $vals[$kode]);
                     } else {
                         $A_plus[$kode] = max($A_plus[$kode], $vals[$kode]);
                         $A_min[$kode] = min($A_min[$kode], $vals[$kode]);
                     }
                }
            }
        }

        $topsisResult = [];
        foreach($matrixV as $jab => $vals) {
            $D_plus = sqrt(
                pow($vals['C1'] - $A_plus['C1'], 2) +
                pow($vals['C2'] - $A_plus['C2'], 2) +
                pow($vals['C3'] - $A_plus['C3'], 2) +
                pow($vals['C4'] - $A_plus['C4'], 2)
            );
            
            $D_min = sqrt(
                pow($vals['C1'] - $A_min['C1'], 2) +
                pow($vals['C2'] - $A_min['C2'], 2) +
                pow($vals['C3'] - $A_min['C3'], 2) +
                pow($vals['C4'] - $A_min['C4'], 2)
            );
            
            $Ci = ($D_plus + $D_min) > 0 ? $D_min / ($D_plus + $D_min) : 0;
            
            $topsisResult[] = [
                'jabatan' => $jab,
                'Ci' => round($Ci, 4),
                'X' => $matrixX[$jab]
            ];
        }

        usort($topsisResult, function($a, $b) {
            return $b['Ci'] <=> $a['Ci'];
        });

        $charts = [
            'bar_bidang' => ['labels' => [], 'data' => []],
            'pie_predikat' => ['labels' => ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'], 'data' => [0, 0, 0, 0]],
            'radar_kriteria' => ['labels' => ['Rata-rata Capaian', 'Isu Kritis', 'Tercapai', 'Stabilitas'], 'series' => []],
            'heatmap_matrix' => ['series' => []],
            'gauge_indeks' => 0,
            'leaderboard' => []
        ];

        $totalCi = 0;
        foreach($topsisResult as $idx => $res) {
            $jab = $res['jabatan'];
            $ci = $res['Ci'];
            
            if ($idx < 10) {
                $charts['bar_bidang']['labels'][] = Str::limit($jab, 15);
                $charts['bar_bidang']['data'][] = $ci;
                
                $charts['heatmap_matrix']['series'][] = [
                    'name' => Str::limit($jab, 15),
                    'data' => [
                        ['x' => 'C1 (Capaian)', 'y' => round($res['X']['C1'], 1)],
                        ['x' => 'C2 (Isu)', 'y' => $res['X']['C2']],
                        ['x' => 'C3 (Tercapai)', 'y' => $res['X']['C3']],
                        ['x' => 'C4 (Stabil)', 'y' => round($res['X']['C4'], 1)],
                    ]
                ];
            }
            
            if ($ci > 0.8) $charts['pie_predikat']['data'][0]++;
            elseif ($ci > 0.6) $charts['pie_predikat']['data'][1]++;
            elseif ($ci > 0.4) $charts['pie_predikat']['data'][2]++;
            else $charts['pie_predikat']['data'][3]++;
            
            $totalCi += $ci;
        }

        if (count($topsisResult) > 0) {
            $charts['gauge_indeks'] = round(($totalCi / count($topsisResult)) * 100, 1);
            
            $topX = $topsisResult[0]['X'];
            $avgX = [
                'C1' => collect($topsisResult)->avg('X.C1'),
                'C2' => collect($topsisResult)->avg('X.C2'),
                'C3' => collect($topsisResult)->avg('X.C3'),
                'C4' => collect($topsisResult)->avg('X.C4'),
            ];
            
            $charts['radar_kriteria']['series'] = [
                [
                    'name' => 'Top: ' . Str::limit($topsisResult[0]['jabatan'], 10),
                    'data' => [round($topX['C1'], 1), round($topX['C2'], 1), round($topX['C3'], 1), round($topX['C4'], 1)]
                ],
                [
                    'name' => 'Rata-rata',
                    'data' => [round($avgX['C1'], 1), round($avgX['C2'], 1), round($avgX['C3'], 1), round($avgX['C4'], 1)]
                ]
            ];
            
            $charts['leaderboard'] = array_slice($topsisResult, 0, 5);
        }

        return $charts;
    }
}
