<?php

namespace App\Models;

use App\Services\TurunanGambar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tulisan journal.
 *
 * Nama kelas dan relasi mengikuti konvensi Laravel, nama kolom bahasa
 * Indonesia sesuai docs/PRD.md bagian 6.
 */
class Post extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'cover',
        'tautan_project',
        'label_tautan',
        'status',
        'dipin',
        'terbit_pada',
    ];

    protected function casts(): array
    {
        return [
            'dipin' => 'boolean',
            'terbit_pada' => 'datetime',
        ];
    }

    /**
     * Project yang menautkan dirinya ke tulisan ini.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Hanya tulisan berstatus terbit. Draf tidak boleh bisa diakses lewat
     * URL publik, jadi halaman publik (Fase 2B) wajib menyaring lewat
     * scope ini di query, bukan menyembunyikannya di tampilan saja.
     */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', 'terbit');
    }

    /**
     * URL turunan thumb sampul, dipakai kartu daftar journal. `cover`
     * menyimpan path thumb secara langsung, lihat PipelineGambar.
     */
    public function sampulUrl(): ?string
    {
        return $this->sampulUrls()['thumb'] ?? null;
    }

    /**
     * Ketiga turunan sampul sekaligus (thumb/sedang/penuh), diturunkan
     * dari path yang tersimpan lewat TurunanGambar. sampulUrl() di atas
     * tinggal mengambil satu darinya, supaya logikanya satu tempat.
     *
     * @return array<string, string>|null
     */
    public function sampulUrls(): ?array
    {
        return $this->cover ? TurunanGambar::urlDari($this->cover) : null;
    }
}
