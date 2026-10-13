<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('email')->unique();
                $t->timestamp('email_verified_at')->nullable();
                $t->string('password');
                $t->rememberToken();
                $t->timestamps();
            });
        }

        Schema::table('users', function (Blueprint $t) {
            if (! Schema::hasColumn('users', 'role_id'))      $t->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            // divisi_id & karyawan_id menunjuk ke database db_indukk (koneksi 'indukk') -> tanpa foreign key
            if (! Schema::hasColumn('users', 'divisi_id'))    $t->unsignedBigInteger('divisi_id')->nullable()->index();
            if (! Schema::hasColumn('users', 'karyawan_id'))  $t->unsignedBigInteger('karyawan_id')->nullable()->index();
            if (! Schema::hasColumn('users', 'is_active'))    $t->boolean('is_active')->default(true);
            if (! Schema::hasColumn('users', 'last_login_at')) $t->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users', 'role_id')) { $t->dropForeign(['role_id']); $t->dropColumn('role_id'); }
            foreach (['divisi_id', 'karyawan_id', 'is_active', 'last_login_at'] as $c) {
                if (Schema::hasColumn('users', $c)) $t->dropColumn($c);
            }
        });
    }
};
