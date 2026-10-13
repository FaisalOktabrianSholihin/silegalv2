<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role_id', 'divisi_id', 'karyawan_id', 'is_active', 'last_login_at'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'is_active' => 'boolean', 'last_login_at' => 'datetime'];
    }

    public function role()     { return $this->belongsTo(Role::class); }
    public function divisi()   { return $this->belongsTo(Divisi::class, 'divisi_id'); }
    public function karyawan() { return $this->belongsTo(Karyawan::class, 'karyawan_id'); }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super-admin';
    }

    public function bisa(string $izin): bool
    {
        if (! $this->role) return false;
        if ($this->isSuperAdmin()) return true;
        return in_array($izin, $this->role->izin ?? [], true);
    }

    /** true = boleh melihat dokumen semua divisi */
    public function lihatSemuaDivisi(): bool
    {
        return $this->isSuperAdmin() || (bool) $this->role?->akses_semua_divisi;
    }

    public function getInisialAttribute(): string
    {
        return collect(explode(' ', trim($this->name)))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }
}
