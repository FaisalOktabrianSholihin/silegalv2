<?php

$modul = [
    'dokumen' => ['label' => 'Dokumen', 'ket' => 'Semua jenis dokumen (Kontrak, Perizinan, dst.)', 'icon' => 'folders',
                  'aksi' => ['lihat', 'tambah', 'ubah', 'hapus'], 'terkunci' => ['lihat']],
    'master'  => ['label' => 'Divisi & Karyawan', 'ket' => 'Master Data bawaan (hanya baca)', 'icon' => 'network',
                  'aksi' => ['lihat'], 'terkunci' => []],
    'user'    => ['label' => 'User', 'ket' => 'Akun pengguna aplikasi', 'icon' => 'user-cog',
                  'aksi' => ['lihat', 'tambah', 'ubah', 'hapus'], 'terkunci' => []],
    'jenis'   => ['label' => 'Jenis Dokumen', 'ket' => 'Pengaturan jenis & menu dokumen', 'icon' => 'layers',
                  'aksi' => ['lihat', 'tambah', 'ubah', 'hapus'], 'terkunci' => []],
    'role'    => ['label' => 'Roles', 'ket' => 'Hak akses & cakupan divisi', 'icon' => 'shield-check',
                  'aksi' => ['lihat', 'tambah', 'ubah', 'hapus'], 'terkunci' => []],
    'log'     => ['label' => 'Log Aktivitas', 'ket' => 'Riwayat aktivitas seluruh user', 'icon' => 'history',
                  'aksi' => ['lihat'], 'terkunci' => []],
];

return [

    // JENIS BAWAAN (hanya dipakai sebagai data awal saat migrasi / cadangan jika tabel belum ada).
    // Jenis dokumen sekarang dikelola dari menu Master Data > Jenis Dokumen (tabel jenis_dokumens).
    // tanggal: 'single' => 1 kolom "Tanggal Dokumen" | 'range' => Tanggal Awal & Tanggal Akhir
    'jenis' => [
        'bom-meeting'     => ['label' => 'BOM Meeting',     'icon' => 'users',          'warna' => 'brand',   'tanggal' => 'single', 'pihak' => false],
        'legal'           => ['label' => 'Legal',           'icon' => 'scale',          'warna' => 'green',   'tanggal' => 'range',  'pihak' => false],
        'kontrak'         => ['label' => 'Kontrak',         'icon' => 'file-signature', 'warna' => 'emerald', 'tanggal' => 'range',  'pihak' => true],
        'risalah'         => ['label' => 'Risalah',         'icon' => 'clipboard-list', 'warna' => 'teal',    'tanggal' => 'single', 'pihak' => false],
        'sop'             => ['label' => 'SOP',             'icon' => 'file-check-2',   'warna' => 'lime',    'tanggal' => 'range',  'pihak' => false],
        'perjanjian'      => ['label' => 'Perjanjian',      'icon' => 'handshake',      'warna' => 'amber',   'tanggal' => 'range',  'pihak' => true],
        'akta'            => ['label' => 'Akta',            'icon' => 'stamp',          'warna' => 'sage',    'tanggal' => 'range',  'pihak' => false],
        'surat-keputusan' => ['label' => 'Surat Keputusan', 'icon' => 'gavel',          'warna' => 'olive',   'tanggal' => 'range',  'pihak' => false],
    ],

    'status' => ['Draft', 'Aktif', 'Selesai', 'Kadaluarsa', 'Dibatalkan'],

    // Status yang TIDAK berlaku untuk jenis dokumen bertanggal tunggal (tidak punya masa berlaku)
    'status_tanpa_masa_berlaku' => ['Kadaluarsa'],

    // Pilihan tampilan saat membuat jenis dokumen baru
    'warna_hex' => [
        'brand' => '#137a45', 'green' => '#22c55e', 'emerald' => '#10b981', 'teal' => '#14b8a6',
        'lime' => '#84cc16', 'amber' => '#f59e0b', 'sage' => '#5f8f76', 'olive' => '#889340',
        'rose' => '#f43f5e', 'orange' => '#f97316', 'sky' => '#0ea5e9', 'cyan' => '#06b6d4',
        'indigo' => '#6366f1', 'violet' => '#8b5cf6', 'pink' => '#ec4899',
    ],
    'icon_pilihan' => [
        'file-text', 'users', 'scale', 'file-signature', 'clipboard-list', 'file-check-2', 'handshake', 'stamp',
        'gavel', 'briefcase', 'building-2', 'landmark', 'shield-check', 'receipt', 'mail', 'folder',
        'award', 'scroll-text', 'file-spreadsheet', 'clipboard-check', 'badge-check', 'book-open', 'file-key', 'file-badge',
    ],

    // Dokumen dengan tanggal akhir <= N hari dianggap "Segera Berakhir"
    'warning_days' => 30,

    // Matriks izin di menu Roles: tiap modul punya aksi Lihat / Tambah / Ubah / Hapus.
    // 'terkunci' = aksi yang selalu aktif untuk semua role (tidak disimpan, tidak bisa dimatikan).
    // Kunci izin tersimpan berbentuk "modul.aksi", mis. user.tambah, dokumen.ubah.
    'modul' => $modul,

    // Daftar datar semua kunci izin (dihitung dari 'modul'): ['user.tambah' => 'Tambah User', ...]
    'izin' => (function () use ($modul) {
        $out = [];
        foreach ($modul as $k => $m) {
            foreach ($m['aksi'] as $x) {
                if (! in_array($x, $m['terkunci'])) $out["$k.$x"] = ucfirst($x) . ' ' . $m['label'];
            }
        }
        return $out;
    })(),
];
