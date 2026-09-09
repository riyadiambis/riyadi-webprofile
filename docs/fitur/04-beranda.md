# Fitur 04 — Beranda

Fase roadmap: 5. Dikerjakan setelah tiga fitur konten selesai karena memanggil datanya.

## Susunan dari atas ke bawah
1. Perkenalan: foto, nama, satu paragraf tentang diri, tautan ke email, LinkedIn, GitHub, TikTok, Instagram, YouTube.
2. Project unggulan: slider horizontal berisi project yang `dipin`.
3. Tulisan unggulan: slider horizontal berisi post yang `dipin`.
4. Galeri: slider horizontal berisi foto terbaru.
5. Penutup: ajakan menghubungi dan tautan email.

## Perilaku slider
- Bisa digeser dengan sentuhan di HP dan tombol panah di desktop.
- Tidak bergerak sendiri. Hanya kartu project yang gambarnya berganti otomatis.
- Setiap bagian punya tautan "lihat semua" ke halaman penuhnya.
- Jika data pin kosong, sembunyikan seluruh bagian itu, jangan tampilkan bagian kosong.

## Teks
Paragraf perkenalan dan penutup diambil dari tabel `site_texts`, bukan ditulis keras di Blade. Hanya ada dua kunci di v1: `perkenalan` dan `penutup`.

Judul bagian dan label kecil di atasnya ("Project unggulan", "Unggulan", "Lihat semua", dan seterusnya) ikut dwibahasa lewat `lang/id/beranda.php` dan `lang/en/beranda.php` — string antarmuka, bukan isi yang disunting pemilik. Lihat `05-dwibahasa.md`.

## Profil
Foto profil dan keenam tautan sosial diambil dari tabel `profiles` (satu baris), disunting lewat halaman Beranda di panel — lihat `08-panel-beranda.md`. Tautan yang dikosongkan **tidak dirender**: tombolnya hilang, bukan tampil mati. Kalau foto belum diunggah, beranda menampilkan kotak inisial.

## Kriteria lolos
Menandai pin pada satu project dari panel admin langsung mengubah isi beranda tanpa deploy ulang.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: `/` membalas 200 dalam kedua pilihan bahasa, dan `migrate:fresh --seed` berjalan bersih.
