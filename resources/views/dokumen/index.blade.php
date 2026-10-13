@extends('layouts.app')
@section('title', $cfg['label'])
@section('subtitle', 'Daftar dokumen ' . $cfg['label'] . (auth()->user()->lihatSemuaDivisi() ? '' : ' · ' . (auth()->user()->divisi?->nama_divisi ?? '')))

@section('content')
@php $u = auth()->user(); $w = $cfg['warna']; $range = $cfg['tanggal'] === 'range'; @endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex min-w-0 items-center gap-4">
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-{{ $w }}-50 text-{{ $w }}-600 ring-1 ring-{{ $w }}-100"><i data-lucide="{{ $cfg['icon'] }}" class="h-7 w-7"></i></div>
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

{{-- ===== Tampilan HP / tablet kecil: kartu ===== --}}
<div class="space-y-3 md:hidden">
    @forelse ($data as $i => $d)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <span class="min-w-0 font-mono text-[11px] font-semibold text-slate-500 [overflow-wrap:anywhere]">#{{ $data->firstItem() + $i }} · {{ $d->no_dokumen }}</span>
                <span class="shrink-0">@include('partials.status-badge', ['status' => $d->status_efektif])</span>
            </div>
            <div class="mt-2 text-sm font-bold leading-snug text-slate-800 [overflow-wrap:anywhere]">{{ $d->nama_dokumen }}</div>
            <dl class="mt-3 space-y-1.5 text-xs text-slate-600">
                <div class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">{{ $range ? 'Masa Berlaku' : 'Tanggal' }}</dt><dd class="min-w-0">@include('partials.dokumen-tanggal', ['d' => $d, 'range' => $range])</dd></div>
                <div class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">Lokasi</dt><dd class="min-w-0 [overflow-wrap:anywhere]">{{ $d->lokasi ?: '-' }}</dd></div>
                @if ($u->lihatSemuaDivisi())<div class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">Divisi</dt><dd class="min-w-0 [overflow-wrap:anywhere]">{{ $d->divisi?->nama_divisi ?? '-' }}</dd></div>@endif
            </dl>
            <div class="mt-3 border-t border-slate-100 pt-2">@include('partials.dokumen-aksi', ['d' => $d, 'jenis' => $jenis, 'u' => $u])</div>
        </div>
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center text-slate-400"><i data-lucide="folder-open" class="mx-auto mb-3 h-10 w-10"></i>Belum ada dokumen {{ $cfg['label'] }}.</div>
    @endforelse
    @if ($data->hasPages())<div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">{{ $data->links() }}</div>@endif
</div>

{{-- ===== Tampilan tablet / desktop: tabel ===== --}}
<div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
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
                    <tr class="align-top transition hover:bg-slate-50/70">
                        <td class="px-5 py-3.5 text-slate-400">{{ $data->firstItem() + $i }}</td>
                        <td class="min-w-[90px] max-w-[160px] px-4 py-3.5 font-mono text-xs font-semibold text-slate-600 [overflow-wrap:anywhere]">{{ $d->no_dokumen }}</td>
                        <td class="min-w-[220px] max-w-[380px] px-4 py-3.5 [overflow-wrap:anywhere]">
                            @if ($d->link_dokumen)
                                <button type="button" @click="$store.preview.open({url:@js($d->link_embed),raw:@js($d->link_dokumen),title:@js($d->nama_dokumen),id:{{ $d->id }}})" class="text-left font-semibold leading-snug text-slate-800 hover:text-brand-600">
                                    {{ $d->nama_dokumen }}
                                    <span class="ml-1 inline-block rounded bg-rose-50 px-1.5 py-0.5 align-middle text-[10px] font-bold text-rose-600">PDF</span>
                                </button>
                            @else
                                <span class="font-semibold leading-snug">{{ $d->nama_dokumen }}</span>
                            @endif
                        </td>
                        @if ($u->lihatSemuaDivisi())<td class="min-w-[120px] max-w-[200px] px-4 py-3.5 text-slate-600 [overflow-wrap:anywhere]">{{ $d->divisi?->nama_divisi ?? '-' }}</td>@endif
                        <td class="whitespace-nowrap px-4 py-3.5 text-slate-600">@include('partials.dokumen-tanggal', ['d' => $d, 'range' => $range])</td>
                        <td class="min-w-[100px] max-w-[200px] px-4 py-3.5 text-slate-600 [overflow-wrap:anywhere]">{{ $d->lokasi ?: '-' }}</td>
                        <td class="px-4 py-3.5">@include('partials.status-badge', ['status' => $d->status_efektif])</td>
                        <td class="px-4 py-3.5">@include('partials.dokumen-aksi', ['d' => $d, 'jenis' => $jenis, 'u' => $u])</td>
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
