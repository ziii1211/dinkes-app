<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    // Fungsi ini akan otomatis jalan saat Model yang pakai Trait ini di-boot
    public static function bootLogsActivity()
    {
        // 1. Saat Data Dibuat (CREATED)
        static::created(function ($model) {
            self::recordLog($model, 'CREATE', null, $model->toArray());
        });

        // 2. Saat Data Diubah (UPDATED)
        static::updated(function ($model) {
            // Ambil hanya data yang berubah
            $changes = $model->getChanges();
            $original = $model->getOriginal();
            
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $value) {
                // Abaikan timestamp updated_at
                if ($key !== 'updated_at') {
                    $oldValues[$key] = $original[$key] ?? null;
                    $newValues[$key] = $value;
                }
            }

            if (!empty($newValues)) {
                self::recordLog($model, 'UPDATE', $oldValues, $newValues);
            }
        });

        // 3. Saat Data Dihapus (DELETED)
        static::deleted(function ($model) {
            self::recordLog($model, 'DELETE', $model->toArray(), null);
        });
    }

    // Fungsi Pembantu untuk Simpan ke Database
    protected static function recordLog($model, $action, $oldValues = null, $newValues = null)
    {
        // Cek apakah ada user login (kalau via seeder/console mungkin null)
        $user = Auth::user();

        ActivityLog::create([
            'user_id'    => $user ? $user->id : null,
            'user_name'  => $user ? $user->name : 'System',
            'action'     => $action,
            'model_type' => get_class($model), // Contoh: App\Models\PerjanjianKinerja
            'model_id'   => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}