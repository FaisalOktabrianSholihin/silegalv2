<?php

namespace App\Http\Controllers;

use App\Models\{Divisi, Karyawan, Role, User};
use App\Support\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $r)
    {
        $data = User::with('role', 'divisi')
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('data'));
    }

    public function create() { return view('users.form', $this->opsi(new User(['is_active' => true]))); }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());
        $data['is_active'] = $r->boolean('is_active');
        $u = User::create($data);
        Aktivitas::catat('tambah', 'User', "Menambah user: {$u->name} ({$u->email})", $u->id);
        return redirect()->route('users.index')->with('sukses', 'User berhasil ditambahkan.');
    }

    public function edit(User $user) { return view('users.form', $this->opsi($user)); }

    public function update(Request $r, User $user)
    {
        $data = $r->validate($this->rules($user->id));
        $data['is_active'] = $r->boolean('is_active');
        if ($user->id === auth()->id()) $data['is_active'] = true; // tidak boleh menonaktifkan diri sendiri
        if (empty($data['password'])) unset($data['password']);
        $user->update($data);
        Aktivitas::catat('ubah', 'User', "Mengubah user: {$user->name} ({$user->email})", $user->id);
        return redirect()->route('users.index')->with('sukses', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->with('gagal', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
        $user->delete();
        Aktivitas::catat('hapus', 'User', "Menghapus user: {$user->name} ({$user->email})", $user->id);
        return back()->with('sukses', 'User berhasil dihapus.');
    }

    private function opsi(User $u): array
    {
        return [
            'user' => $u, 'roles' => Role::orderBy('nama')->get(), 'divisis' => Divisi::orderBy('nama_divisi')->get(),
            'karyawans' => Karyawan::orderBy('nama')->get(['id', 'nama', 'email', 'divisi_id', 'nik']),
        ];
    }

    private function rules(?int $id = null): array
    {
        return [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email' . ($id ? ",$id" : ''),
            'password'    => [$id ? 'nullable' : 'required', Password::min(8)],
            'role_id'     => 'required|exists:roles,id',
            'divisi_id'   => 'nullable|exists:indukk.divisis,id',
            'karyawan_id' => 'nullable|exists:indukk.karyawans,id',
        ];
    }
}
