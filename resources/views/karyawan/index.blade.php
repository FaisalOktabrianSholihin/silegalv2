@extends('layouts.app')
@section('title', 'Master Karyawan')
@section('subtitle', 'Data karyawan dari db_indukk · hanya baca')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-4">
    <form method="GET" class="grid flex-1 gap-3 sm:grid-cols-[1fr_260px_auto]">
        <div class="relative"><i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i><input name="q" value="{{ request('q') }}" placeholder="Cari nama / NIK / email…" class="field pl-10"></div>
        <select name="divisi" class="field"><option value="">Semua divisi</option>@foreach ($divisis as $d)<option value="{{ $d->id }}" @selected(request('divisi') == $d->id)>{{ $d->nama_divisi }}</option>@endforeach</select>
        <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white">Filter</button>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3.5">Karyawan</th><th class="px-4 py-3.5">NIK</th><th class="px-4 py-3.5">Jabatan</th><th class="px-4 py-3.5">Divisi</th><th class="px-4 py-3.5">Status</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($data as $k)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 text-xs font-bold text-white">{{ mb_strtoupper(mb_substr($k->nama, 0, 1)) }}</span>
                                <div class="min-w-0"><div class="truncate font-semibold">{{ $k->nama }}</div><div class="truncate text-xs text-slate-400">{{ $k->email ?: '-' }}</div></div>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $k->nik }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $k->divisi?->nama_divisi ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $k->status_aktif === '1' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $k->status_aktif === '1' ? 'Aktif' : 'Nonaktif' }}</span>
                            <span class="ml-1 text-xs uppercase text-slate-400">{{ $k->status_pegawai }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-14 text-center text-slate-400">Tidak ada data karyawan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($data->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $data->links() }}</div>@endif
</div>
@endsection
