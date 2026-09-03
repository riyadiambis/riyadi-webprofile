# Fitur 04 — Beranda

Fase roadmap: 5. Dikerjakan setelah tiga fitur konten selesai karena memanggil datanya.

## Susunan dari atas ke bawah
1. Perkenalan: foto, nama, satu paragraf tentang diri, tautan ke LinkedIn, GitHub, TikTok, Instagram, email.
2. Project pilihan: slider horizontal berisi project yang `dipin`.
3. Tulisan pilihan: slider horizontal berisi post yang `dipin`.
4. Galeri: slider horizontal berisi foto terbaru.
5. Penutup: ajakan menghubungi dan tautan email.

## Perilaku slider
- Bisa digeser dengan sentuhan di HP dan tombol panah di desktop.
- Tidak bergerak sendiri. Hanya kartu project yang gambarnya berganti otomatis.
- Setiap bagian punya tautan "lihat semua" ke halaman penuhnya.
- Jika data pin kosong, sembunyikan seluruh bagian itu, jangan tampilkan bagian kosong.

## Teks
Paragraf perkenalan dan penutup diambil dari tabel `site_texts`, bukan ditulis keras di Blade.

## Kriteria lolos
Menandai pin pada satu project dari panel admin langsung mengubah isi beranda tanpa deploy ulang.
