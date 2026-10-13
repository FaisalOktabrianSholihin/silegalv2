<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('dokumens')) {
            Schema::create('dokumens', function (Blueprint $t) {
                $t->id();
                $t->string('jenis', 30)->index();          // bom-meeting, legal, kontrak, risalah, sop, perjanjian, akta, surat-keputusan
                $t->unsignedBigInteger('divisi_id')->nullable()->index(); // -> db_indukk.divisis (lintas database, tanpa FK)
                $t->string('status', 30)->default('Aktif')->index();
                $t->string('no_dokumen')->index();
                $t->string('nama_dokumen');
                $t->date('tanggal_awal')->nullable();       // untuk jenis 'single' = Tanggal Dokumen
                $t->date('tanggal_akhir')->nullable()->index();
                $t->string('lokasi')->nullable();
                $t->text('catatan')->nullable();
                $t->string('link_dokumen', 1000)->nullable();
                $t->text('rangkuman')->nullable();
                $t->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
                $t->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamps();
                $t->softDeletes();
            });
        }

        if (! Schema::hasTable('dokumen_pihaks')) {
            Schema::create('dokumen_pihaks', function (Blueprint $t) {
                $t->id();
                $t->foreignId('dokumen_id')->constrained('dokumens')->cascadeOnDelete();
                $t->unsignedSmallInteger('urutan')->default(1);
                $t->string('nama');
                $t->string('jabatan')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pihaks');
        Schema::dropIfExists('dokumens');
    }
};
