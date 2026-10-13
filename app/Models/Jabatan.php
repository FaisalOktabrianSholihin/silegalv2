<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $connection = 'indukk'; // database db_indukk (read-only)
    protected $table = 'jabatans';
    protected $fillable = ['nama_jabatan'];

    public function karyawans() { return $this->hasMany(Karyawan::class, 'jabatan_id'); }
}
