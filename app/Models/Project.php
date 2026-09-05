<?php

namespace App\Models;

use App\Services\TurunanGambar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Project.
 *
 * Kolom `nama` sengaja tidak punya versi Inggris. Yang dwibahasa hanya
 * `ringkasan`, lihat docs/PRD.md bagian 7.6.
 */
class Project extends Model
{
    protected $fillable = [
        'nama',
        'ringkasan',
        'ringkasan_en',
        'gambar',
        'tahun',
        'post_id',
        'dipin',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'gambar' => 'array',
            'dipin' => 'boolean',
        ];
    }

    /**
     * Tulisan journal yang dituju saat kartu project diklik.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Kartu publik hanya boleh diklik kalau ada tulisan tertaut DAN
     * tulisan itu sudah terbit. Post draf tetap bisa dipilih di panel
     * admin (persiapan sebelum artikelnya terbit), tapi tautan ke
     * draf lewat /journal/{slug} akan 404 di publik (lihat Fase 2B),
     * jadi kartunya tetap dianggap "segera ditulis" sampai post itu
     * benar-benar terbit. Alasan lengkapnya di docs/keputusan.md.
     */
    public function bisaDiklik(): bool
    {
        return filled($this->post_id) && $this->post?->status === 'terbit';
    }

    /**
     * Ketiga turunan tiap gambar (thumb/sedang/penuh), diturunkan lewat
     * TurunanGambar dari satu path yang tersimpan per gambar. Kartu
     * grid memakai thumb; sedang/penuh tetap tersedia dari sini untuk
     * kebutuhan nanti, tanpa perlu menyimpan tiga path per gambar.
     *
     * @return array<int, array<string, string>>
     */
    public function gambarUrls(): array
    {
        return collect($this->gambar ?? [])
            ->map(fn (string $path) => TurunanGambar::urlDari($path))
            ->all();
    }

    /**
     * Teks ringkasan yang ditampilkan, dengan cadangan ke bahasa
     * Indonesia. Titik tunggal dwibahasa project sejak Fase 3; Fase 5
     * menyambungkan pengalih bahasa ke sini lewat app()->getLocale()
     * yang ditetapkan middleware SetLocale — kartu di beranda maupun
     * di halaman /project sama-sama lewat sini, tanpa logika kedua.
     * Nama project tidak diterjemahkan, lihat docs/PRD.md bagian 7.6.
     */
    public function ringkasanTampil(?string $bahasa = null): string
    {
        $bahasa ??= app()->getLocale();

        if ($bahasa === 'en' && filled($this->ringkasan_en)) {
            return $this->ringkasan_en;
        }

        return $this->ringkasan;
    }
}
