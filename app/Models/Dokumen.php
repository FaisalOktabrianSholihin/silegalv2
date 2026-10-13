<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dokumen extends Model
{
    use SoftDeletes;

    protected $table = 'dokumens';

    protected $fillable = [
        'jenis', 'divisi_id', 'status', 'no_dokumen', 'nama_dokumen', 'tanggal_awal', 'tanggal_akhir',
        'lokasi', 'catatan', 'link_dokumen', 'rangkuman', 'dibuat_oleh', 'diubah_oleh',
    ];

    protected function casts(): array
    {
        return ['tanggal_awal' => 'date', 'tanggal_akhir' => 'date'];
    }

    public function divisi()  { return $this->belongsTo(Divisi::class, 'divisi_id'); }
    public function pembuat() { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function pihaks()  { return $this->hasMany(DokumenPihak::class)->orderBy('urutan'); }

    /**
     * Data lama di database menyimpan jenis sebagai LABEL ("BOM Meeting", "Surat Keputusan"),
     * sedangkan aplikasi memakai SLUG ("bom-meeting", "surat-keputusan").
     * Method ini mengubah keduanya menjadi slug.
     */
    public static function slugJenis(?string $nilai): string
    {
        $nilai = trim((string) $nilai);
        foreach (config('dokumen.jenis') as $slug => $c) {
            if (strcasecmp($nilai, $slug) === 0 || strcasecmp($nilai, $c['label']) === 0) return $slug;
        }
        return Str::slug($nilai);
    }

    /** Daftar nilai kolom `jenis` yang dianggap sama (slug + label). */
    public static function nilaiJenis(string $slug): array
    {
        return array_values(array_unique(array_filter([$slug, config("dokumen.jenis.$slug.label")])));
    }

    /** Selalu kembalikan slug, apa pun format yang tersimpan di database. */
    public function getJenisAttribute($value): string { return self::slugJenis($value); }

    public function scopeJenis($q, string $jenis) { return $q->whereIn('jenis', self::nilaiJenis($jenis)); }

    /** Pembatasan per divisi: user tanpa akses semua divisi hanya melihat divisinya sendiri. */
    public function scopeMilikUser($q, User $user)
    {
        if ($user->lihatSemuaDivisi()) return $q;
        return $user->divisi_id ? $q->where('divisi_id', $user->divisi_id) : $q->whereRaw('1 = 0');
    }

    public function getConfigAttribute(): array { return config("dokumen.jenis.{$this->jenis}", []); }

    public function getStatusEfektifAttribute(): string
    {
        if (in_array($this->status, ['Selesai', 'Dibatalkan'], true)) return $this->status;
        if ($this->tanggal_akhir) {
            if ($this->tanggal_akhir->lt(today())) return 'Kadaluarsa';
            if ($this->status === 'Aktif' && $this->tanggal_akhir->lte(today()->addDays(config('dokumen.warning_days')))) return 'Segera Berakhir';
        }
        return $this->status;
    }

    public function getSisaHariAttribute(): ?int
    {
        return $this->tanggal_akhir ? (int) round(today()->diffInDays($this->tanggal_akhir, false)) : null;
    }

    /** Ubah link Google Drive / Docs menjadi URL yang bisa di-embed di iframe. */
    public function getLinkEmbedAttribute(): ?string
    {
        $url = $this->link_dokumen;
        if (! $url) return null;

        $host = parse_url($url, PHP_URL_HOST) ?: '';
        if (str_contains($host, 'drive.google.com')) {
            if (preg_match('#/d/([\w-]+)#', $url, $m) || preg_match('#[?&]id=([\w-]+)#', $url, $m)) {
                return "https://drive.google.com/file/d/{$m[1]}/preview";
            }
        }
        if (str_contains($host, 'docs.google.com') && preg_match('#/(document|spreadsheets|presentation)/d/([\w-]+)#', $url, $m)) {
            return "https://docs.google.com/{$m[1]}/d/{$m[2]}/preview";
        }
        return $url;
    }
}
