@extends('layouts.app')
@section('title', $dokumen->nama_dokumen)
@section('subtitle', $cfg['label'] . ' · ' . $dokumen->no_dokumen)

@section('content')
@php $u = auth()->user(); $range = $cfg['tanggal'] === 'range'; $w = $cfg['warna']; $adaLink = $cfg['link']; @endphp
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('dokumen.index', $jenis) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-brand-600"><i data-lucide="arrow-left" class="h-4 w-4"></i>Kembali ke {{ $cfg['label'] }}</a>
    <div class="flex gap-2">
        @if ($u->bisa('dokumen.ubah'))<a href="{{ route('dokumen.edit', [$jenis, $dokumen->id]) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50"><i data-lucide="pencil" class="h-4 w-4"></i>Ubah</a>@endif
    </div>
</div>

<div class="grid gap-6 {{ $adaLink ? 'xl:grid-cols-[420px_minmax(0,1fr)]' : 'mx-auto max-w-3xl' }}">
    <div class="min-w-0 space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-start justify-between gap-3">
                <span class="rounded-full bg-{{ $w }}-50 px-3 py-1 text-xs font-bold text-{{ $w }}-700">{{ $cfg['label'] }}</span>
                @include('partials.status-badge', ['status' => $dokumen->status_efektif])
            </div>
            <h2 class="text-lg font-extrabold leading-snug [overflow-wrap:anywhere]">{{ $dokumen->nama_dokumen }}</h2>
            <dl class="mt-5 space-y-3 text-sm">
                @foreach ([
                    ['No. Dokumen', $dokumen->no_dokumen],
                    ['Divisi', $dokumen->divisi?->nama_divisi ?? '-'],
                    [$range ? 'Tanggal Awal' : 'Tanggal Dokumen', $dokumen->tanggal_awal?->translatedFormat('d F Y')],
                    ...($range ? [['Tanggal Akhir', $dokumen->tanggal_akhir?->translatedFormat('d F Y')]] : []),
                    ...($cfg['lokasi'] ? [['Lokasi', $dokumen->lokasi ?: '-']] : []),
                    ['Dibuat oleh', ($dokumen->pembuat?->name ?? '-') . ' · ' . $dokumen->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB'],
                ] as [$k, $v])
                    <div class="flex justify-between gap-4 border-b border-slate-100 pb-2"><dt class="shrink-0 text-slate-500">{{ $k }}</dt><dd class="min-w-0 text-right font-semibold [overflow-wrap:anywhere]">{{ $v }}</dd></div>
                @endforeach
            </dl>
        </section>

        @if ($cfg['rangkuman'] && $dokumen->rangkuman)
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h3 class="mb-2 font-bold">Rangkuman</h3><p class="whitespace-pre-line text-sm leading-relaxed text-slate-600 [overflow-wrap:anywhere]">{{ $dokumen->rangkuman }}</p></section>
        @endif
        @if ($cfg['catatan'] && $dokumen->catatan)
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-6"><h3 class="mb-2 font-bold text-amber-800">Catatan</h3><p class="whitespace-pre-line text-sm text-amber-900/80 [overflow-wrap:anywhere]">{{ $dokumen->catatan }}</p></section>
        @endif

        @if ($cfg['pihak'])
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-bold">Para Pihak</h3>
                <ol class="space-y-3">
                    @forelse ($dokumen->pihaks as $p)
                        <li class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-amber-100 text-xs font-extrabold text-amber-700">{{ $p->urutan }}</span>
                            <div class="min-w-0"><div class="text-sm font-semibold [overflow-wrap:anywhere]">{{ $p->nama }}</div><div class="text-xs text-slate-500 [overflow-wrap:anywhere]">{{ $p->jabatan ?: '-' }}</div></div>
                            <span class="ml-auto shrink-0 text-[11px] text-slate-400">Pihak ke-{{ $p->urutan }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">Belum ada data pihak.</li>
                    @endforelse
                </ol>
            </section>
        @endif
    </div>

    @if ($adaLink)
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3">
            <h3 class="font-bold">Pratinjau Dokumen</h3>
            @if ($dokumen->link_dokumen)<a href="{{ $dokumen->link_dokumen }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline"><i data-lucide="external-link" class="h-3.5 w-3.5"></i>Buka di tab baru</a>@endif
        </div>
        @if ($dokumen->link_dokumen)
            <iframe src="{{ $dokumen->link_embed }}" class="h-[70vh] w-full bg-slate-100 sm:h-[80vh]"></iframe>
        @else
            <div class="grid h-80 place-items-center text-sm text-slate-400"><div class="text-center"><i data-lucide="file-x" class="mx-auto mb-3 h-10 w-10"></i>Dokumen ini belum memiliki link.</div></div>
        @endif
    </section>
    @endif
</div>
@endsection
