<?php

namespace App\Models;

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
}
