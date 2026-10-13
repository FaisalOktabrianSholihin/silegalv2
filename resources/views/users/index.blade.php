@extends('layouts.app')
@section('title', 'Master User')
@section('subtitle', 'Akun yang dapat masuk ke SILEGAL')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-4">
    <form method="GET" class="relative w-full max-w-sm"><i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i><input name="q" value="{{ request('q') }}" placeholder="Cari nama / email…" class="field pl-10"></form>
    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30"><i data-lucide="plus" class="h-4 w-4"></i>Tambah User</a>
</div>
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3.5">User</th><th class="px-4 py-3.5">Role</th><th class="px-4 py-3.5">Divisi</th><th class="px-4 py-3.5">Login Terakhir</th><th class="px-4 py-3.5">Status</th><th class="px-4 py-3.5 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($data as $x)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-3"><div class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-brand-400 to-green-500 text-xs font-bold text-white">{{ $x->inisial }}</span><div><div class="font-semibold">{{ $x->name }}</div><div class="text-xs text-slate-400">{{ $x->email }}</div></div></div></td>
                        <td class="px-4 py-3"><span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700">{{ $x->role?->nama ?? '-' }}</span></td>
                        <td class="px-4 py-3 text-slate-600">{{ $x->divisi?->nama_divisi ?? ($x->role?->akses_semua_divisi || $x->isSuperAdmin() ? 'Semua divisi' : '-') }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $x->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $x->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-600' }}">{{ $x->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="px-4 py-3"><div class="flex justify-end gap-1">
                            <a href="{{ route('users.edit', $x) }}" class="rounded-lg p-2 text-teal-600 hover:bg-teal-50"><i data-lucide="pencil" class="h-4 w-4"></i></a>
                            @if ($x->id !== auth()->id())<form method="POST" action="{{ route('users.destroy', $x) }}" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="rounded-lg p-2 text-rose-500 hover:bg-rose-50"><i data-lucide="trash-2" class="h-4 w-4"></i></button></form>@endif
                        </div></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($data->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $data->links() }}</div>@endif
</div>
@endsection
