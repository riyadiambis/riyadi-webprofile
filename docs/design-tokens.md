# Design Tokens

Turunan gaya "Buku Tulis" milik pemilik, disesuaikan agar terbaca dewasa dan profesional. Seluruh nilai di bawah ini adalah satu-satunya sumber kebenaran visual. Daftarkan ke `resources/css/app.css` di dalam blok `@theme`, jangan tulis ulang di Blade. Tailwind v4 memakai konfigurasi berbasis CSS, bukan `tailwind.config.js`.

## Warna

| Nama token | Nilai | Pemakaian |
|---|---|---|
| paper | #FDFCF8 | latar halaman |
| grid | #E9E5DC | garis kertas, sangat tipis |
| ink | #1C2B3A | teks utama, border, judul |
| ink-soft | #5C6B7A | teks sekunder, label |
| card | #FFFFFF | latar kartu |
| line | #C9C2B4 | warna bayangan offset |
| accent | #F2C744 | aksen stabilo dan tombol utama, dipakai irit |
| accent-alt | #2F7D6E | aksen kedua, tautan aktif dan badge |
| danger | #C0483A | pesan galat |

## Tipografi

- Judul dan angka: Fraunces, bobot 500 sampai 700.
- Teks isi dan antarmuka: Nunito Sans, bobot 400 sampai 700.
- Label bagian: huruf besar, jarak huruf 1,2px, ukuran 13px, warna ink-soft.
- Kolom baca artikel dibatasi sekitar 70 karakter.

## Bentuk dan bayangan

- Border 1,5px solid ink pada kartu dan tombol.
- Radius 10px sampai 12px.
- Bayangan offset solid tanpa blur: `3px 3px 0 var(--line)`.
- Interaksi tekan: elemen bergeser ke arah bayangan dan bayangan mengecil.

## Latar

Dua repeating-linear-gradient bersilangan warna grid, ukuran 32px, opasitas rendah. Kesan yang dituju adalah tekstur kertas, bukan buku kotak-kotak yang mencolok.

## Tata letak

- Lebar konten maksimal 1100px, desktop-first, wajib rapi di layar HP.
- Jarak antar bagian besar: 96px di desktop, 56px di mobile.
- Aksen stabilo hanya untuk satu sampai dua kata per halaman, dengan `linear-gradient(transparent 58%, var(--accent) 58%)`.

## Yang harus dihindari

- Bayangan lembut berblur.
- Lebih dari dua warna aksen dalam satu halaman.
- Font bulat kekanak-kanakan seperti Baloo 2. Gaya versi ini sengaja lebih tenang.
