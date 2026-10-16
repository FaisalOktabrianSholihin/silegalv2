<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Mengubah izin lama "xxx.kelola" menjadi izin terperinci (lihat/tambah/ubah/hapus).
return new class extends Migration {
    private array $peta = [
        'master.kelola' => ['master.lihat'],
        'user.kelola'   => ['user.lihat', 'user.tambah', 'user.ubah', 'user.hapus'],
        'jenis.kelola'  => ['jenis.lihat', 'jenis.tambah', 'jenis.ubah', 'jenis.hapus'],
        'role.kelola'   => ['role.lihat', 'role.tambah', 'role.ubah', 'role.hapus'],
    ];

    public function up(): void
    {
        foreach (DB::table('roles')->get() as $r) {
            $izin = json_decode($r->izin ?? '[]', true) ?: [];
            $baru = [];
            foreach ($izin as $i) array_push($baru, ...($this->peta[$i] ?? [$i]));
            if ($r->slug === 'super-admin') $baru = array_keys(config('dokumen.izin'));
            DB::table('roles')->where('id', $r->id)->update(['izin' => json_encode(array_values(array_unique($baru)))]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('roles')->get() as $r) {
            $izin = json_decode($r->izin ?? '[]', true) ?: [];
            $lama = [];
            foreach ($izin as $i) {
                $lama[] = match ($i) {
                    'master.lihat' => 'master.kelola', 'user.lihat' => 'user.kelola', 'jenis.lihat' => 'jenis.kelola', 'role.lihat' => 'role.kelola',
                    default => (preg_match('/^(user|jenis|role|master)\./', $i) ? null : $i),
                };
            }
            DB::table('roles')->where('id', $r->id)->update(['izin' => json_encode(array_values(array_unique(array_filter($lama))))]);
        }
    }
};
