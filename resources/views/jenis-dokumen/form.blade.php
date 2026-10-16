@extends('layouts.app')
@php
    $edit = $jenis->exists;
    $warnaHex = config('dokumen.warna_hex');
    $v = fn ($k) => old($k, $jenis->$k);
    $cek = fn ($k) => (bool) ($errors->any() ? old($k) : $jenis->$k);   // checkbox: ikuti old() saat validasi gagal
@endphp
@section('title', $edit ? 'Ubah Jenis Dokumen' : 'Tambah Jenis Dokumen')
@section('subtitle', 'Tentukan tampilan dan field yang dipakai jenis dokumen ini')

@section('content')
<form method="POST" action="{{ $edit ? route('jenis-dokumen.update', $jenis) : route('jenis-dokumen.store') }}" class="mx-auto max-w-4xl space-y-6">
    @csrf @if ($edit) @method('PUT') @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700"><b>Periksa kembali isian Anda:</b> terdapat {{ $errors->count() }} kesalahan.</div>
    @endif

    {{-- ===== Informasi dasar ===== --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-5 font-bold">Informasi Dasar</h3>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="lbl">Nama Jenis Dokumen *</label>
                <input name="label" value="{{ $v('label') }}" maxlength="60" class="field @error('label') err @enderror" placeholder="mis. Surat Kuasa">
                @error('label')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                @if ($edit)<p class="mt-1 text-xs text-slate-400">Alamat menu tetap <span class="font-mono">/dokumen/{{ $jenis->slug }}</span> meskipun nama diubah.</p>@endif
            </div>
            <div>
                <label class="lbl">Urutan di Menu</label>
                <input type="number" name="urutan" min="0" max="999" value="{{ $v('urutan') }}" class="field @error('urutan') err @enderror">
                @error('urutan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-6">
            <label class="lbl">Warna</label>
            <div class="flex flex-wrap gap-2">
                @foreach ($warnaHex as $nama => $hex)
                    <label class="cursor-pointer">
                        <input type="radio" name="warna" value="{{ $nama }}" class="peer sr-only" @checked($v('warna') === $nama)>
                        <span class="block h-9 w-9 rounded-full ring-2 ring-transparent ring-offset-2 transition peer-checked:ring-slate-800" style="background:{{ $hex }}" title="{{ $nama }}"></span>
                    </label>
                @endforeach
            </div>
            @error('warna')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="mt-6">
            <label class="lbl">Ikon</label>
            <div class="grid grid-cols-6 gap-2 sm:grid-cols-8 lg:grid-cols-12">
                @foreach (config('dokumen.icon_pilihan') as $ic)
                    <label class="cursor-pointer" title="{{ $ic }}">
                        <input type="radio" name="icon" value="{{ $ic }}" class="peer sr-only" @checked($v('icon') === $ic)>
                        <span class="grid h-11 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 peer-checked:ring-2 peer-checked:ring-brand-200"><i data-lucide="{{ $ic }}" class="h-5 w-5"></i></span>
                    </label>
                @endforeach
            </div>
            @error('icon')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </section>

    {{-- ===== Jenis tanggal ===== --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-1 font-bold">Jenis Tanggal</h3>
        <p class="mb-4 text-xs text-slate-500">Menentukan kolom tanggal pada form dan apakah dokumen punya masa berlaku.</p>
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach (['single' => ['1 Tanggal', 'Hanya "Tanggal Dokumen". Tanpa masa berlaku, tidak pernah berstatus Kadaluarsa. Cocok untuk BOM Meeting, Risalah.'], 'range' => ['Rentang Tanggal', 'Tanggal Awal & Tanggal Akhir. Punya masa berlaku, otomatis ditandai Segera Berakhir / Kadaluarsa. Cocok untuk Kontrak, SOP, Akta.']] as $val => [$jud, $desk])
                <label class="cursor-pointer">
                    <input type="radio" name="tanggal" value="{{ $val }}" class="peer sr-only" @checked($v('tanggal') === $val)>
                    <span class="block h-full rounded-xl border border-slate-200 p-4 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-200">
                        <span class="block text-sm font-bold">{{ $jud }}</span>
                        <span class="mt-1 block text-xs leading-relaxed text-slate-500">{{ $desk }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        @error('tanggal')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        @if ($edit && ($jumlah ?? 0) > 0)
            <p class="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-700">Jenis ini sudah memiliki {{ $jumlah }} dokumen. Jika jenis tanggal diubah, tampilan dan status dokumen lama akan mengikuti pengaturan baru (data tanggal tidak dihapus).</p>
        @endif
    </section>

    {{-- ===== Field ===== --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-1 font-bold">Field pada Form</h3>
        <p class="mb-4 text-xs text-slate-500">Field dasar (Status, Divisi, No. Dokumen, Nama Dokumen, Tanggal) selalu ada. Pilih field tambahan yang diperlukan.</p>
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach (['pakai_link' => ['Link Dokumen', 'Link PDF / Google Drive dengan pratinjau'], 'pakai_rangkuman' => ['Rangkuman', 'Ringkasan isi dokumen'], 'pakai_lokasi' => ['Lokasi', 'Lokasi arsip / penyimpanan'], 'pakai_catatan' => ['Catatan', 'Catatan tambahan'], 'pakai_pihak' => ['Para Pihak', 'Daftar nama & jabatan pihak (mis. kontrak, perjanjian)']] as $k => [$jud, $desk])
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 hover:bg-slate-50">
                    <input type="checkbox" name="{{ $k }}" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600" @checked($cek($k))>
                    <span><span class="block text-sm font-semibold">{{ $jud }}</span><span class="block text-xs text-slate-500">{{ $desk }}</span></span>
                </label>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <label class="flex cursor-pointer items-start gap-3">
            <input type="checkbox" name="aktif" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600" @checked($cek('aktif'))>
            <span><span class="block text-sm font-semibold">Jenis aktif</span><span class="block text-xs text-slate-500">Jika dinonaktifkan, menu disembunyikan dan dokumen baru tidak bisa ditambahkan. Dokumen lama tetap tersimpan.</span></span>
        </label>
    </section>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('jenis-dokumen.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
        <button class="rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30">{{ $edit ? 'Simpan Perubahan' : 'Simpan Jenis Dokumen' }}</button>
    </div>
</form>
@endsection
