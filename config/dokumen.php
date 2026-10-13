<?php

return [

    // Jenis dokumen. Key = slug di URL & kolom `jenis` di tabel dokumens.
    // tanggal: 'single' => 1 kolom "Tanggal Dokumen" | 'range' => Tanggal Awal & Tanggal Akhir
    'jenis' => [
        'bom-meeting'     => ['label' => 'BOM Meeting',     'icon' => 'users',          'warna' => 'indigo',  'tanggal' => 'single', 'pihak' => false],
        'legal'           => ['label' => 'Legal',           'icon' => 'scale',          'warna' => 'violet',  'tanggal' => 'range',  'pihak' => false],
        'kontrak'         => ['label' => 'Kontrak',         'icon' => 'file-signature', 'warna' => 'sky',     'tanggal' => 'range',  'pihak' => true],
        'risalah'         => ['label' => 'Risalah',         'icon' => 'clipboard-list', 'warna' => 'teal',    'tanggal' => 'single', 'pihak' => false],
        'sop'             => ['label' => 'SOP',             'icon' => 'file-check-2',   'warna' => 'emerald', 'tanggal' => 'range',  'pihak' => false],
        'perjanjian'      => ['label' => 'Perjanjian',      'icon' => 'handshake',      'warna' => 'amber',   'tanggal' => 'range',  'pihak' => true],
        'akta'            => ['label' => 'Akta',            'icon' => 'stamp',          'warna' => 'rose',    'tanggal' => 'range',  'pihak' => false],
        'surat-keputusan' => ['label' => 'Surat Keputusan', 'icon' => 'gavel',          'warna' => 'fuchsia', 'tanggal' => 'range',  'pihak' => false],
    ],

    'status' => ['Draft', 'Aktif', 'Selesai', 'Kadaluarsa', 'Dibatalkan'],

    // Dokumen dengan tanggal akhir <= N hari dianggap "Segera Berakhir"
    'warning_days' => 30,

    // Daftar izin yang bisa dicentang di menu Roles
    'izin' => [
        'dokumen.tambah' => 'Tambah dokumen',
        'dokumen.ubah'   => 'Ubah dokumen',
        'dokumen.hapus'  => 'Hapus dokumen',
        'master.kelola'  => 'Kelola Master Data (Divisi & Karyawan)',
        'user.kelola'    => 'Kelola User',
        'role.kelola'    => 'Kelola Roles',
        'log.lihat'      => 'Lihat Log Aktivitas (semua user)',
    ],
];
