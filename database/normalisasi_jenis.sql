-- OPSIONAL. Kode sudah bisa membaca data lama apa adanya (label maupun slug),
-- jadi SQL ini TIDAK wajib. Jalankan hanya jika ingin merapikan kolom `jenis` di database.
-- Backup dulu sebelum menjalankan.
UPDATE dokumens SET jenis = 'bom-meeting'     WHERE jenis = 'BOM Meeting';
UPDATE dokumens SET jenis = 'surat-keputusan' WHERE jenis = 'Surat Keputusan';
UPDATE dokumens SET jenis = 'legal'           WHERE jenis = 'Legal';
UPDATE dokumens SET jenis = 'kontrak'         WHERE jenis = 'Kontrak';
UPDATE dokumens SET jenis = 'risalah'         WHERE jenis = 'Risalah';
UPDATE dokumens SET jenis = 'sop'             WHERE jenis = 'SOP';
UPDATE dokumens SET jenis = 'perjanjian'      WHERE jenis = 'Perjanjian';
UPDATE dokumens SET jenis = 'akta'            WHERE jenis = 'Akta';
