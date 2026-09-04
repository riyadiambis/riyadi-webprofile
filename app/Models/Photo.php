<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Foto galeri.
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
}
