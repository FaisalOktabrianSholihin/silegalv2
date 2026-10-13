<?php

namespace App\Http\Controllers;

use App\Models\{Dokumen, Divisi, Karyawan, LogAktivitas, User};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $u = auth()->user();
        $base = fn () => Dokumen::milikUser($u);
        $today = today();
        $batas = today()->addDays(config('dokumen.warning_days'));
        $tutup = ['Selesai', 'Dibatalkan'];

        $stat = [
            'total'      => $base()->count(),
            'aktif'      => $base()->where('status', 'Aktif')
                                ->where(fn ($q) => $q->whereNull('tanggal_akhir')->orWhere('tanggal_akhir', '>=', $today))->count(),
            'segera'     => $base()->whereNotIn('status', $tutup)->whereBetween('tanggal_akhir', [$today, $batas])->count(),
            'kadaluarsa' => $base()->whereNotIn('status', $tutup)->whereNotNull('tanggal_akhir')->where('tanggal_akhir', '<', $today)->count(),
        ];

        // Kelompokkan per slug di PHP, karena di database bisa tersimpan sebagai label ("BOM Meeting") maupun slug ("bom-meeting").
        $perJenis = $base()->select('jenis', DB::raw('count(*) as total'))->groupBy('jenis')->get()
            ->groupBy(fn ($r) => Dokumen::slugJenis($r->getRawOriginal('jenis')))
            ->map(fn ($g) => (int) $g->sum('total'));

        // 6 bulan terakhir
        $bulan = collect(range(5, 0))->map(fn ($i) => Carbon::today()->startOfMonth()->subMonths($i));
        $perBulanRaw = $base()->where('created_at', '>=', $bulan->first())
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bln"), DB::raw('count(*) as total'))
            ->groupBy('bln')->pluck('total', 'bln');
        $grafikBulan = [
            'label' => $bulan->map(fn ($b) => $b->translatedFormat('M Y'))->all(),
            'data'  => $bulan->map(fn ($b) => (int) ($perBulanRaw[$b->format('Y-m')] ?? 0))->all(),
        ];

        $perDivisi = null;
        if ($u->lihatSemuaDivisi()) {
            $hitung = Dokumen::select('divisi_id', DB::raw('count(*) as total'))
                ->groupBy('divisi_id')->orderByDesc('total')->limit(8)->get();
            $nama = Divisi::whereIn('id', $hitung->pluck('divisi_id')->filter())->pluck('nama_divisi', 'id');
            $perDivisi = $hitung->map(fn ($r) => (object) ['nama' => $nama[$r->divisi_id] ?? 'Tanpa Divisi', 'total' => (int) $r->total])->values();
        }

        $segeraList = $base()->with('divisi')->whereNotIn('status', $tutup)->whereNotNull('tanggal_akhir')
            ->where('tanggal_akhir', '<=', $batas)->orderBy('tanggal_akhir')->limit(6)->get();
        $terbaru = $base()->with('divisi')->latest('id')->limit(6)->get();

        $logs = LogAktivitas::with('user')
            ->when(! $u->bisa('log.lihat'), fn ($q) => $q->where('user_id', $u->id))
            ->latest('id')->limit(7)->get();

        $master = $u->bisa('master.kelola') ? [
            'karyawan' => Karyawan::count(), 'divisi' => Divisi::count(), 'user' => User::count(),
        ] : null;

        return view('dashboard', compact('stat', 'perJenis', 'grafikBulan', 'perDivisi', 'segeraList', 'terbaru', 'logs', 'master'));
    }
}
