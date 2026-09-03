<?php

/*
 | Akun admin tunggal. Dibaca lewat config, bukan env() langsung, supaya
 | seeder tetap benar saat konfigurasi di-cache di server (Fase 6).
 */
return [
    'name' => env('ADMIN_NAME', 'Admin'),
    'email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'password' => env('ADMIN_PASSWORD', 'password'),
];
