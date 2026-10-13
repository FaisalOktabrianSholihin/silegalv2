<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['nama', 'slug', 'deskripsi', 'akses_semua_divisi', 'izin'];

    protected function casts(): array
    {
        return ['izin' => 'array', 'akses_semua_divisi' => 'boolean'];
    }

    public function users() { return $this->hasMany(User::class); }
}
