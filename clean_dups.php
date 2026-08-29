<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

// Keep the latest record, delete the older duplicates
DB::statement('
    DELETE t1 FROM perjanjian_kinerjas t1
    INNER JOIN perjanjian_kinerjas t2 
    WHERE t1.id < t2.id 
    AND t1.jabatan_id = t2.jabatan_id 
    AND t1.tahun = t2.tahun 
    AND t1.bulan = t2.bulan
');
echo "Duplicates cleaned!\n";
