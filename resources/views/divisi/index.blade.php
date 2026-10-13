@extends('layouts.app')
@section('title', 'Master Divisi')
@section('subtitle', 'Divisi menentukan batas akses dokumen tiap user')

@section('content')
<div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="relative w-full max-w-sm">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
            <input name="q" value="{{ request('q') }}" placeholder="Cari divisi…" class="field pl-10">
        </form>
        <span class="inline-flex items-center gap-2 rounded-xl bg-teal-50 px-4 py-2 text-xs font-semibold text-teal-700 ring-1 ring-teal-200"><i data-lucide="database" class="h-4 w-4"></i>Sumber: db_indukk · hanya baca</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($divisis as $d)
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="flex items-start gap-3">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-600"><i data-lucide="network" class="h-5 w-5"></i></div>
                    <div class="min-w-0 flex-1"><h3 class="font-bold leading-snug">{{ $d->nama_divisi }}</h3><p class="text-xs text-slate-400">ID #{{ $d->id }}</p></div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    @foreach ([['Karyawan', $d->karyawans_count], ['User', $d->users_count], ['Dokumen', $d->dokumens_count]] as [$l, $n])
                        <div class="rounded-xl bg-slate-50 py-2"><div class="text-lg font-extrabold">{{ $n }}</div><div class="text-[10px] text-slate-500">{{ $l }}</div></div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="col-span-full py-16 text-center text-slate-400">Belum ada divisi.</p>
        @endforelse
    </div>

</div>
@endsection
