<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('jenis_dokumens')) {
            Schema::create('jenis_dokumens', function (Blueprint $t) {
                $t->id();
                $t->string('slug', 30)->unique();          // dipakai di URL & kolom dokumens.jenis (tidak diubah setelah dibuat)
                $t->string('label', 60)->unique();
                $t->string('icon', 40)->default('file-text');
                $t->string('warna', 20)->default('brand');
                $t->string('tanggal', 10)->default('single'); // single | range
                $t->boolean('pakai_pihak')->default(false);
                $t->boolean('pakai_lokasi')->default(true);
                $t->boolean('pakai_catatan')->default(true);
                $t->boolean('pakai_link')->default(true);
                $t->boolean('pakai_rangkuman')->default(true);
                $t->boolean('aktif')->default(true);
                $t->unsignedSmallInteger('urutan')->default(0);
                $t->timestamps();
            });
        }

        // Data awal dari jenis bawaan (hanya jika tabel masih kosong)
        if (DB::table('jenis_dokumens')->count() === 0) {
            $i = 1;
            foreach (config('dokumen.jenis') as $slug => $c) {
                DB::table('jenis_dokumens')->insert([
                    'slug' => $slug, 'label' => $c['label'], 'icon' => $c['icon'], 'warna' => $c['warna'],
                    'tanggal' => $c['tanggal'], 'pakai_pihak' => (bool) $c['pihak'],
                    'pakai_lokasi' => true, 'pakai_catatan' => true, 'pakai_link' => true, 'pakai_rangkuman' => true,
                    'aktif' => true, 'urutan' => $i++, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // Rapikan data lama: kolom dokumens.jenis yang berisi LABEL ("BOM Meeting") diubah jadi SLUG ("bom-meeting")
        if (Schema::hasTable('dokumens')) {
            foreach (DB::table('jenis_dokumens')->get() as $j) {
                // BINARY: MySQL biasanya tidak membedakan huruf besar/kecil, jadi "Akta" dianggap sama dengan "akta"
                $q = DB::table('dokumens');
                DB::getDriverName() === 'mysql' ? $q->whereRaw('BINARY jenis = ?', [$j->label]) : $q->where('jenis', $j->label);
                $q->update(['jenis' => $j->slug]);
            }
            // Jenis bertanggal tunggal tidak punya masa berlaku -> tidak boleh berstatus Kadaluarsa
            $single = DB::table('jenis_dokumens')->where('tanggal', 'single')->pluck('slug')->all();
            if ($single) {
                DB::table('dokumens')->whereIn('jenis', $single)->where('status', 'Kadaluarsa')->update(['status' => 'Aktif']);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_dokumens');
    }
};
