# Fitur 03 — Galeri

Fase roadmap: 4

## Panel admin
- Resource Filament untuk `photos`.
- Unggah banyak berkas sekaligus.
- Caption pendek opsional dan tanggal pengambilan opsional.
- Pengurutan lewat kolom `urutan`.

## Halaman publik `/galeri`
- Grid rapat bergaya Instagram.
- Foto diklik membuka tampilan besar berisi foto dan caption.
- Bisa dinavigasi maju mundur lewat tombol, tombol panah keyboard, dan geser di layar sentuh.
- Pemuatan bertahap saat digulir, memakai ukuran thumbnail di grid.

## Rincian pelaksanaan
- Kolom `photos.gambar` menyimpan basis nama (direktori UUID tanpa nama turunan); ketiga URL disusun TurunanGambar. Alasannya di `docs/keputusan.md`.
- Overlay lightbox dipasang sekali di layout publik dan dipakai bersama gambar artikel journal (Fase 2B). Foto galeri menambah tombol maju/mundur, panah keyboard, geser sentuh, dan caption; gambar artikel tetap tanpa keempatnya.
- Grid memuat thumbnail bertahap: 8 ubin pertama `loading="eager"`, sisanya `loading="lazy"` native. Ukuran penuh baru dimuat saat foto dibuka.

## Kriteria lolos
Dua puluh foto percobaan terbuka mulus di HP, grid hanya memuat ukuran kecil, ukuran penuh baru dimuat saat foto dibuka.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: `/galeri` membalas 200, dan `migrate:fresh --seed` berjalan bersih.
