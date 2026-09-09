# Fitur 05 — Dwibahasa Terbatas

Fase roadmap: 5, dikerjakan bersama beranda dalam satu branch dan satu pull request.

## Cakupan
Teks perkenalan dan penutup di beranda, ringkasan project, serta judul bagian dan label di halaman beranda.

Dua sumber berbeda, sengaja dibedakan:

- **Isi yang disunting pemilik** (perkenalan, penutup, ringkasan project) ada di basis data, dengan kolom `_en` di sebelah kolom Indonesianya. Kalau versi Inggris kosong, tampilkan versi Indonesia sebagai cadangan.
- **String antarmuka beranda** (judul bagian, label kecil di atasnya, tombol "Lihat semua" dan "Kirim email") ada di `lang/id/beranda.php` dan `lang/en/beranda.php`. Ini bukan isi yang disunting pemilik, jadi tidak masuk basis data. Berbeda dari isi di atas, kunci yang belum diterjemahkan **tidak** jatuh ke bahasa Indonesia — Laravel menampilkan nama kuncinya mentah, jadi kedua berkas wajib punya kunci yang sama persis.

Cakupan `lang/` sengaja dibatasi pada beranda saja. Navigasi header, halaman project, journal, dan galeri tetap berbahasa Indonesia di kedua pilihan bahasa; halaman-halaman itu bahkan mengunci `<html lang="id">`. Menambah kunci untuk halaman lain berarti memperluas cakupan dwibahasa — perbarui dokumen ini dan `PRD.md` bagian 7.6 lebih dulu, jangan diam-diam.

Tidak mencakup nama project, isi artikel journal, dan caption galeri. Ini keputusan sadar, bukan kelalaian. Kolom `nama_en` tidak dibuat.

## Aturan
- Pengisian sepenuhnya manual. Dilarang memanggil layanan penerjemah apa pun.
- Di panel admin, teks dwibahasa ditampilkan sebagai dua kolom bersebelahan, Indonesia dan Inggris.
- Tombol pengalih bahasa di header, hanya ID dan EN. Pilihan disimpan di cookie.
- Jika versi Inggris kosong, tampilkan versi Indonesia sebagai cadangan. Jangan tampilkan bagian kosong.
- Di panel admin, tandai baris yang versi Inggrisnya masih kosong.
- Halaman journal dan galeri tidak berubah saat bahasa dialihkan.

## Kriteria lolos
Menekan pengalih bahasa mengubah teks perkenalan, ringkasan project, dan judul bagian beranda sekaligus; nama project tetap sama, tata letak tetap rapi, dan project yang ringkasannya belum diterjemahkan tetap tampil dalam bahasa Indonesia. Tidak ada lagi judul bagian berbahasa Indonesia di atas isi yang sudah berbahasa Inggris.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: `/` membalas 200 dalam kedua pilihan bahasa, dan `migrate:fresh --seed` berjalan bersih.
