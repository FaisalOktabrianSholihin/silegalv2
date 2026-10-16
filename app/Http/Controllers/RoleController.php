<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Support\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return view('roles.index', ['roles' => Role::withCount('users')->orderBy('id')->get()]);
    }

    public function create() { return view('roles.form', ['role' => new Role()]); }

    public function store(Request $r)
    {
        $data = $this->validasi($r);
        $data['slug'] = $this->slugUnik($data['nama']);
        $role = Role::create($data);
        Aktivitas::catat('tambah', 'Role', "Menambah role: {$role->nama}", $role->id);
        return redirect()->route('roles.index')->with('sukses', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role) { return view('roles.form', compact('role')); }

    public function update(Request $r, Role $role)
    {
        $data = $this->validasi($r);
        if ($role->slug === 'super-admin') { // super admin selalu penuh
            $data['izin'] = array_keys(config('dokumen.izin'));
            $data['akses_semua_divisi'] = true;
        }
        $role->update($data);
        Aktivitas::catat('ubah', 'Role', "Mengubah role: {$role->nama}", $role->id);
        return redirect()->route('roles.index')->with('sukses', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin') return back()->with('gagal', 'Role Super Admin tidak dapat dihapus.');
        if ($role->users()->exists()) return back()->with('gagal', 'Role masih dipakai oleh user.');
        $role->delete();
        Aktivitas::catat('hapus', 'Role', "Menghapus role: {$role->nama}", $role->id);
        return back()->with('sukses', 'Role berhasil dihapus.');
    }

    private function validasi(Request $r): array
    {
        $data = $r->validate([
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'izin'      => 'nullable|array',
            'izin.*'    => Rule::in(array_keys(config('dokumen.izin'))),
        ]);
        $izin = $data['izin'] ?? [];
        // Aksi tambah/ubah/hapus pada suatu modul otomatis membutuhkan izin "lihat" modul itu
        foreach (config('dokumen.modul') as $mk => $m) {
            if (! in_array('lihat', $m['aksi']) || in_array('lihat', $m['terkunci'])) continue;
            foreach ($m['aksi'] as $x) {
                if ($x !== 'lihat' && in_array("$mk.$x", $izin)) { $izin[] = "$mk.lihat"; break; }
            }
        }
        $data['izin'] = array_values(array_unique($izin));
        $data['akses_semua_divisi'] = $r->boolean('akses_semua_divisi');
        return $data;
    }

    private function slugUnik(string $nama): string
    {
        $slug = Str::slug($nama); $i = 1; $base = $slug;
        while (Role::where('slug', $slug)->exists()) $slug = $base . '-' . $i++;
        return $slug;
    }
}
