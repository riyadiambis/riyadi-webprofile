# Fitur 01 — Journal dan Panel Penulisan

Fase roadmap: 2. Fitur paling penting di produk ini.

## Tujuan
Pemilik bisa menulis artikel lengkap dari HP tanpa membuka VS Code, lalu artikel itu tampil rapi di halaman publik.

## Panel admin
- Resource Filament untuk `posts`.
- Editor teks kaya dengan: heading, tebal, miring, daftar, kutipan, blok kode, tautan.
- Unggah gambar di tengah tulisan langsung dari editor.
- Sematan video YouTube. Pemilik cukup menempel tautan, sistem mengubahnya jadi iframe.
- Status draf dan terbit. Draf tidak boleh bisa diakses lewat URL publik.
- Penanda `dipin` untuk tampil di beranda.
- Kolom `tautan_project` dan `label_tautan`, keduanya opsional.

## Pipeline media
- Kompres dan konversi ke WebP otomatis saat unggah.
- Tiga ukuran turunan: thumbnail, sedang, penuh.
- Batas 10 MB per berkas.
- Berkas disimpan di `storage`, bukan di basis data.

## Halaman publik
- `/journal`: kartu berisi cover, judul, ringkasan, tanggal. Urut terbaru. Paginasi sederhana.
- `/journal/{slug}`: jika `tautan_project` terisi, tampilkan tombolnya di bagian paling atas.
- Lebar kolom baca sekitar 70 karakter.
- Gambar dalam tulisan bisa diklik untuk diperbesar.

## Kriteria lolos
Satu artikel percobaan berisi teks, dua gambar, dan satu video YouTube ditulis dari HP, diterbitkan, lalu tampil benar di halaman publik termasuk video yang bisa diputar.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: `/journal` dan `/journal/{slug}` membalas 200, dan `migrate:fresh --seed` berjalan bersih.
