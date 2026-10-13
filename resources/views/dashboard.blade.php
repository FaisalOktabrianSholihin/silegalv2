@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan dokumen legal dan aktivitas terbaru')

@section('content')
@php
    $u = auth()->user();
    $jenisCfg = config('dokumen.jenis');
    \Illuminate\Support\Carbon::setLocale('id');

    // Data grafik disiapkan di sini (bukan di dalam @json) supaya parser Blade tidak salah baca.
    $jenisChart = [];
    foreach ($jenisCfg as $slug => $j) {
        $jenisChart[] = ['label' => $j['label'], 'warna' => $j['warna'], 'total' => (int) ($perJenis[$slug] ?? 0)];
    }
@endphp

{{-- ===== Banner ===== --}}
<section class="relative overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-brand-500/20 sm:p-8"
         style="background:radial-gradient(600px 300px at 90% 0%,rgba(16,185,129,.35),transparent 60%),linear-gradient(120deg,#06401f,#09572b 55%,#137a45)">
    <div class="pointer-events-none absolute -right-8 -top-8 opacity-10"><i data-lucide="scale" class="h-56 w-56"></i></div>
    <div class="relative flex flex-wrap items-end justify-between gap-6">
        <div>
            <p class="text-sm text-brand-200">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">Halo, {{ $u->name }} 👋</h2>
            <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                <span class="rounded-full bg-white/15 px-3 py-1 ring-1 ring-white/25">{{ $u->role?->nama }}</span>
                <span class="rounded-full bg-white/15 px-3 py-1 ring-1 ring-white/25">{{ $u->divisi?->nama_divisi ?? 'Semua Divisi' }}</span>
            </div>
            @unless ($u->lihatSemuaDivisi())
                <p class="mt-3 flex items-center gap-2 text-xs text-brand-100"><i data-lucide="lock" class="h-3.5 w-3.5"></i>Anda hanya melihat dokumen milik {{ $u->divisi?->nama_divisi ?? 'divisi Anda' }}.</p>
            @endunless
        </div>
        @if ($master)
            <div class="flex gap-3">
                @foreach (['karyawan' => 'Karyawan', 'divisi' => 'Divisi', 'user' => 'User'] as $k => $lbl)
                    <div class="rounded-2xl bg-white/10 px-5 py-3 text-center ring-1 ring-white/20 backdrop-blur">
                        <div class="text-2xl font-extrabold">{{ number_format($master[$k]) }}</div>
                        <div class="text-[11px] text-brand-200">{{ $lbl }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ===== Kartu statistik ===== --}}
<section class="mt-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
    @foreach ([
        ['Total Dokumen', $stat['total'], 'files', 'brand', 'Seluruh dokumen tercatat'],
        ['Dokumen Aktif', $stat['aktif'], 'circle-check', 'emerald', 'Masih berlaku'],
        ['Segera Berakhir', $stat['segera'], 'clock-alert', 'amber', config('dokumen.warning_days') . ' hari ke depan'],
        ['Kadaluarsa', $stat['kadaluarsa'], 'triangle-alert', 'rose', 'Perlu tindak lanjut'],
    ] as [$lbl, $val, $ico, $c, $note])
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500">{{ $lbl }}</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($val) }}</p>
                </div>
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-{{ $c }}-50 text-{{ $c }}-600 ring-1 ring-{{ $c }}-100"><i data-lucide="{{ $ico }}" class="h-5 w-5"></i></div>
            </div>
            <p class="mt-3 text-xs text-slate-400">{{ $note }}</p>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-{{ $c }}-400 to-{{ $c }}-600 opacity-70"></div>
        </div>
    @endforeach
</section>

{{-- ===== Grafik ===== --}}
<section class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="font-bold">Komposisi Jenis Dokumen</h3>
        <p class="text-xs text-slate-500">Jumlah dokumen per kategori</p>
        <div class="relative mx-auto mt-4 h-60"><canvas id="chJenis"></canvas></div>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
        <h3 class="font-bold">Dokumen Ditambahkan</h3>
        <p class="text-xs text-slate-500">6 bulan terakhir</p>
        <div class="relative mt-4 h-60"><canvas id="chBulan"></canvas></div>
    </div>
