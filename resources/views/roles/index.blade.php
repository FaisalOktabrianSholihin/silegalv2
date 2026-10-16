@extends('layouts.app')
@section('title', 'Roles')
@section('subtitle', 'Atur hak akses dan cakupan divisi')

@section('content')
@if (auth()->user()->bisa('role.tambah'))<div class="mb-6 flex justify-end"><a href="{{ route('roles.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30"><i data-lucide="plus" class="h-4 w-4"></i>Tambah Role</a></div>@endif
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($roles as $r)
        <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3"><div class="grid h-11 w-11 place-items-center rounded-xl bg-green-50 text-green-600"><i data-lucide="shield-check" class="h-5 w-5"></i></div><div><h3 class="font-bold">{{ $r->nama }}</h3><p class="text-xs text-slate-400">{{ $r->users_count }} user</p></div></div>
                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $r->akses_semua_divisi || $r->slug === 'super-admin' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $r->akses_semua_divisi || $r->slug === 'super-admin' ? 'Semua divisi' : 'Per divisi' }}</span>
            </div>
            <p class="mt-3 flex-1 text-sm text-slate-500">{{ $r->deskripsi ?: 'Tanpa deskripsi.' }}</p>
            @php $izin = $r->slug === 'super-admin' ? array_keys(config('dokumen.izin')) : ($r->izin ?? []); @endphp
            <div class="mt-4 space-y-1.5">
                @foreach (config('dokumen.modul') as $mk => $m)
                    @php
                        $aktif = collect($m['aksi'])->filter(fn ($x) => in_array("$mk.$x", $izin) || in_array($x, $m['terkunci']))->all();
                        $adaLebih = collect($aktif)->contains(fn ($x) => $x !== 'lihat');
                    @endphp
                    @continue(empty($aktif) || ($mk !== 'dokumen' && ! in_array("$mk.lihat", $izin)))
                    <div class="flex items-start gap-2 text-[11px]">
                        <span class="w-24 shrink-0 pt-0.5 font-bold text-slate-700">{{ $m['label'] }}</span>
                        <span class="flex flex-wrap gap-1">
                            @foreach ($aktif as $x)
                                <span class="rounded-md px-1.5 py-0.5 font-semibold {{ ['lihat' => 'bg-sky-50 text-sky-700', 'tambah' => 'bg-emerald-50 text-emerald-700', 'ubah' => 'bg-amber-50 text-amber-700', 'hapus' => 'bg-rose-50 text-rose-700'][$x] }}">{{ ucfirst($x) }}</span>
                            @endforeach
                        </span>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 flex justify-end gap-1 border-t border-slate-100 pt-3">
                @if (auth()->user()->bisa('role.ubah'))<a href="{{ route('roles.edit', $r) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-teal-600 hover:bg-teal-50">Ubah</a>@endif
                @if ($r->slug !== 'super-admin' && auth()->user()->bisa('role.hapus'))<form method="POST" action="{{ route('roles.destroy', $r) }}" data-confirm="Role ini akan dihapus secara permanen." data-judul="Hapus role?" data-tombol="Ya, hapus">@csrf @method('DELETE')<button class="rounded-lg px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button></form>@endif
            </div>
        </div>
    @endforeach
</div>
@endsection
