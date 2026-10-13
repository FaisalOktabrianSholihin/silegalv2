@extends('layouts.app')
@section('title', $cfg['label'])
@section('subtitle', 'Daftar dokumen ' . $cfg['label'] . (auth()->user()->lihatSemuaDivisi() ? '' : ' · ' . (auth()->user()->divisi?->nama_divisi ?? '')))

@section('content')
@php $u = auth()->user(); $w = $cfg['warna']; $range = $cfg['tanggal'] === 'range'; @endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-{{ $w }}-50 text-{{ $w }}-600 ring-1 ring-{{ $w }}-100"><i data-lucide="{{ $cfg['icon'] }}" class="h-7 w-7"></i></div>
        <div>
            <h2 class="text-xl font-extrabold">{{ $cfg['label'] }}</h2>
            <p class="text-sm text-slate-500">{{ number_format($data->total()) }} dokumen ditemukan</p>
        </div>
    </div>
    @if ($u->bisa('dokumen.tambah'))
        <a href="{{ route('dokumen.create', $jenis) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:shadow-xl">
            <i data-lucide="plus" class="h-4 w-4"></i>Tambah Dokumen
        </a>
    @endif
</div>

<form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[1fr_180px_240px_auto]">
    <div class="relative">
        <i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
        <input name="q" value="{{ request('q') }}" placeholder="Cari no / nama dokumen / lokasi…" class="field pl-10">
    </div>
    <select name="status" class="field"><option value="">Semua status</option>@foreach ($statuses as $s)<option @selected(request('status') === $s)>{{ $s }}</option>@endforeach</select>
    @if ($u->lihatSemuaDivisi())
        <select name="divisi" class="field"><option value="">Semua divisi</option>@foreach ($divisis as $d)<option value="{{ $d->id }}" @selected(request('divisi') == $d->id)>{{ $d->nama_divisi }}</option>@endforeach</select>
    @endif
    <div class="flex gap-2">
        <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Filter</button>
        @if (request()->hasAny(['q', 'status', 'divisi']))<a href="{{ route('dokumen.index', $jenis) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>@endif
    </div>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3.5">#</th>
                    <th class="px-4 py-3.5">No. Dokumen</th>
                    <th class="px-4 py-3.5">Nama Dokumen</th>
                    @if ($u->lihatSemuaDivisi())<th class="px-4 py-3.5">Divisi</th>@endif
                    <th class="px-4 py-3.5">{{ $range ? 'Masa Berlaku' : 'Tanggal' }}</th>
                    <th class="px-4 py-3.5">Lokasi</th>
                    <th class="px-4 py-3.5">Status</th>
                    <th class="px-4 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($data as $i => $d)
                    <tr class="transition hover:bg-slate-50/70">
                        <td class="px-5 py-3.5 text-slate-400">{{ $data->firstItem() + $i }}</td>
                        <td class="whitespace-nowrap px-4 py-3.5 font-mono text-xs font-semibold text-slate-600">{{ $d->no_dokumen }}</td>
                        <td class="max-w-xs px-4 py-3.5">
                            @if ($d->link_dokumen)
                                <button @click="$store.preview.open({url:@js($d->link_embed),raw:@js($d->link_dokumen),title:@js($d->nama_dokumen),id:{{ $d->id }}})" class="group flex items-center gap-2 text-left font-semibold text-slate-800 hover:text-brand-600">
                                    <span class="truncate">{{ $d->nama_dokumen }}</span>
                                    <span class="shrink-0 rounded bg-rose-50 px-1.5 py-0.5 text-[10px] font-bold text-rose-600 group-hover:bg-rose-100">PDF</span>
                                </button>
                            @else
                                <span class="font-semibold">{{ $d->nama_dokumen }}</span>
                            @endif
                        </td>
                        @if ($u->lihatSemuaDivisi())<td class="px-4 py-3.5 text-slate-600">{{ $d->divisi?->nama_divisi ?? '-' }}</td>@endif
                        <td class="whitespace-nowrap px-4 py-3.5 text-slate-600">
                            {{ $d->tanggal_awal?->translatedFormat('d M Y') }}
                            @if ($range && $d->tanggal_akhir)
                                <span class="text-slate-300">→</span> {{ $d->tanggal_akhir->translatedFormat('d M Y') }}
                                @php $sisa = $d->sisa_hari; @endphp
                                @if (! in_array($d->status, ['Selesai', 'Dibatalkan']) && $sisa <= config('dokumen.warning_days'))
                                    <div class="text-[11px] font-semibold {{ $sisa < 0 ? 'text-rose-600' : 'text-amber-600' }}">{{ $sisa < 0 ? 'Lewat ' . abs($sisa) . ' hari' : $sisa . ' hari lagi' }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">{{ $d->lokasi ?: '-' }}</td>
                        <td class="px-4 py-3.5">@include('partials.status-badge', ['status' => $d->status_efektif])</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1">
                                @if ($d->link_dokumen)
                                    <button @click="$store.preview.open({url:@js($d->link_embed),raw:@js($d->link_dokumen),title:@js($d->nama_dokumen),id:{{ $d->id }}})" class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Preview PDF"><i data-lucide="file-search" class="h-4 w-4"></i></button>
                                @endif
                                <a href="{{ route('dokumen.show', [$jenis, $d->id]) }}" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100" title="Detail"><i data-lucide="eye" class="h-4 w-4"></i></a>
                                @if ($u->bisa('dokumen.ubah'))<a href="{{ route('dokumen.edit', [$jenis, $d->id]) }}" class="rounded-lg p-2 text-teal-600 hover:bg-teal-50" title="Ubah"><i data-lucide="pencil" class="h-4 w-4"></i></a>@endif
                                @if ($u->bisa('dokumen.hapus'))
                                    <form method="POST" action="{{ route('dokumen.destroy', [$jenis, $d->id]) }}" onsubmit="return confirm('Hapus dokumen ini?')">@csrf @method('DELETE')
                                        <button class="rounded-lg p-2 text-rose-500 hover:bg-rose-50" title="Hapus"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-16 text-center text-slate-400"><i data-lucide="folder-open" class="mx-auto mb-3 h-10 w-10"></i>Belum ada dokumen {{ $cfg['label'] }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($data->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $data->links() }}</div>@endif
</div>
@endsection
