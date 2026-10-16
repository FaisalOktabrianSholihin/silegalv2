<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    protected $table = 'jenis_dokumens';

    protected $fillable = [
        'slug', 'label', 'icon', 'warna', 'tanggal',
        'pakai_pihak', 'pakai_lokasi', 'pakai_catatan', 'pakai_link', 'pakai_rangkuman',
        'aktif', 'urutan',
    ];

    protected function casts(): array
    {
        return [
            'pakai_pihak' => 'boolean', 'pakai_lokasi' => 'boolean', 'pakai_catatan' => 'boolean',
            'pakai_link' => 'boolean', 'pakai_rangkuman' => 'boolean', 'aktif' => 'boolean', 'urutan' => 'integer',
        ];
    }

    private static ?array $cache = null;

    protected static function booted(): void
    {
        static::saved(fn () => self::$cache = null);
        static::deleted(fn () => self::$cache = null);
    }

    /** Bentuk array yang dipakai seluruh view & controller (kompatibel dengan config lama). */
    public function toCfg(): array
    {
        return [
            'id' => $this->id, 'slug' => $this->slug, 'label' => $this->label, 'icon' => $this->icon, 'warna' => $this->warna,
            'tanggal' => $this->tanggal, 'pihak' => $this->pakai_pihak, 'lokasi' => $this->pakai_lokasi,
            'catatan' => $this->pakai_catatan, 'link' => $this->pakai_link, 'rangkuman' => $this->pakai_rangkuman,
            'aktif' => $this->aktif,
        ];
    }

    /** Semua jenis (slug => cfg), terurut. Jika tabel belum dimigrasi, pakai jenis bawaan di config. */
    public static function map(): array
    {
        if (self::$cache !== null) return self::$cache;

        try {
            return self::$cache = self::orderBy('urutan')->orderBy('id')->get()
                ->mapWithKeys(fn ($j) => [$j->slug => $j->toCfg()])->all();
        } catch (\Throwable $e) {
            return self::$cache = collect(config('dokumen.jenis'))->map(fn ($c, $slug) => $c + [
                'id' => null, 'slug' => $slug, 'lokasi' => true, 'catatan' => true, 'link' => true, 'rangkuman' => true, 'aktif' => true,
            ])->all();
        }
    }

    public static function cfg(string $slug): ?array
    {
        return self::map()[$slug] ?? null;
    }

    /** Hanya jenis yang aktif (untuk menu & tombol tambah). */
    public static function aktif(): array
    {
        return array_filter(self::map(), fn ($c) => $c['aktif']);
    }

    /** Status yang boleh dipilih untuk sebuah jenis. Jenis 1 tanggal tidak punya "Kadaluarsa". */
    public static function statusUntuk(array $cfg): array
    {
        $semua = config('dokumen.status');
        return $cfg['tanggal'] === 'range'
            ? $semua
            : array_values(array_diff($semua, config('dokumen.status_tanpa_masa_berlaku')));
    }
}
