# Fitur 02 — Project

Fase roadmap: 3

## Panel admin
- Resource Filament untuk `projects`.
- Unggah banyak gambar dalam satu project, urutannya bisa diatur.
- Pemilihan satu post yang ditautkan lewat `post_id`.
- Kolom `ringkasan` dan `ringkasan_en` bersebelahan dalam satu baris.
- Penanda `dipin` dan kolom `urutan`.

## Halaman publik `/project`
Grid kartu. Setiap kartu berisi gambar, nama, ringkasan, tahun.

Perilaku pergantian gambar:
- Kartu dengan lebih dari satu gambar menampilkan gambarnya bergantian otomatis, transisi lembut, jeda sekitar 3 detik.
- Jeda awal tiap kartu diberi selisih acak supaya tidak berganti serentak.
- Jika `prefers-reduced-motion` aktif, tampilkan gambar pertama saja tanpa animasi.
- Gambar berikutnya dimuat lebih dulu supaya tidak berkedip saat berganti.

Perilaku klik:
- Kartu menuju `/journal/{slug}` dari post yang tertaut.
- Kartu tanpa post tertaut tidak bisa diklik, diberi label "segera ditulis".

## Kriteria lolos
Dua project percobaan tampil, gambarnya berganti sendiri dan tidak serentak, dan klik kartu mendarat di tulisan yang benar.
