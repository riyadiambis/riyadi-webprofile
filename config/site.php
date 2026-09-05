<?php

/*
 | Isian profil pemilik situs, dipakai beranda (Fase 5).
 |
 | SELURUH nilai di bawah ini masih isian sementara dan harus diganti
 | sebelum peluncuran:
 |
 | - foto_profil: path berkas di disk public relatif terhadap
 |   storage/app/public, misalnya 'profil/foto.webp'. null berarti
 |   beranda menampilkan kotak inisial berlabel "Foto sementara".
 | - sosial: URL asli LinkedIn, GitHub, TikTok, dan Instagram.
 | - email: alamat email asli.
 |
 | Domain contoh.invalid sengaja dipakai karena TLD khusus
 | placeholder — tidak bisa di-resolve dan jelas terlihat bukan
 | alamat asli.
 */

return [
    'foto_profil' => null,

    'sosial' => [
        'linkedin' => 'https://contoh.invalid/linkedin',
        'github' => 'https://contoh.invalid/github',
        'tiktok' => 'https://contoh.invalid/tiktok',
        'instagram' => 'https://contoh.invalid/instagram',
    ],

    'email' => 'contoh@contoh.invalid',
];
