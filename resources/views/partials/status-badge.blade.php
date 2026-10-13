@php
    $map = [
        'Draft'           => 'bg-slate-100 text-slate-600 ring-slate-200',
        'Aktif'           => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'Selesai'         => 'bg-sky-50 text-sky-700 ring-sky-200',
        'Kadaluarsa'      => 'bg-rose-50 text-rose-700 ring-rose-200',
        'Dibatalkan'      => 'bg-zinc-100 text-zinc-500 ring-zinc-200',
        'Segera Berakhir' => 'bg-amber-50 text-amber-700 ring-amber-200',
    ];
@endphp
<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $map[$status] ?? $map['Draft'] }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $status }}
</span>
