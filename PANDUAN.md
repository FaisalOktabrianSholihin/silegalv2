# SILEGAL v2: Panduan Pemasangan

Untuk Laravel 11/12 (PHP 8.2+), MySQL/MariaDB, database `db_indukk`.
Tampilan memakai Tailwind, Alpine.js, Chart.js, dan Lucide lewat CDN, jadi tidak perlu `npm install` atau build Vite.

## 1. Salin file
Backup project dulu, lalu salin isi folder ini ke root project Laravel (gabungkan folder):

- `config/dokumen.php` (baru)
- `app/Models/*` (Divisi, Jabatan, Karyawan, User, Role, Dokumen, DokumenPihak, LogAktivitas)
- `app/Support/Aktivitas.php`, `app/Http/Middleware/CekIzin.php`, `app/Http/Controllers/*`
- `routes/web.php` dan `bootstrap/app.php` (menimpa file lama; kalau `bootstrap/app.php` Anda sudah ada isian lain, cukup tambahkan baris alias `izin`)
- `resources/views/*`
- `database/migrations/*` dan `database/seeders/SilegalSeeder.php`

Catatan: `app/Models/User.php` akan menimpa model User lama.

## 2. Pengaturan dua database
Pembagian:
- **`silegal`** (koneksi default `mysql`): users, roles, dokumens, dokumen_pihaks, log_aktivitas, sessions.
- **`db_indukk`** (koneksi `indukk`): divisis, jabatans, karyawans (hanya dibaca; Divisi & Karyawan di menu Master Data bersifat read-only).

`.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=silegal
DB_USERNAME=root
DB_PASSWORD=

DB_INDUKK_DATABASE=db_indukk
# opsional jika user/host berbeda:
# DB_INDUKK_HOST=127.0.0.1
# DB_INDUKK_USERNAME=root
# DB_INDUKK_PASSWORD=

APP_LOCALE=id
SESSION_DRIVER=file
```
`config/database.php`: tambahkan koneksi `indukk` (isi ada di `config/database-indukk.snippet.php`).
`config/app.php`: ubah `'timezone' => 'Asia/Jakarta'`.

Database `silegal` dibuat kosong. `db_indukk` harus sudah berisi data dari `db_indukk.sql`. Tidak ada foreign key antar database. Kolom `divisi_id` dan `karyawan_id` di `users` / `dokumens` hanya menyimpan ID dari db_indukk.

## 3. Migration (hanya ke database silegal)
Pastikan folder `database/migrations` TIDAK berisi migration yang membuat tabel divisis/karyawans/jabatans (tabel itu milik db_indukk), dan file `0001_01_01_000000_create_users_table.php` adalah bawaan Laravel yang asli. Lalu:
```
php artisan config:clear
php artisan migrate
php artisan db:seed --class=SilegalSeeder
php artisan optimize:clear
```

## 4. Login awal
`admin@silegal.test` / `Silegal@2026`. Segera ganti passwordnya lewat menu Master Data → User.

## 5. Cara kerja pembatasan per divisi
- Setiap dokumen punya `divisi_id`.
- Role dengan "Akses semua divisi" (Super Admin, Admin Legal, Pimpinan) melihat semua dokumen.
- Role "per divisi" (Staf Divisi, Viewer Divisi) hanya melihat dokumen yang `divisi_id`-nya sama dengan divisi user.
  Contoh: user Divisi Budidaya hanya melihat Risalah (dan dokumen lain) milik Divisi Budidaya.
- Dokumen yang ditambah oleh user per divisi otomatis memakai divisinya.
- Aturan ini berlaku di daftar, detail, ubah, hapus, preview, dan dashboard (lewat `Dokumen::milikUser()`).

## 6. Catatan penting
- **Link dokumen**: isi dengan link PDF. Link Google Drive otomatis dikonversi ke mode preview. Pastikan akses file dibagikan, dan beberapa situs memblokir iframe (ada tombol "Buka di tab baru").
- **Tailwind CDN** cocok untuk pengembangan. Untuk produksi sebaiknya dipindah ke Vite/Tailwind build.
- Untuk menambah jenis dokumen baru, cukup tambah satu entri di `config/dokumen.php`.
