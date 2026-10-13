<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SilegalSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nama' => 'Super Admin', 'slug' => 'super-admin', 'akses_semua_divisi' => true,
             'deskripsi' => 'Akses penuh ke seluruh fitur dan seluruh divisi.', 'izin' => array_keys(config('dokumen.izin'))],
            ['nama' => 'Admin Legal', 'slug' => 'admin-legal', 'akses_semua_divisi' => true,
             'deskripsi' => 'Mengelola seluruh dokumen lintas divisi.', 'izin' => ['dokumen.tambah', 'dokumen.ubah', 'dokumen.hapus', 'log.lihat']],
            ['nama' => 'Pimpinan', 'slug' => 'pimpinan', 'akses_semua_divisi' => true,
             'deskripsi' => 'Melihat seluruh dokumen lintas divisi (hanya baca).', 'izin' => []],
            ['nama' => 'Staf Divisi', 'slug' => 'staf-divisi', 'akses_semua_divisi' => false,
             'deskripsi' => 'Hanya melihat & mengelola dokumen milik divisinya sendiri.', 'izin' => ['dokumen.tambah', 'dokumen.ubah']],
            ['nama' => 'Viewer Divisi', 'slug' => 'viewer-divisi', 'akses_semua_divisi' => false,
             'deskripsi' => 'Hanya melihat dokumen milik divisinya sendiri.', 'izin' => []],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['slug' => $r['slug']], $r);
        }

        User::updateOrCreate(['email' => 'administrator@gmail.com'], [
            'name'      => 'Administrator',
            'password'  => 'password',   // GANTI setelah login pertama
            'role_id'   => Role::where('slug', 'super-admin')->value('id'),
            'is_active' => true,
        ]);
    }
}
