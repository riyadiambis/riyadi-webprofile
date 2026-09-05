<?php

namespace App\Models;

use App\Services\TurunanGambar;
use Illuminate\Database\Eloquent\Model;

/**
 * Foto galeri.
 *
 * `gambar` menyimpan basis nama — direktori UUID tempat ketiga
 * turunan berada, tanpa nama turunan. Ketiga URL disusun oleh
 * TurunanGambar, bukan diturunkan lewat manipulasi string di sini.
 * Alasan lengkapnya di docs/keputusan.md (keputusan Fase 4).
 */
class Photo extends Model
{
    protected $fillable = [
        'gambar',
        'caption',
        'diambil_pada',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'diambil_pada' => 'date',
        ];
    }

    /**
     * Ketiga turunan gambar sekaligus (thumb/sedang/penuh),
     * disusun TurunanGambar dari basis nama yang tersimpan.
     * Grid publik memakai thumb, lightbox memakai penuh.
     *
     * @return array<string, string>
     */
    public function gambarUrls(): array
    {
        return TurunanGambar::urlDariBasis($this->gambar);
    }
}
