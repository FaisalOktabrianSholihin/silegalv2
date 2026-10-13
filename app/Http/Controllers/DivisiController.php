<?php

namespace App\Http\Controllers;

use App\Models\{Divisi, Dokumen, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Divisi bersumber dari db_indukk (read-only). */
class DivisiController extends Controller
{
    public function index(Request $r)
    {
        $divisis = Divisi::withCount('karyawans') // sama-sama di db_indukk
            ->when($r->q, fn ($q, $s) => $q->where('nama_divisi', 'like', "%$s%"))
            ->orderBy('nama_divisi')->get();

        // user & dokumen ada di database silegal -> hitung terpisah (tanpa subquery lintas database)
        $userCount = User::whereNotNull('divisi_id')->select('divisi_id', DB::raw('count(*) as c'))->groupBy('divisi_id')->pluck('c', 'divisi_id');
        $dokCount  = Dokumen::whereNotNull('divisi_id')->select('divisi_id', DB::raw('count(*) as c'))->groupBy('divisi_id')->pluck('c', 'divisi_id');

        $divisis->each(function ($d) use ($userCount, $dokCount) {
            $d->setAttribute('users_count', (int) ($userCount[$d->id] ?? 0));
            $d->setAttribute('dokumens_count', (int) ($dokCount[$d->id] ?? 0));
        });

        return view('divisi.index', compact('divisis'));
    }
}
