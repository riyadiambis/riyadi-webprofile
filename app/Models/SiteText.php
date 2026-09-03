<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Teks beranda dwibahasa. Hanya dua kunci di v1: perkenalan dan penutup.
 *
 * Model bernama SiteText, tabelnya site_texts. Laravel menurunkan nama
 * tabel itu sendiri dari nama kelas, jadi tidak perlu $table.
 */
class SiteText extends Model
{
    protected $fillable = [
        'kunci',
        'nilai_id',
        'nilai_en',
    ];

    /**
     * Versi bahasa Inggris kalau ada, jatuh ke bahasa Indonesia kalau kosong.
     * Aturan cadangan ini dipakai Fase 5, lihat docs/fitur/05-dwibahasa.md.
     */
    public function nilai(string $bahasa = 'id'): string
    {
        if ($bahasa === 'en' && filled($this->nilai_en)) {
            return $this->nilai_en;
        }

        return $this->nilai_id;
    }
}
