<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $connection = 'indukk'; // database db_indukk (read-only)
    protected $table = 'divisis';
    protected $fillable = ['nama_divisi'];

    public function karyawans() { return $this->hasMany(Karyawan::class, 'divisi_id'); }
    public function users()     { return $this->hasMany(User::class, 'divisi_id'); }
    public function dokumens()  { return $this->hasMany(Dokumen::class, 'divisi_id'); }
}
