<?php

namespace App\Http\Controllers;

use App\Models\{Divisi, Karyawan};
use Illuminate\Http\Request;

/** Karyawan bersumber dari db_indukk (read-only). */
class KaryawanController extends Controller
{
    public function index(Request $r)
    {
        $data = Karyawan::with('divisi', 'jabatan')
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('nama', 'like', "%$s%")->orWhere('nik', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
            ->when($r->divisi, fn ($q, $d) => $q->where('divisi_id', $d))
            ->orderBy('nama')->paginate(15)->withQueryString();

        return view('karyawan.index', ['data' => $data, 'divisis' => Divisi::orderBy('nama_divisi')->get()]);
    }
}
