# Fitur 05 — Dwibahasa Terbatas

Fase roadmap: 5, dikerjakan bersama beranda dalam satu branch dan satu pull request.

## Cakupan
Teks perkenalan dan penutup di beranda, serta ringkasan project.

Tidak mencakup nama project, isi artikel journal, dan caption galeri. Ini keputusan sadar, bukan kelalaian. Kolom `nama_en` tidak dibuat.

## Aturan
- Pengisian sepenuhnya manual. Dilarang memanggil layanan penerjemah apa pun.
- Di panel admin, teks dwibahasa ditampilkan sebagai dua kolom bersebelahan, Indonesia dan Inggris.
- Tombol pengalih bahasa di header, hanya ID dan EN. Pilihan disimpan di cookie.
- Jika versi Inggris kosong, tampilkan versi Indonesia sebagai cadangan. Jangan tampilkan bagian kosong.
- Di panel admin, tandai baris yang versi Inggrisnya masih kosong.
- Halaman journal dan galeri tidak berubah saat bahasa dialihkan.

## Kriteria lolos
Menekan pengalih bahasa mengubah teks perkenalan dan ringkasan project, nama project tetap sama, tata letak tetap rapi, dan project yang ringkasannya belum diterjemahkan tetap tampil dalam bahasa Indonesia.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: `/` membalas 200 dalam kedua pilihan bahasa, dan `migrate:fresh --seed` berjalan bersih.
