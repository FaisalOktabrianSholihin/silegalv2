<?php

namespace App\Http\Controllers;

use App\Models\{LogAktivitas, User};
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $r)
    {
        $data = LogAktivitas::with('user')
            ->when($r->user_id, fn ($q, $v) => $q->where('user_id', $v))
            ->when($r->aksi, fn ($q, $v) => $q->where('aksi', $v))
            ->when($r->dari, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($r->sampai, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($r->q, fn ($q, $s) => $q->where('deskripsi', 'like', "%$s%"))
            ->latest('id')->paginate(20)->withQueryString();

        return view('logs.index', [
            'data' => $data, 'users' => User::orderBy('name')->get(['id', 'name']),
            'aksis' => LogAktivitas::select('aksi')->distinct()->orderBy('aksi')->pluck('aksi'),
        ]);
    }
}
