<?php

namespace App\Models;

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
}
