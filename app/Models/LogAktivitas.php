<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    public $timestamps = false;
    protected $fillable = ['user_id', 'aksi', 'modul', 'deskripsi', 'subject_id', 'ip_address', 'user_agent'];

    protected function casts(): array { return ['created_at' => 'datetime']; }

    public function user() { return $this->belongsTo(User::class); }
}
