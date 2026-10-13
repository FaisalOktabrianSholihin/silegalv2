<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('roles')) return;

        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('slug')->unique();
            $t->text('deskripsi')->nullable();
            $t->boolean('akses_semua_divisi')->default(false); // false = hanya dokumen divisinya sendiri
            $t->json('izin')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('roles'); }
};
