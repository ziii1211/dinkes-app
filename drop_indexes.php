<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$indexes = [
    'tujuans' => 'tujuans_unique_sasaran_tujuan',
    'sasarans' => 'sasarans_unique_tujuan_sasaran',
    'outcomes' => 'outcomes_unique_sasaran_outcome',
    'programs' => ['programs_unique_kode', 'programs_unique_nama'],
    'kegiatans' => ['kegiatans_unique_program_kode', 'kegiatans_unique_program_nama'],
    'sub_kegiatans' => ['subkegiatans_unique_keg_kode', 'subkegiatans_unique_keg_nama'],
];

foreach ($indexes as $table => $idx) {
    if (is_array($idx)) {
        foreach ($idx as $i) {
            try { DB::statement("ALTER TABLE {$table} DROP INDEX {$i}"); } catch (\Exception $e) {}
        }
    } else {
        try { DB::statement("ALTER TABLE {$table} DROP INDEX {$idx}"); } catch (\Exception $e) {}
    }
}
echo "Dropped partial indexes.\n";
