@extends('layouts.app')
@php $edit = $role->exists; $super = $role->slug === 'super-admin'; $izinAktif = old('izin', $role->izin ?? []); @endphp
@section('title', $edit ? 'Ubah Role' : 'Tambah Role')

@section('content')
<form method="POST" action="{{ $edit ? route('roles.update', $role) : route('roles.store') }}" class="mx-auto max-w-3xl space-y-6">
    @csrf @if ($edit) @method('PUT') @endif
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="space-y-5">
            <div><label class="lbl">Nama Role *</label><input name="nama" value="{{ old('nama', $role->nama) }}" required class="field @error('nama') err @enderror">@error('nama')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            <div><label class="lbl">Deskripsi</label><textarea name="deskripsi" rows="2" class="field">{{ old('deskripsi', $role->deskripsi) }}</textarea></div>

            <label class="flex items-start gap-3 rounded-xl border border-indigo-200 bg-indigo-50/60 p-4">
                <input type="checkbox" name="akses_semua_divisi" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600" @checked(old('akses_semua_divisi', $role->akses_semua_divisi) || $super) @disabled($super)>
                <span><span class="block text-sm font-bold">Akses semua divisi</span><span class="text-xs text-slate-500">Jika tidak dicentang, user dengan role ini hanya melihat dokumen milik divisinya sendiri (mis. user Divisi Budidaya hanya melihat dokumen Divisi Budidaya).</span></span>
            </label>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 font-bold">Izin</h3>
        @if ($super)<p class="mb-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-700">Super Admin selalu memiliki seluruh izin.</p>@endif
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach (config('dokumen.izin') as $key => $label)
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
                    <input type="checkbox" name="izin[]" value="{{ $key }}" class="h-4 w-4 rounded border-slate-300 text-indigo-600" @checked(in_array($key, $izinAktif) || $super) @disabled($super)>
                    <span class="text-sm font-medium">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <p class="mt-3 text-xs text-slate-400">Semua role dapat melihat dokumen sesuai cakupan divisinya.</p>
    </section>

    <div class="flex justify-end gap-3">
        <a href="{{ route('roles.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600">Batal</a>
        <button class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30">Simpan</button>
    </div>
</form>
@endsection
