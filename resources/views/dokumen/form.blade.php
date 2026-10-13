@extends('layouts.app')
@php
    $edit = $dokumen->exists; $u = auth()->user(); $w = $cfg['warna']; $range = $cfg['tanggal'] === 'range';
    $pihakAwal = old('pihak', $edit ? $dokumen->pihaks->map(fn ($p) => ['nama' => $p->nama, 'jabatan' => $p->jabatan])->all() : [['nama' => '', 'jabatan' => ''], ['nama' => '', 'jabatan' => '']]);
@endphp
@section('title', ($edit ? 'Ubah ' : 'Tambah ') . $cfg['label'])
@section('subtitle', 'Lengkapi data dokumen di bawah ini')

@section('content')
<form method="POST" action="{{ $edit ? route('dokumen.update', [$jenis, $dokumen->id]) : route('dokumen.store', $jenis) }}" class="mx-auto max-w-4xl space-y-6">
    @csrf @if ($edit) @method('PUT') @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700"><b>Periksa kembali isian Anda:</b> terdapat {{ $errors->count() }} kesalahan.</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-3">
            <div class="grid h-10 w-10 place-items-center rounded-xl bg-{{ $w }}-50 text-{{ $w }}-600"><i data-lucide="{{ $cfg['icon'] }}" class="h-5 w-5"></i></div>
            <h3 class="font-bold">Informasi {{ $cfg['label'] }}</h3>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="lbl">Status Dokumen *</label>
                <select name="status" class="field @error('status') err @enderror">@foreach ($statuses as $s)<option @selected(old('status', $dokumen->status) === $s)>{{ $s }}</option>@endforeach</select>
                @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="lbl">Divisi *</label>
                @if ($u->lihatSemuaDivisi())
                    <select name="divisi_id" class="field @error('divisi_id') err @enderror"><option value="">— Pilih divisi —</option>@foreach ($divisis as $d)<option value="{{ $d->id }}" @selected(old('divisi_id', $dokumen->divisi_id) == $d->id)>{{ $d->nama_divisi }}</option>@endforeach</select>
                    @error('divisi_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                @else
                    <div class="field flex items-center gap-2 bg-slate-50 text-slate-600"><i data-lucide="lock" class="h-4 w-4"></i>{{ $u->divisi?->nama_divisi ?? 'Belum ada divisi' }}</div>
                @endif
            </div>
            <div>
                <label class="lbl">No. Dokumen *</label>
                <input name="no_dokumen" value="{{ old('no_dokumen', $dokumen->no_dokumen) }}" class="field @error('no_dokumen') err @enderror" placeholder="mis. 012/KTR/X/2026">
                @error('no_dokumen')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="lbl">Nama Dokumen *</label>
                <input name="nama_dokumen" value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}" class="field @error('nama_dokumen') err @enderror">
                @error('nama_dokumen')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="lbl">{{ $range ? 'Tanggal Awal' : 'Tanggal Dokumen' }} *</label>
                <input type="date" name="tanggal_awal" value="{{ old('tanggal_awal', $dokumen->tanggal_awal?->format('Y-m-d')) }}" class="field @error('tanggal_awal') err @enderror">
                @error('tanggal_awal')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            @if ($range)
                <div>
                    <label class="lbl">Tanggal Akhir *</label>
                    <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir', $dokumen->tanggal_akhir?->format('Y-m-d')) }}" class="field @error('tanggal_akhir') err @enderror">
                    @error('tanggal_akhir')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            @endif
            <div class="{{ $range ? '' : 'sm:col-span-2' }}">
                <label class="lbl">Lokasi</label>
                <input name="lokasi" value="{{ old('lokasi', $dokumen->lokasi) }}" class="field" placeholder="Lokasi arsip / tempat dokumen disimpan">
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-3">
            <div class="grid h-10 w-10 place-items-center rounded-xl bg-brand-50 text-brand-600"><i data-lucide="link" class="h-5 w-5"></i></div>
            <h3 class="font-bold">Link, Rangkuman & Catatan</h3>
        </div>
        <div class="space-y-5">
            <div>
                <label class="lbl">Link Dokumen (PDF)</label>
                <input type="url" name="link_dokumen" value="{{ old('link_dokumen', $dokumen->link_dokumen) }}" class="field @error('link_dokumen') err @enderror" placeholder="https://drive.google.com/file/d/…/view  atau  https://…/dokumen.pdf">
                <p class="mt-1 text-xs text-slate-400">Link Google Drive otomatis diubah agar bisa dipratinjau. Pastikan akses link dapat dilihat oleh pengguna.</p>
                @error('link_dokumen')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div><label class="lbl">Rangkuman</label><textarea name="rangkuman" rows="4" class="field" placeholder="Ringkasan isi dokumen…">{{ old('rangkuman', $dokumen->rangkuman) }}</textarea></div>
            <div><label class="lbl">Catatan</label><textarea name="catatan" rows="3" class="field" placeholder="Catatan tambahan…">{{ old('catatan', $dokumen->catatan) }}</textarea></div>
        </div>
    </section>

    @if ($cfg['pihak'])
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" x-data="{ pihak: @js($pihakAwal) }">
        <div class="mb-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-amber-600"><i data-lucide="users" class="h-5 w-5"></i></div>
                <div><h3 class="font-bold">Daftar Para Pihak</h3><p class="text-xs text-slate-500">Isi nama dan jabatan tiap pihak</p></div>
            </div>
            <button type="button" @click="pihak.push({nama:'',jabatan:''})" class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-bold text-white shadow hover:bg-amber-600">+ Tambah Pihak</button>
        </div>
        <div class="space-y-3">
            <template x-for="(p, i) in pihak" :key="i">
                <div class="grid gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 sm:grid-cols-[auto_1fr_1fr_auto] sm:items-end">
                    <div class="grid h-10 place-items-center rounded-lg bg-white px-3 text-xs font-extrabold text-amber-600 ring-1 ring-amber-200" x-text="'Pihak ke-' + (i + 1)"></div>
                    <div><label class="lbl">Nama</label><input :name="`pihak[${i}][nama]`" x-model="p.nama" class="field" required></div>
                    <div><label class="lbl">Jabatan</label><input :name="`pihak[${i}][jabatan]`" x-model="p.jabatan" class="field"></div>
                    <button type="button" @click="pihak.splice(i,1)" class="h-10 rounded-lg px-3 text-sm font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                </div>
            </template>
            <p x-show="pihak.length === 0" class="rounded-xl border border-dashed border-slate-300 py-8 text-center text-sm text-slate-400">Belum ada pihak. Klik “Tambah Pihak”.</p>
        </div>
        @error('pihak.*.nama')<p class="mt-2 text-xs text-rose-600">Nama pihak wajib diisi.</p>@enderror
    </section>
    @endif

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('dokumen.index', $jenis) }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
        <button class="rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30">{{ $edit ? 'Simpan Perubahan' : 'Simpan Dokumen' }}</button>
    </div>
</form>
@endsection
