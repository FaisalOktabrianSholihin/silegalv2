<?php

namespace App\Http\Controllers;

use App\Models\{Dokumen, JenisDokumen};
use App\Support\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JenisDokumenController extends Controller
{
    public function index()
    {
        $data = JenisDokumen::orderBy('urutan')->orderBy('id')->get()
            ->each(fn ($j) => $j->jumlah_dokumen = Dokumen::jenis($j->slug)->count());

        return view('jenis-dokumen.index', compact('data'));
    }

    public function create()
    {
        $jenis = new JenisDokumen([
            'icon' => 'file-text', 'warna' => 'brand', 'tanggal' => 'single',
            'pakai_pihak' => false, 'pakai_lokasi' => true, 'pakai_catatan' => true, 'pakai_link' => true, 'pakai_rangkuman' => true,
            'aktif' => true, 'urutan' => (int) JenisDokumen::max('urutan') + 1,
        ]);
        return view('jenis-dokumen.form', compact('jenis'));
    }

    public function store(Request $r)
    {
        $data = $this->validasi($r);
        $data['slug'] = $this->slugUnik($data['label']);
        $jenis = JenisDokumen::create($data);

        Aktivitas::catat('tambah', 'Jenis Dokumen', "Menambah jenis dokumen: {$jenis->label}", $jenis->id);
        return redirect()->route('jenis-dokumen.index')->with('sukses', "Jenis dokumen {$jenis->label} berhasil ditambahkan.");
    }

    public function edit(JenisDokumen $jenis)
    {
        return view('jenis-dokumen.form', ['jenis' => $jenis, 'jumlah' => Dokumen::jenis($jenis->slug)->count()]);
    }

    public function update(Request $r, JenisDokumen $jenis)
    {
        $data = $this->validasi($r, $jenis->id);
        $jenis->update($data); // slug tidak diubah agar URL & data dokumen tetap terhubung

        Aktivitas::catat('ubah', 'Jenis Dokumen', "Mengubah jenis dokumen: {$jenis->label}", $jenis->id);
        return redirect()->route('jenis-dokumen.index')->with('sukses', "Jenis dokumen {$jenis->label} berhasil diperbarui.");
    }

    public function destroy(JenisDokumen $jenis)
    {
        $jumlah = Dokumen::withTrashed()->jenis($jenis->slug)->count();
        if ($jumlah > 0) {
            return back()->with('gagal', "Jenis {$jenis->label} masih dipakai {$jumlah} dokumen dan tidak dapat dihapus. Nonaktifkan saja jika tidak ingin dipakai lagi.");
        }
        $jenis->delete();

        Aktivitas::catat('hapus', 'Jenis Dokumen', "Menghapus jenis dokumen: {$jenis->label}", $jenis->id);
        return back()->with('sukses', "Jenis dokumen {$jenis->label} berhasil dihapus.");
    }

    // ------------------------------------------------------------------

    private function validasi(Request $r, ?int $ignoreId = null): array
    {
        $data = $r->validate([
            'label'   => ['required', 'string', 'max:60', Rule::unique('jenis_dokumens', 'label')->ignore($ignoreId)],
            'icon'    => ['required', Rule::in(config('dokumen.icon_pilihan'))],
            'warna'   => ['required', Rule::in(array_keys(config('dokumen.warna_hex')))],
            'tanggal' => ['required', Rule::in(['single', 'range'])],
            'urutan'  => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        foreach (['pakai_pihak', 'pakai_lokasi', 'pakai_catatan', 'pakai_link', 'pakai_rangkuman', 'aktif'] as $k) {
            $data[$k] = $r->boolean($k);
        }
        $data['urutan'] = (int) ($data['urutan'] ?? 0);
        return $data;
    }

    private function slugUnik(string $label): string
    {
        $dasar = trim(substr(Str::slug($label), 0, 24), '-') ?: 'jenis';
        $slug = $dasar; $i = 2;
        while (JenisDokumen::where('slug', $slug)->exists()) $slug = $dasar . '-' . $i++;
        return $slug;
    }
}
