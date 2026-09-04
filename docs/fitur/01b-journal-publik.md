# Fitur 01b — Halaman Publik Journal

Fase roadmap: 2B. Bagian dari Fitur 01 — Journal dan Panel Penulisan, dipecah dari `01-journal.md` supaya satu fase tetap satu PR yang wajar ukurannya. Baca bersama `01a-journal-admin.md`, yang harus sudah selesai dan ter-merge lebih dulu.

## Tujuan
Artikel yang ditulis lewat panel admin tampil rapi di halaman publik, termasuk gambar dan video yang bisa diputar.

## Halaman publik
- `/journal`: kartu berisi cover, judul, ringkasan, tanggal. Urut terbaru. Paginasi sederhana.
- `/journal/{slug}`: jika `tautan_project` terisi, tampilkan tombolnya di bagian paling atas.
- Sematan YouTube yang disimpan di Fase 2A dirender sebagai iframe yang bisa diputar.
- Lebar kolom baca sekitar 70 karakter.
- Gambar dalam tulisan bisa diklik untuk diperbesar.

## Di luar cakupan
Panel admin dan pipeline media. Itu sudah selesai di Fitur 01a.

## Kriteria lolos
Artikel percobaan dari Fase 2A tampil benar di halaman publik, lengkap dengan dua gambar dan video YouTube yang bisa diputar.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: `/journal` dan `/journal/{slug}` membalas 200, dan `migrate:fresh --seed` berjalan bersih.
