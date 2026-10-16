<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Membuat rangkuman dokumen (PDF) otomatis memakai Google Gemini API.
 * Dokumen diambil dari link_dokumen (Google Drive / URL PDF langsung) lalu dikirim sebagai PDF.
 */
class RangkumanAi
{
    private const MAKS_BYTE = 14 * 1024 * 1024; // batas request inline Gemini 20 MB (setelah base64)

    public static function tersedia(): bool
    {
        return filled(config('services.gemini.key'));
    }

    /** @throws RuntimeException berisi pesan yang aman ditampilkan ke pengguna */
    public static function buat(string $link, ?string $namaDokumen = null): string
    {
        if (! self::tersedia()) {
            throw new RuntimeException('Fitur AI belum aktif. Isi GEMINI_API_KEY di file .env.');
        }

        @set_time_limit(240); // unduh PDF + proses AI bisa memakan waktu
        $pdf = self::ambilPdf($link);

        $prompt = "Buatkan rangkuman dokumen legal ini dalam Bahasa Indonesia"
            . ($namaDokumen ? " (\"{$namaDokumen}\")" : '') . ".\n"
            . "Format: satu paragraf pembuka singkat (maks 3 kalimat) tentang isi dan tujuan dokumen, "
            . "lalu 3-6 poin penting (para pihak, objek/ruang lingkup, nilai atau kewajiban utama, jangka waktu, sanksi/ketentuan penting) "
            . "yang benar-benar ada di dokumen. Tiap poin diawali tanda '- '. "
            . "Jangan mengarang; jika suatu informasi tidak ada, lewati. Tanpa markdown tebal dan tanpa kalimat pengantar.";

        $model = config('services.gemini.model');
        $res = Http::withHeaders(['x-goog-api-key' => config('services.gemini.key')])
            ->timeout(120)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'contents' => [[
                    'parts' => [
                        ['inline_data' => ['mime_type' => 'application/pdf', 'data' => base64_encode($pdf)]],
                        ['text' => $prompt],
                    ],
                ]],
                'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 4000],
            ]);

        if ($res->failed()) {
            report(new RuntimeException('Gemini API: ' . $res->status() . ' ' . $res->body()));
            throw new RuntimeException(match (true) {
                in_array($res->status(), [400, 401, 403]) && str_contains($res->body(), 'API key') => 'API key Gemini tidak valid.',
                $res->status() === 404 => "Model Gemini \"{$model}\" tidak tersedia. Ganti GEMINI_MODEL di .env (mis. gemini-2.5-flash).",
                $res->status() === 429 => 'Kuota Gemini habis atau terlalu banyak permintaan. Coba beberapa saat lagi.',
                default => 'Layanan AI sedang tidak dapat dihubungi. Coba lagi atau isi rangkuman manual.',
            });
        }

        $teks = collect($res->json('candidates.0.content.parts', []))->pluck('text')->filter()->implode("\n");
        $teks = trim($teks);
        if ($teks === '') throw new RuntimeException('AI tidak menghasilkan rangkuman. Coba lagi atau isi manual.');

        return $teks;
    }

    private static function ambilPdf(string $link): string
    {
        $url = $link;
        $host = parse_url($link, PHP_URL_HOST) ?: '';
        if (str_contains($host, 'drive.google.com')
            && (preg_match('#/d/([\w-]+)#', $link, $m) || preg_match('#[?&]id=([\w-]+)#', $link, $m))) {
            $url = "https://drive.google.com/uc?export=download&id={$m[1]}";
        }

        try {
            $res = Http::timeout(60)->withOptions(['allow_redirects' => true])->get($url);
        } catch (\Throwable $e) {
            throw new RuntimeException('Link dokumen tidak dapat diakses dari server.');
        }

        $body = $res->body();
        if ($res->failed() || ! str_starts_with($body, '%PDF')) {
            throw new RuntimeException('Dokumen tidak dapat dibaca. Pastikan link berupa PDF dan aksesnya "Siapa saja yang memiliki link".');
        }
        if (strlen($body) > self::MAKS_BYTE) {
            throw new RuntimeException('Ukuran PDF terlalu besar untuk dirangkum otomatis (maks 14 MB).');
        }
        return $body;
    }
}
