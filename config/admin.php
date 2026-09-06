<?php

/*
 | Akun admin tunggal. Dibaca lewat config, bukan env() langsung, supaya
 | seeder tetap benar saat konfigurasi di-cache di server (Fase 6).
 |
 | 'password' SENGAJA tidak punya fallback. AdminSeeder gagal dengan
 | pesan jelas kalau ADMIN_PASSWORD kosong, daripada diam-diam membuat
 | akun bersandi lemah yang bisa ditebak. Lihat docs/keputusan.md.
 */
return [
    'name' => env('ADMIN_NAME', 'Admin'),
    'email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'password' => env('ADMIN_PASSWORD'),
];
