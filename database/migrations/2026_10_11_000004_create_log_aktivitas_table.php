<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('log_aktivitas')) return;

        Schema::create('log_aktivitas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('aksi', 30)->index();    // login, logout, tambah, ubah, hapus, lihat, preview, login_gagal
            $t->string('modul', 60)->index();
            $t->text('deskripsi');
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void { Schema::dropIfExists('log_aktivitas'); }
};
