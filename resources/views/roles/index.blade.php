@extends('layouts.app')
@section('title', 'Roles')
@section('subtitle', 'Atur hak akses dan cakupan divisi')

@section('content')
<div class="mb-6 flex justify-end"><a href="{{ route('roles.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30"><i data-lucide="plus" class="h-4 w-4"></i>Tambah Role</a></div>
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($roles as $r)
        <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3"><div class="grid h-11 w-11 place-items-center rounded-xl bg-green-50 text-green-600"><i data-lucide="shield-check" class="h-5 w-5"></i></div><div><h3 class="font-bold">{{ $r->nama }}</h3><p class="text-xs text-slate-400">{{ $r->users_count }} user</p></div></div>
                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $r->akses_semua_divisi || $r->slug === 'super-admin' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $r->akses_semua_divisi || $r->slug === 'super-admin' ? 'Semua divisi' : 'Per divisi' }}</span>
            </div>
            <p class="mt-3 flex-1 text-sm text-slate-500">{{ $r->deskripsi ?: 'Tanpa deskripsi.' }}</p>
            <div class="mt-4 flex flex-wrap gap-1.5">
                @php $izin = $r->slug === 'super-admin' ? array_keys(config('dokumen.izin')) : ($r->izin ?? []); @endphp
                @forelse ($izin as $i)<span class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-600">{{ config('dokumen.izin')[$i] ?? $i }}</span>@empty<span class="text-xs text-slate-400">Hanya lihat dokumen</span>@endforelse
            </div>
            <div class="mt-5 flex justify-end gap-1 border-t border-slate-100 pt-3">
                <a href="{{ route('roles.edit', $r) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-teal-600 hover:bg-teal-50">Ubah</a>
                @if ($r->slug !== 'super-admin')<form method="POST" action="{{ route('roles.destroy', $r) }}" onsubmit="return confirm('Hapus role ini?')">@csrf @method('DELETE')<button class="rounded-lg px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button></form>@endif
            </div>
        </div>
    @endforeach
</div>
@endsection
