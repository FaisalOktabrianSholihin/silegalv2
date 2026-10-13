@extends('layouts.app')
@php
    $edit = $user->exists;
    $kmap = $karyawans->mapWithKeys(fn ($k) => [$k->id => ['nama' => $k->nama, 'email' => $k->email, 'divisi' => $k->divisi_id]]);
@endphp
@section('title', $edit ? 'Ubah User' : 'Tambah User')

@section('content')
<form method="POST" action="{{ $edit ? route('users.update', $user) : route('users.store') }}" class="mx-auto max-w-3xl space-y-6"
      x-data="{ k: @js($kmap), karyawan: @js((string) old('karyawan_id', $user->karyawan_id)), name: @js(old('name', $user->name)), email: @js(old('email', $user->email)), divisi: @js((string) old('divisi_id', $user->divisi_id)),
                isi(){ const d = this.k[this.karyawan]; if(!d) return; this.name = d.nama; if(d.email) this.email = d.email; if(d.divisi) this.divisi = String(d.divisi); } }">
    @csrf @if ($edit) @method('PUT') @endif
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-5 font-bold">Akun Pengguna</h3>
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="lbl">Hubungkan ke Karyawan (opsional)</label>
                <select name="karyawan_id" x-model="karyawan" @change="isi()" class="field">
                    <option value="">— Tidak dihubungkan —</option>
                    @foreach ($karyawans as $k)<option value="{{ $k->id }}">{{ $k->nama }} · {{ $k->nik }}</option>@endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">Memilih karyawan akan mengisi nama, email, dan divisi secara otomatis.</p>
            </div>
            <div><label class="lbl">Nama *</label><input name="name" x-model="name" required class="field @error('name') err @enderror">@error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            <div><label class="lbl">Email *</label><input type="email" name="email" x-model="email" required class="field @error('email') err @enderror">@error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            <div class="sm:col-span-2"><label class="lbl">Password {{ $edit ? '(kosongkan jika tidak diubah)' : '*' }}</label><input type="password" name="password" autocomplete="new-password" class="field @error('password') err @enderror" @required(! $edit)>@error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            <div>
                <label class="lbl">Role *</label>
                <select name="role_id" class="field @error('role_id') err @enderror" required><option value="">— Pilih role —</option>@foreach ($roles as $r)<option value="{{ $r->id }}" @selected(old('role_id', $user->role_id) == $r->id)>{{ $r->nama }}{{ $r->akses_semua_divisi ? ' (semua divisi)' : ' (per divisi)' }}</option>@endforeach</select>
                @error('role_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="lbl">Divisi</label>
                <select name="divisi_id" x-model="divisi" class="field"><option value="">— Tanpa divisi —</option>@foreach ($divisis as $d)<option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>@endforeach</select>
                <p class="mt-1 text-xs text-slate-400">Wajib diisi untuk role “per divisi”. User hanya melihat dokumen divisi ini.</p>
            </div>
            <label class="flex items-center gap-3 sm:col-span-2"><input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600" @checked(old('is_active', $user->is_active))><span class="text-sm font-medium">Akun aktif</span></label>
        </div>
    </section>
    <div class="flex justify-end gap-3">
        <a href="{{ route('users.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600">Batal</a>
        <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">Simpan</button>
    </div>
</form>
@endsection
