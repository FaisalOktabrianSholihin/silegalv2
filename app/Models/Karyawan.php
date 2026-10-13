<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $connection = 'indukk'; // database db_indukk (read-only)
    protected $table = 'karyawans';

    protected $fillable = [
        'nik', 'no_absen', 'nama', 'nama_panggilan', 'alamat', 'email', 'no_hp', 'no_ktp',
        'jabatan_id', 'divisi_id', 'status_pegawai', 'status_aktif', 'golongan',
        'tanggal_masuk', 'tanggal_pengangkatan', 'tanggal_pensiun', 'jenis_kelamin', 'tanggal_lahir',
    ];

    protected function casts(): array
    {
        return ['tanggal_masuk' => 'date', 'tanggal_lahir' => 'date', 'tanggal_pengangkatan' => 'date', 'tanggal_pensiun' => 'date'];
    }

    public function jabatan() { return $this->belongsTo(Jabatan::class, 'jabatan_id'); }
    public function divisi()  { return $this->belongsTo(Divisi::class, 'divisi_id'); }
}
