<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPihak extends Model
{
    protected $table = 'dokumen_pihaks';
    protected $fillable = ['dokumen_id', 'urutan', 'nama', 'jabatan'];

    public function dokumen() { return $this->belongsTo(Dokumen::class); }
}
