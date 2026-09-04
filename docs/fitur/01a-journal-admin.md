# Fitur 01a — Panel Admin dan Pipeline Media Journal

Fase roadmap: 2A. Bagian dari Fitur 01 — Journal dan Panel Penulisan, dipecah dari `01-journal.md` supaya satu fase tetap satu PR yang wajar ukurannya. Baca bersama `01b-journal-publik.md`.

## Tujuan
Pemilik bisa menulis artikel lengkap dari HP tanpa membuka VS Code, dan menyimpannya sebagai draf atau langsung menerbitkannya.

## Panel admin
- Resource Filament untuk `posts`.
- Editor teks kaya dengan: heading, tebal, miring, daftar, kutipan, blok kode, tautan.
- Unggah gambar di tengah tulisan langsung dari editor.
- Sematan video YouTube. Pemilik cukup menempel tautan, sistem menyimpannya dalam bentuk terstruktur yang nanti dirender sebagai iframe di halaman publik (Fase 2B).
- Status draf dan terbit. Draf tidak boleh bisa diakses lewat URL publik.
- Penanda `dipin` untuk tampil di beranda.
- Kolom `tautan_project` dan `label_tautan`, keduanya opsional.

## Pipeline media
- Kompres dan konversi ke WebP otomatis saat unggah.
- Tiga ukuran turunan: thumbnail, sedang, penuh.
- Batas 10 MB per berkas.
- Berkas disimpan di `storage`, bukan di basis data.

## Di luar cakupan
Halaman publik `/journal` dan `/journal/{slug}`. Itu Fitur 01b.

## Kriteria lolos
Satu artikel percobaan berisi teks, dua gambar, dan satu tautan YouTube ditulis dari `/admin`, disimpan sebagai draf, lalu sebagai terbit. Setelah unggah, tiga berkas turunan WebP benar-benar ada di storage untuk tiap gambar.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: `migrate:fresh --seed` berjalan bersih.
