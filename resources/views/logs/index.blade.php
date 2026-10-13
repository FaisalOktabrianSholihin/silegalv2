@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('subtitle', 'Jejak seluruh aktivitas pengguna di SILEGAL')

@section('content')
@php $warna = ['tambah'=>'emerald','ubah'=>'teal','hapus'=>'rose','preview'=>'olive','lihat'=>'slate','login'=>'brand','logout'=>'slate','login_gagal'=>'rose']; @endphp
<form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-6">
    <div class="lg:col-span-2"><input name="q" value="{{ request('q') }}" placeholder="Cari deskripsi…" class="field"></div>
    <select name="user_id" class="field"><option value="">Semua user</option>@foreach ($users as $x)<option value="{{ $x->id }}" @selected(request('user_id') == $x->id)>{{ $x->name }}</option>@endforeach</select>
    <select name="aksi" class="field"><option value="">Semua aksi</option>@foreach ($aksis as $a)<option @selected(request('aksi') === $a)>{{ $a }}</option>@endforeach</select>
    <input type="date" name="dari" value="{{ request('dari') }}" class="field" title="Dari tanggal">
    <div class="flex gap-2"><input type="date" name="sampai" value="{{ request('sampai') }}" class="field" title="Sampai tanggal"><button class="rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white">Cari</button></div>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3.5">Waktu</th><th class="px-4 py-3.5">User</th><th class="px-4 py-3.5">Aksi</th><th class="px-4 py-3.5">Modul</th><th class="px-4 py-3.5">Keterangan</th><th class="px-4 py-3.5">IP</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($data as $l)
                    @php $c = $warna[$l->aksi] ?? 'slate'; @endphp
                    <tr class="align-top hover:bg-slate-50/70">
                        <td class="whitespace-nowrap px-5 py-3 text-xs text-slate-500">{{ $l->created_at->translatedFormat('d M Y H:i:s') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-semibold">{{ $l->user?->name ?? 'Tidak dikenal' }}</td>
                        <td class="whitespace-nowrap px-4 py-3"><span class="rounded-full bg-{{ $c }}-50 px-2.5 py-1 text-xs font-bold text-{{ $c }}-700">{{ $l->aksi }}</span></td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $l->modul }}</td>
                        <td class="min-w-[240px] max-w-md px-4 py-3 text-slate-600 [overflow-wrap:anywhere]">{{ $l->deskripsi }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-400">{{ $l->ip_address }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-14 text-center text-slate-400">Belum ada aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($data->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $data->links() }}</div>@endif
</div>
@endsection
