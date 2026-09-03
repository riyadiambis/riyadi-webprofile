# Fitur 00 — Fondasi

Fase roadmap: 1

## Tujuan
Menyiapkan kerangka aplikasi supaya fitur berikutnya bisa dikerjakan tanpa menyentuh konfigurasi lagi.

## Cakupan
- Instalasi Laravel, konfigurasi SQLite, Tailwind, dan Filament dengan satu akun admin.
- Migrasi tabel `posts`, `projects`, `photos`, `site_texts` sesuai model data di PRD. Nama tabel dan model bahasa Inggris, nama kolom bahasa Indonesia.
- Pemasangan Pest sebagai kerangka smoke test.
- Pendaftaran seluruh nilai di `docs/design-tokens.md` ke `tailwind.config.js`.
- Layout publik: header dengan navigasi empat halaman, footer, latar kertas.
- Rute kosong untuk `/`, `/project`, `/journal`, `/galeri`.
- Seeder akun admin.

## Di luar cakupan
Isi halaman, resource Filament untuk konten, upload media.

## Kriteria lolos
- Keempat rute publik terbuka tanpa galat dan sudah memakai token desain.
- `/admin` bisa login dengan akun hasil seeder.
- `php artisan migrate:fresh --seed` berjalan bersih dari nol.
- Smoke test Pest terpasang dan hijau: keempat rute publik membalas 200, dan `migrate:fresh --seed` berjalan bersih. Aturan tetapnya di `CLAUDE.md`.
