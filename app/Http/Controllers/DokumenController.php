<?php

namespace App\Http\Controllers;

use App\Models\{Dokumen, Divisi};
use App\Support\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DokumenController extends Controller
{
    private function cfg(string $jenis): array
    {
        return config("dokumen.jenis.$jenis") ?? abort(404);
    }

    /** Pastikan dokumen sesuai jenis & user berhak (aturan per divisi). */
    private function otorisasi(Dokumen $d, ?string $jenis = null): void
    {
        $u = auth()->user();
        abort_if($jenis && $d->jenis !== $jenis, 404);
        abort_unless($u->lihatSemuaDivisi() || ($u->divisi_id && $d->divisi_id === $u->divisi_id), 403, 'Dokumen ini bukan milik divisi Anda.');
    }

    public function index(Request $r, string $jenis)
    {
        $cfg = $this->cfg($jenis);
        $u = $r->user();

        $data = Dokumen::jenis($jenis)->milikUser($u)->with('divisi')
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('no_dokumen', 'like', "%$s%")
                ->orWhere('nama_dokumen', 'like', "%$s%")->orWhere('lokasi', 'like', "%$s%")))
            ->when($r->status, fn ($q, $s) => $q->where('status', $s))
            ->when($u->lihatSemuaDivisi() && $r->divisi, fn ($q) => $q->where('divisi_id', $r->divisi))
            ->latest('id')->paginate(10)->withQueryString();

        return view('dokumen.index', [
            'jenis' => $jenis, 'cfg' => $cfg, 'data' => $data,
            'statuses' => config('dokumen.status'),
            'divisis' => $u->lihatSemuaDivisi() ? Divisi::orderBy('nama_divisi')->get() : collect(),
        ]);
    }

    public function create(string $jenis)
    {
        return view('dokumen.form', [
            'jenis' => $jenis, 'cfg' => $this->cfg($jenis), 'dokumen' => new Dokumen(['status' => 'Aktif']),
            'statuses' => config('dokumen.status'), 'divisis' => Divisi::orderBy('nama_divisi')->get(),
        ]);
    }

    public function store(Request $r, string $jenis)
    {
        $cfg = $this->cfg($jenis);
        $data = $r->validate($this->rules($r, $jenis, $cfg));
        $divisiId = $this->tentukanDivisi($r, $data);

        $dokumen = DB::transaction(function () use ($data, $jenis, $divisiId, $cfg) {
            $d = Dokumen::create(collect($data)->except('pihak', 'divisi_id')->all()
                + ['jenis' => $jenis, 'divisi_id' => $divisiId, 'dibuat_oleh' => auth()->id()]);
            if ($cfg['pihak']) $this->simpanPihak($d, $data['pihak'] ?? []);
            return $d;
        });

        Aktivitas::catat('tambah', $cfg['label'], "Menambah {$cfg['label']}: {$dokumen->no_dokumen} - {$dokumen->nama_dokumen}", $dokumen->id);
        return redirect()->route('dokumen.index', $jenis)->with('sukses', "{$cfg['label']} berhasil ditambahkan.");
    }

    public function show(string $jenis, Dokumen $dokumen)
    {
        $cfg = $this->cfg($jenis);
        $this->otorisasi($dokumen, $jenis);
        $dokumen->load('divisi', 'pihaks', 'pembuat');

        Aktivitas::catat('lihat', $cfg['label'], "Membuka detail {$cfg['label']}: {$dokumen->no_dokumen}", $dokumen->id);
        return view('dokumen.show', compact('jenis', 'cfg', 'dokumen'));
    }

    public function edit(string $jenis, Dokumen $dokumen)
    {
        $cfg = $this->cfg($jenis);
        $this->otorisasi($dokumen, $jenis);
        $dokumen->load('pihaks');

        return view('dokumen.form', [
            'jenis' => $jenis, 'cfg' => $cfg, 'dokumen' => $dokumen,
            'statuses' => config('dokumen.status'), 'divisis' => Divisi::orderBy('nama_divisi')->get(),
        ]);
    }

    public function update(Request $r, string $jenis, Dokumen $dokumen)
    {
        $cfg = $this->cfg($jenis);
        $this->otorisasi($dokumen, $jenis);
        $data = $r->validate($this->rules($r, $jenis, $cfg, $dokumen->id));

        DB::transaction(function () use ($data, $dokumen, $r, $cfg) {
            $attrs = collect($data)->except('pihak', 'divisi_id')->all() + ['jenis' => $dokumen->jenis, 'diubah_oleh' => auth()->id()]; // jenis dirapikan ke slug
            if ($r->user()->lihatSemuaDivisi()) $attrs['divisi_id'] = $data['divisi_id'];
            if ($cfg['tanggal'] !== 'range') $attrs['tanggal_akhir'] = null;
            $dokumen->update($attrs);
            if ($cfg['pihak']) $this->simpanPihak($dokumen, $data['pihak'] ?? []);
        });

        Aktivitas::catat('ubah', $cfg['label'], "Mengubah {$cfg['label']}: {$dokumen->no_dokumen} - {$dokumen->nama_dokumen}", $dokumen->id);
        return redirect()->route('dokumen.index', $jenis)->with('sukses', "{$cfg['label']} berhasil diperbarui.");
    }

    public function destroy(string $jenis, Dokumen $dokumen)
    {
        $cfg = $this->cfg($jenis);
        $this->otorisasi($dokumen, $jenis);
        $dokumen->update(['diubah_oleh' => auth()->id()]);
        $dokumen->delete(); // soft delete

        Aktivitas::catat('hapus', $cfg['label'], "Menghapus {$cfg['label']}: {$dokumen->no_dokumen} - {$dokumen->nama_dokumen}", $dokumen->id);
        return redirect()->route('dokumen.index', $jenis)->with('sukses', "{$cfg['label']} berhasil dihapus.");
    }

    /** Dipanggil via fetch saat modal preview PDF dibuka. */
    public function catatPreview(Dokumen $dokumen)
    {
        $this->otorisasi($dokumen);
        Aktivitas::catat('preview', $dokumen->config['label'] ?? 'Dokumen', "Preview dokumen: {$dokumen->no_dokumen} - {$dokumen->nama_dokumen}", $dokumen->id);
        return response()->noContent();
    }

    // ------------------------------------------------------------------

    private function rules(Request $r, string $jenis, array $cfg, ?int $ignoreId = null): array
    {
        $rules = [
            'status'       => ['required', Rule::in(config('dokumen.status'))],
            'no_dokumen'   => ['required', 'string', 'max:255',
                Rule::unique('dokumens', 'no_dokumen')->where(fn ($q) => $q->whereIn('jenis', Dokumen::nilaiJenis($jenis)))->whereNull('deleted_at')->ignore($ignoreId)],
            'nama_dokumen' => ['required', 'string', 'max:255'],
            'tanggal_awal' => ['required', 'date'],
            'lokasi'       => ['nullable', 'string', 'max:255'],
            'catatan'      => ['nullable', 'string'],
            'link_dokumen' => ['nullable', 'url', 'max:1000'],
            'rangkuman'    => ['nullable', 'string'],
        ];

        if ($cfg['tanggal'] === 'range') {
            $rules['tanggal_akhir'] = ['required', 'date', 'after_or_equal:tanggal_awal'];
        }
        if ($r->user()->lihatSemuaDivisi()) {
            $rules['divisi_id'] = ['required', 'exists:indukk.divisis,id'];
        }
        if ($cfg['pihak']) {
            $rules['pihak'] = ['nullable', 'array'];
            $rules['pihak.*.nama'] = ['required', 'string', 'max:255'];
            $rules['pihak.*.jabatan'] = ['nullable', 'string', 'max:255'];
        }
        return $rules;
    }

    private function tentukanDivisi(Request $r, array $data): int
    {
        $u = $r->user();
        $id = $u->lihatSemuaDivisi() ? ($data['divisi_id'] ?? null) : $u->divisi_id;
        abort_unless($id, 403, 'Akun Anda belum terhubung ke divisi. Hubungi administrator.');
        return (int) $id;
    }

    private function simpanPihak(Dokumen $d, array $pihak): void
    {
        $d->pihaks()->delete();
        foreach (array_values($pihak) as $i => $p) {
            $d->pihaks()->create(['urutan' => $i + 1, 'nama' => $p['nama'], 'jabatan' => $p['jabatan'] ?? null]);
        }
    }
}
