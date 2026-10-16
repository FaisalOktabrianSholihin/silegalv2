@extends('layouts.app')
@section('title', 'Jenis Dokumen')
@section('subtitle', 'Atur jenis dokumen, jenis tanggal, dan field yang tersedia')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-4">
    <p class="max-w-xl text-sm text-slate-500">Tiap jenis dokumen otomatis menjadi menu di sidebar. Anda bisa menentukan apakah memakai <b>1 tanggal</b> atau <b>rentang tanggal</b>, serta field mana saja yang tampil di form.</p>
    @if (auth()->user()->bisa('jenis.tambah'))<a href="{{ route('jenis-dokumen.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30"><i data-lucide="plus" class="h-4 w-4"></i>Tambah Jenis Dokumen</a>@endif
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3.5">Jenis</th><th class="px-4 py-3.5">Tanggal</th><th class="px-4 py-3.5">Field Aktif</th><th class="px-4 py-3.5">Dokumen</th><th class="px-4 py-3.5">Status</th><th class="px-4 py-3.5 text-right">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($data as $j)
                    @php $w = $j->warna; @endphp
                    <tr class="align-top hover:bg-slate-50/70">
                        <td class="min-w-[220px] px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-{{ $w }}-50 text-{{ $w }}-600 ring-1 ring-{{ $w }}-100"><i data-lucide="{{ $j->icon }}" class="h-5 w-5"></i></div>
                                <div class="min-w-0 [overflow-wrap:anywhere]"><div class="font-semibold">{{ $j->label }}</div><div class="font-mono text-[11px] text-slate-400">/dokumen/{{ $j->slug }}</div></div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $j->tanggal === 'range' ? 'bg-amber-50 text-amber-700' : 'bg-sky-50 text-sky-700' }}">{{ $j->tanggal === 'range' ? 'Rentang tanggal' : '1 tanggal' }}</span>
                        </td>
                        <td class="min-w-[200px] px-4 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach (['pakai_lokasi' => 'Lokasi', 'pakai_link' => 'Link', 'pakai_rangkuman' => 'Rangkuman', 'pakai_catatan' => 'Catatan', 'pakai_pihak' => 'Para Pihak'] as $k => $l)
                                    @if ($j->$k)<span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">{{ $l }}</span>@endif
                                @endforeach
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 font-semibold text-slate-600">{{ number_format($j->jumlah_dokumen) }}</td>
                        <td class="whitespace-nowrap px-4 py-3.5"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $j->aktif ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $j->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="px-4 py-3.5">
                            <div class="flex justify-end gap-1">
                                @if (auth()->user()->bisa('jenis.ubah'))<a href="{{ route('jenis-dokumen.edit', $j) }}" class="rounded-lg p-2 text-teal-600 hover:bg-teal-50" title="Ubah"><i data-lucide="pencil" class="h-4 w-4"></i></a>@endif
                                @if (auth()->user()->bisa('jenis.hapus'))<form method="POST" action="{{ route('jenis-dokumen.destroy', $j) }}" data-confirm="Jenis dokumen ini akan dihapus dari menu." data-judul="Hapus jenis dokumen?" data-tombol="Ya, hapus">@csrf @method('DELETE')
                                    <button class="rounded-lg p-2 text-rose-500 hover:bg-rose-50" title="Hapus"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                </form>@endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-14 text-center text-slate-400">Belum ada jenis dokumen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
