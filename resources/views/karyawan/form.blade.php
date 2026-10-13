@extends('layouts.app')
@php $edit = $karyawan->exists; @endphp
@section('title', $edit ? 'Ubah Karyawan' : 'Tambah Karyawan')

@section('content')
<form method="POST" action="{{ $edit ? route('master.karyawan.update', $karyawan) : route('master.karyawan.store') }}" class="mx-auto max-w-4xl space-y-6">
    @csrf @if ($edit) @method('PUT') @endif
    @php
        $f = fn ($n, $l, $type = 'text', $req = false) => compact('n', 'l', 'type', 'req');
        $fields = [
            $f('nik', 'NIK', 'text', true), $f('no_absen', 'No. Absen'), $f('nama', 'Nama Lengkap', 'text', true), $f('nama_panggilan', 'Nama Panggilan'),
            $f('no_ktp', 'No. KTP', 'text', true), $f('email', 'Email', 'email'), $f('no_hp', 'No. HP'), $f('golongan', 'Golongan'),
            $f('tanggal_lahir', 'Tanggal Lahir', 'date'), $f('tanggal_masuk', 'Tanggal Masuk', 'date'),
        ];
    @endphp
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-5 font-bold">Data Karyawan</h3>
        <div class="grid gap-5 sm:grid-cols-2">
            @foreach ($fields as $x)
                <div>
                    <label class="lbl">{{ $x['l'] }} {{ $x['req'] ? '*' : '' }}</label>
                    <input type="{{ $x['type'] }}" name="{{ $x['n'] }}" value="{{ old($x['n'], $x['type'] === 'date' ? $karyawan->{$x['n']}?->format('Y-m-d') : $karyawan->{$x['n']}) }}" class="field @error($x['n']) err @enderror" @required($x['req'])>
                    @error($x['n'])<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <div class="sm:col-span-2">
                <label class="lbl">Alamat *</label>
                <input name="alamat" value="{{ old('alamat', $karyawan->alamat) }}" class="field @error('alamat') err @enderror" required>
                @error('alamat')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div><label class="lbl">Divisi</label><select name="divisi_id" class="field"><option value="">—</option>@foreach ($divisis as $d)<option value="{{ $d->id }}" @selected(old('divisi_id', $karyawan->divisi_id) == $d->id)>{{ $d->nama_divisi }}</option>@endforeach</select></div>
            <div><label class="lbl">Jabatan</label><select name="jabatan_id" class="field"><option value="">—</option>@foreach ($jabatans as $j)<option value="{{ $j->id }}" @selected(old('jabatan_id', $karyawan->jabatan_id) == $j->id)>{{ $j->nama_jabatan }}</option>@endforeach</select></div>
            <div><label class="lbl">Status Pegawai</label><select name="status_pegawai" class="field">@foreach (['pkwtt' => 'PKWTT', 'pkwt' => 'PKWT', 'penugasan' => 'Penugasan'] as $v => $l)<option value="{{ $v }}" @selected(old('status_pegawai', $karyawan->status_pegawai) === $v)>{{ $l }}</option>@endforeach</select></div>
            <div><label class="lbl">Status Aktif</label><select name="status_aktif" class="field"><option value="1" @selected(old('status_aktif', $karyawan->status_aktif) === '1')>Aktif</option><option value="0" @selected(old('status_aktif', $karyawan->status_aktif) === '0')>Nonaktif</option></select></div>
            <div><label class="lbl">Jenis Kelamin</label><select name="jenis_kelamin" class="field"><option value="">—</option>@foreach (['PRIA', 'WANITA'] as $jk)<option @selected(old('jenis_kelamin', $karyawan->jenis_kelamin) === $jk)>{{ $jk }}</option>@endforeach</select></div>
        </div>
    </section>
    <div class="flex justify-end gap-3">
        <a href="{{ route('master.karyawan.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600">Batal</a>
        <button class="rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30">Simpan</button>
    </div>
</form>
@endsection
