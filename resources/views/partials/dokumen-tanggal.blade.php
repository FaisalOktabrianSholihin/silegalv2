{{-- Param: $d (Dokumen), $range (bool) --}}
{{ $d->tanggal_awal?->translatedFormat('d M Y') }}
@if ($range && $d->tanggal_akhir)
    <span class="text-slate-300">→</span> {{ $d->tanggal_akhir->translatedFormat('d M Y') }}
    @php $sisa = $d->sisa_hari; @endphp
    @if (! in_array($d->status, ['Selesai', 'Dibatalkan']) && $sisa <= config('dokumen.warning_days'))
        <div class="text-[11px] font-semibold {{ $sisa < 0 ? 'text-rose-600' : 'text-amber-600' }}">{{ $sisa < 0 ? 'Lewat ' . abs($sisa) . ' hari' : $sisa . ' hari lagi' }}</div>
    @endif
@endif
