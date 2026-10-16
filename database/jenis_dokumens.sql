-- ALTERNATIF migrasi lewat phpMyAdmin (jika tidak memakai `php artisan migrate`).
-- Aman dijalankan berulang. Backup database dulu.

CREATE TABLE IF NOT EXISTS `jenis_dokumens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` varchar(30) NOT NULL,
  `label` varchar(60) NOT NULL,
  `icon` varchar(40) NOT NULL DEFAULT 'file-text',
  `warna` varchar(20) NOT NULL DEFAULT 'brand',
  `tanggal` varchar(10) NOT NULL DEFAULT 'single',
  `pakai_pihak` tinyint(1) NOT NULL DEFAULT 0,
  `pakai_lokasi` tinyint(1) NOT NULL DEFAULT 1,
  `pakai_catatan` tinyint(1) NOT NULL DEFAULT 1,
  `pakai_link` tinyint(1) NOT NULL DEFAULT 1,
  `pakai_rangkuman` tinyint(1) NOT NULL DEFAULT 1,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_dokumens_slug_unique` (`slug`),
  UNIQUE KEY `jenis_dokumens_label_unique` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `jenis_dokumens` (`slug`,`label`,`icon`,`warna`,`tanggal`,`pakai_pihak`,`urutan`,`created_at`,`updated_at`) VALUES
('bom-meeting','BOM Meeting','users','brand','single',0,1,NOW(),NOW()),
('legal','Legal','scale','green','range',0,2,NOW(),NOW()),
('kontrak','Kontrak','file-signature','emerald','range',1,3,NOW(),NOW()),
('risalah','Risalah','clipboard-list','teal','single',0,4,NOW(),NOW()),
('sop','SOP','file-check-2','lime','range',0,5,NOW(),NOW()),
('perjanjian','Perjanjian','handshake','amber','range',1,6,NOW(),NOW()),
('akta','Akta','stamp','sage','range',0,7,NOW(),NOW()),
('surat-keputusan','Surat Keputusan','gavel','olive','range',0,8,NOW(),NOW());

-- Rapikan data lama: label -> slug (BINARY agar "Akta" dan "akta" dibedakan)
UPDATE dokumens SET jenis='bom-meeting'     WHERE BINARY jenis='BOM Meeting';
UPDATE dokumens SET jenis='surat-keputusan' WHERE BINARY jenis='Surat Keputusan';
UPDATE dokumens SET jenis='legal'           WHERE BINARY jenis='Legal';
UPDATE dokumens SET jenis='kontrak'         WHERE BINARY jenis='Kontrak';
UPDATE dokumens SET jenis='risalah'         WHERE BINARY jenis='Risalah';
UPDATE dokumens SET jenis='sop'             WHERE BINARY jenis='SOP';
UPDATE dokumens SET jenis='perjanjian'      WHERE BINARY jenis='Perjanjian';
UPDATE dokumens SET jenis='akta'            WHERE BINARY jenis='Akta';

-- Jenis 1 tanggal tidak punya masa berlaku -> tidak boleh berstatus Kadaluarsa
UPDATE dokumens SET status='Aktif' WHERE jenis IN ('bom-meeting','risalah') AND status='Kadaluarsa';
