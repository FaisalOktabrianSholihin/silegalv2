<?php

namespace App\Support;

use App\Models\LogAktivitas;

class Aktivitas
{
    public static function catat(string $aksi, string $modul, string $deskripsi, ?int $subjectId = null): void
    {
        try {
            LogAktivitas::create([
                'user_id'    => auth()->id(),
                'aksi'       => $aksi,
                'modul'      => $modul,
                'deskripsi'  => $deskripsi,
                'subject_id' => $subjectId,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 250),
            ]);
        } catch (\Throwable $e) {
            report($e); // jangan sampai gagal log mengganggu proses utama
        }
    }
}
