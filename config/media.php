<?php

/*
 | Konfigurasi pipeline gambar. Dipakai oleh PipelineGambar untuk
 | sampul post dan gambar di dalam editor. Lihat docs/PRD.md bagian 8.
 */
return [
    'batas_unggah_kb' => 10 * 1024,

    'kualitas_webp' => 82,

    'turunan' => [
        'thumb' => 400,
        'sedang' => 900,
        'penuh' => 1600,
    ],

    'disk' => 'public',

    'direktori' => 'journal',
];
