<?php

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

    // Daftar izin yang bisa dicentang di menu Roles
    'izin' => [
        'dokumen.tambah' => 'Tambah dokumen',
        'dokumen.ubah'   => 'Ubah dokumen',
        'dokumen.hapus'  => 'Hapus dokumen',
        'master.kelola'  => 'Kelola Master Data (Divisi & Karyawan)',
        'user.kelola'    => 'Kelola User',
        'role.kelola'    => 'Kelola Roles',
        'jenis.kelola'   => 'Kelola Jenis Dokumen',
        'log.lihat'      => 'Lihat Log Aktivitas (semua user)',
    ],
];