</section>

{{-- ===== Akses cepat per jenis ===== --}}
<section class="mt-6">
    <h3 class="mb-3 font-bold">Menu Dokumen</h3>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach ($jenisCfg as $slug => $j)
            <a href="{{ route('dokumen.index', $slug) }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-{{ $j['warna'] }}-300 hover:shadow-lg">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-{{ $j['warna'] }}-50 text-{{ $j['warna'] }}-600 transition group-hover:scale-110"><i data-lucide="{{ $j['icon'] }}" class="h-6 w-6"></i></div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-bold">{{ $j['label'] }}</div>
                    <div class="text-xs text-slate-500">{{ $perJenis[$slug] ?? 0 }} dokumen</div>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- ===== Segera berakhir + aktivitas ===== --}}
<section class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div><h3 class="font-bold">Perlu Perhatian</h3><p class="text-xs text-slate-500">Dokumen yang segera atau sudah melewati masa berlaku</p></div>
            <i data-lucide="bell-ring" class="h-5 w-5 text-amber-500"></i>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($segeraList as $d)
                @php $sisa = $d->sisa_hari; @endphp
                <a href="{{ route('dokumen.show', [$d->jenis, $d->id]) }}" class="flex items-center gap-4 px-6 py-3.5 transition hover:bg-slate-50">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-{{ $d->config['warna'] ?? 'slate' }}-50 text-{{ $d->config['warna'] ?? 'slate' }}-600"><i data-lucide="{{ $d->config['icon'] ?? 'file' }}" class="h-5 w-5"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold">{{ $d->nama_dokumen }}</div>
                        <div class="truncate text-xs text-slate-500">{{ $d->config['label'] ?? '' }} · {{ $d->no_dokumen }} · {{ $d->divisi?->nama_divisi }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold {{ $sisa < 0 ? 'text-rose-600' : ($sisa <= 7 ? 'text-orange-600' : 'text-amber-600') }}">
                            {{ $sisa < 0 ? 'Lewat ' . abs($sisa) . ' hari' : ($sisa === 0 ? 'Hari ini' : $sisa . ' hari lagi') }}
                        </div>
                        <div class="text-[11px] text-slate-400">{{ $d->tanggal_akhir->translatedFormat('d M Y') }}</div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center text-sm text-slate-400"><i data-lucide="party-popper" class="mx-auto mb-2 h-8 w-8"></i>Tidak ada dokumen yang perlu perhatian.</div>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div><h3 class="font-bold">Aktivitas Terbaru</h3><p class="text-xs text-slate-500">{{ $u->bisa('log.lihat') ? 'Seluruh pengguna' : 'Aktivitas Anda' }}</p></div>
            @if ($u->bisa('log.lihat'))<a href="{{ route('log.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">Semua</a>@endif
        </div>
        <ol class="space-y-4 px-6 py-5">
            @forelse ($logs as $l)
                @php $w = ['tambah'=>'emerald','ubah'=>'teal','hapus'=>'rose','preview'=>'olive','lihat'=>'slate','login'=>'brand','logout'=>'slate','login_gagal'=>'rose'][$l->aksi] ?? 'slate'; @endphp
                <li class="flex gap-3">
                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-{{ $w }}-500 ring-4 ring-{{ $w }}-100"></span>
                    <div class="min-w-0">
                        <p class="text-[13px] leading-snug text-slate-700"><b>{{ $l->user?->name ?? 'Sistem' }}</b> · {{ $l->deskripsi }}</p>
                        <p class="text-[11px] text-slate-400">{{ $l->created_at->diffForHumans() }}</p>
                    </div>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-slate-400">Belum ada aktivitas.</li>
            @endforelse
        </ol>
    </div>
</section>

{{-- ===== Dokumen terbaru + per divisi ===== --}}
<section class="mt-6 grid gap-6 lg:grid-cols-3">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm {{ $perDivisi ? 'lg:col-span-2' : 'lg:col-span-3' }}">
        <div class="border-b border-slate-100 px-6 py-4"><h3 class="font-bold">Dokumen Terbaru</h3><p class="text-xs text-slate-500">Klik ikon PDF untuk pratinjau</p></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-6 py-3">Dokumen</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Divisi</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($terbaru as $d)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3"><a href="{{ route('dokumen.show', [$d->jenis, $d->id]) }}" class="font-semibold hover:text-brand-600">{{ $d->nama_dokumen }}</a><div class="text-xs text-slate-400">{{ $d->no_dokumen }}</div></td>
                            <td class="px-4 py-3"><span class="rounded-full bg-{{ $d->config['warna'] ?? 'slate' }}-50 px-2.5 py-1 text-xs font-semibold text-{{ $d->config['warna'] ?? 'slate' }}-700">{{ $d->config['label'] ?? $d->jenis }}</span></td>
                            <td class="px-4 py-3 text-slate-600">{{ $d->divisi?->nama_divisi ?? '-' }}</td>
                            <td class="px-4 py-3">@include('partials.status-badge', ['status' => $d->status_efektif])</td>
                            <td class="px-4 py-3 text-right">
                                @if ($d->link_dokumen)
                                    <button @click="$store.preview.open({url:@js($d->link_embed),raw:@js($d->link_dokumen),title:@js($d->nama_dokumen),id:{{ $d->id }}})" class="rounded-lg bg-rose-50 p-2 text-rose-600 hover:bg-rose-100" title="Preview"><i data-lucide="file-text" class="h-4 w-4"></i></button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada dokumen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($perDivisi)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold">Dokumen per Divisi</h3>
            <p class="text-xs text-slate-500">8 divisi teratas</p>
            <div class="relative mt-4 h-72"><canvas id="chDivisi"></canvas></div>
        </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    Chart.defaults.font.family = '"Plus Jakarta Sans", sans-serif';
    Chart.defaults.color = '#64748b';

    const warna = { brand:'#137a45', green:'#22c55e', emerald:'#10b981', teal:'#14b8a6', lime:'#84cc16', amber:'#f59e0b', sage:'#5f8f76', olive:'#889340', rose:'#f43f5e' };
    const jenis = @json($jenisChart);

    new Chart(document.getElementById('chJenis'), {
        type: 'doughnut',
        data: { labels: jenis.map(j => j.label), datasets: [{ data: jenis.map(j => j.total), backgroundColor: jenis.map(j => warna[j.warna]), borderWidth: 3, borderColor: '#fff' }] },
        options: { maintainAspectRatio: false, cutout: '66%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11 } } } } }
    });

    const bln = @json($grafikBulan);
    const ctx = document.getElementById('chBulan').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 240); grad.addColorStop(0, 'rgba(19,122,69,.35)'); grad.addColorStop(1, 'rgba(19,122,69,0)');
    new Chart(ctx, {
        type: 'line',
        data: { labels: bln.label, datasets: [{ label: 'Dokumen', data: bln.data, borderColor: '#137a45', backgroundColor: grad, fill: true, tension: .4, pointRadius: 5, pointBackgroundColor: '#fff', pointBorderWidth: 3 }] },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
    });

    @if ($perDivisi)
    const dv = @json($perDivisi);
    new Chart(document.getElementById('chDivisi'), {
        type: 'bar',
        data: { labels: dv.map(d => d.nama.replace('Divisi ', '')), datasets: [{ data: dv.map(d => d.total), backgroundColor: '#48b06f', borderRadius: 6, barThickness: 14 }] },
        options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } }, y: { grid: { display: false }, ticks: { font: { size: 11 } } } } }
    });
    @endif
});
</script>
@endpush
