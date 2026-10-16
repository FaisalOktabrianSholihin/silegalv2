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

            <label class="flex items-start gap-3 rounded-xl border border-brand-200 bg-brand-50/60 p-4">
                <input type="checkbox" name="akses_semua_divisi" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600" @checked(old('akses_semua_divisi', $role->akses_semua_divisi) || $super) @disabled($super)>
                <span><span class="block text-sm font-bold">Akses semua divisi</span><span class="text-xs text-slate-500">Jika tidak dicentang, user dengan role ini hanya melihat dokumen milik divisinya sendiri (mis. user Divisi Budidaya hanya melihat dokumen Divisi Budidaya).</span></span>
            </label>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        x-data="{
            sel: @js(array_values($super ? array_keys(config('dokumen.izin')) : $izinAktif)),
            modul: @js(config('dokumen.modul')),
            super: @js($super),
            has(k) { return this.sel.includes(k); },
            adaLihat(mk) { const m = this.modul[mk]; return m.aksi.includes('lihat') && !m.terkunci.includes('lihat'); },
            toggle(mk, x) {
                if (this.super) return;
                const k = mk + '.' + x;
                if (this.has(k)) {
                    this.sel = this.sel.filter(i => i !== k);
                    if (x === 'lihat') this.sel = this.sel.filter(i => !i.startsWith(mk + '.')); // tanpa Lihat, aksi lain tidak berguna
                } else {
                    this.sel.push(k);
                    if (x !== 'lihat' && this.adaLihat(mk) && !this.has(mk + '.lihat')) this.sel.push(mk + '.lihat');
                }
            },
            keys(mk) { const m = this.modul[mk]; return m.aksi.filter(x => !m.terkunci.includes(x)).map(x => mk + '.' + x); },
            rowAll(mk) {
                if (this.super) return;
                const ks = this.keys(mk);
                this.sel = ks.every(k => this.has(k)) ? this.sel.filter(i => !ks.includes(i)) : [...new Set([...this.sel, ...ks])];
            },
            rowFull(mk) { const ks = this.keys(mk); return ks.length > 0 && ks.every(k => this.has(k)); },
            colAll(x) {
                if (this.super) return;
                const ks = Object.keys(this.modul).filter(mk => this.modul[mk].aksi.includes(x) && !this.modul[mk].terkunci.includes(x)).map(mk => mk + '.' + x);
                if (ks.every(k => this.has(k))) { this.sel = this.sel.filter(i => !ks.includes(i)); if (x === 'lihat') this.sel = []; }
                else this.sel = [...new Set([...this.sel, ...ks])];
            },
            semua() { if (!this.super) this.sel = Object.keys(this.modul).flatMap(mk => this.keys(mk)); },
            kosong() { if (!this.super) this.sel = []; },
            hanyaLihat() { if (!this.super) this.sel = Object.keys(this.modul).filter(mk => this.adaLihat(mk) && mk !== 'role' && mk !== 'user').map(mk => mk + '.lihat'); }
        }">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div><h3 class="font-bold">Hak Akses</h3><p class="text-xs text-slate-500">Centang aksi yang boleh dilakukan role ini pada tiap menu.</p></div>
            @unless ($super)
            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                <button type="button" @click="semua()" class="rounded-lg bg-brand-50 px-3 py-1.5 text-brand-700 hover:bg-brand-100">Pilih semua</button>
                <button type="button" @click="hanyaLihat()" class="rounded-lg bg-sky-50 px-3 py-1.5 text-sky-700 hover:bg-sky-100">Hanya lihat</button>
                <button type="button" @click="kosong()" class="rounded-lg bg-slate-100 px-3 py-1.5 text-slate-600 hover:bg-slate-200">Kosongkan</button>
            </div>
            @endunless
        </div>
        @if ($super)<p class="mb-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-700">Super Admin selalu memiliki seluruh izin.</p>@endif

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full min-w-[560px] text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold">Menu</th>
                        @foreach (['lihat' => 'Lihat', 'tambah' => 'Tambah', 'ubah' => 'Ubah', 'hapus' => 'Hapus'] as $x => $lb)
                            <th class="px-3 py-3 text-center font-bold">
                                <button type="button" @click="colAll('{{ $x }}')" class="rounded-md px-1.5 py-0.5 uppercase hover:bg-slate-200" title="Pilih/kosongkan kolom {{ $lb }}">{{ $lb }}</button>
                            </th>
                        @endforeach
                        <th class="px-3 py-3 text-center font-bold">Semua</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach (config('dokumen.modul') as $mk => $m)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-brand-50 text-brand-600"><i data-lucide="{{ $m['icon'] }}" class="h-4 w-4"></i></div>
                                    <div><div class="font-semibold text-slate-800">{{ $m['label'] }}</div><div class="text-[11px] text-slate-400">{{ $m['ket'] }}</div></div>
                                </div>
                            </td>
                            @foreach (['lihat', 'tambah', 'ubah', 'hapus'] as $x)
                                <td class="px-3 py-3 text-center">
                                    @if (! in_array($x, $m['aksi']))
                                        <span class="text-slate-300">—</span>
                                    @elseif (in_array($x, $m['terkunci']))
                                        <span class="inline-grid h-5 w-5 place-items-center rounded bg-slate-200 text-slate-500" title="Selalu aktif untuk semua role"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                                    @else
                                        <input type="checkbox" name="izin[]" value="{{ $mk }}.{{ $x }}" :checked="has('{{ $mk }}.{{ $x }}')" @change="toggle('{{ $mk }}', '{{ $x }}')" @disabled($super)
                                               class="h-[18px] w-[18px] cursor-pointer rounded border-slate-300 text-brand-600 focus:ring-brand-500 disabled:cursor-not-allowed disabled:opacity-60">
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-3 py-3 text-center">
                                @if (count($m['aksi']) > count($m['terkunci']))
                                    <button type="button" @click="rowAll('{{ $mk }}')" @disabled($super) class="rounded-md px-2 py-1 text-[11px] font-bold transition"
                                            :class="rowFull('{{ $mk }}') ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">Semua</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-3 text-xs text-slate-400">Menu Dokumen selalu dapat dilihat sesuai cakupan divisi. Mencentang Tambah/Ubah/Hapus otomatis mencentang Lihat pada menu yang sama.</p>
    </section>

    <div class="flex justify-end gap-3">
        <a href="{{ route('roles.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600">Batal</a>
        <button class="rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30">Simpan</button>
    </div>
</form>
@endsection
