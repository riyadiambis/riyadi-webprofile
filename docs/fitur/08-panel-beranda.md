# Fitur 08 — Rombak Panel Admin

Di luar fase roadmap: dikerjakan atas permintaan pemilik setelah Fase 5 dan perbaikan audit selesai. Satu branch (`panel-beranda`, tanpa awalan `fase-N` — lihat `../keputusan.md`) dan satu pull request.

## Halaman Beranda di panel

Satu menu tunggal bernama "Beranda" menampung **seluruh** isi halaman beranda:

- Foto profil, diproses pipeline gambar yang sudah ada.
- Enam tautan sosial: Email, LinkedIn, GitHub, TikTok, Instagram, YouTube. Tautan yang dikosongkan tidak dirender di beranda.
- Teks perkenalan dan penutup, Indonesia dan Inggris bersebelahan.

Aturan yang mengikat:

- **Tidak boleh ada dua menu untuk beranda.** Menu "Teks beranda" yang dulu terpisah dihapus; `SiteTextResource` beserta seluruh berkasnya ikut dibuang. Tabel `site_texts` sendiri tetap ada, lihat `../keputusan.md`.
- Halaman ini **menggantikan Dasbor** bawaan Filament dan menempati path `/` panel, jadi login mendarat langsung di sini. Widget bawaan Filament dilepas.
- Halaman login punya tautan kembali ke beranda situs.
- `config/site.php` **dipensiunkan**. Berkas yang jadi tidak terpakai dan ikut dihapus: `config/site.php` sendiri dan enam berkas di `app/Filament/Resources/SiteTexts/`.

## Judul bagian beranda

"Project pilihan" jadi **Project unggulan**, "Tulisan pilihan" jadi **Tulisan unggulan**. Padanan Inggrisnya *Selected projects* dan *Selected writing*; bagian galeri mengikuti pola yang sama.

Judul bagian wajib ikut berganti saat bahasa dialihkan — sebelumnya judulnya tetap Indonesia padahal isinya sudah Inggris. Rinciannya di `05-dwibahasa.md`.

## Tata letak form di panel

Murni penataan: kolom, validasi, dan perilaku simpan tidak diubah (kecuali batas tahun di bawah).

Ketiga form menetapkan tata letaknya **eksplisit** (`->columns(1)` di akar skema), tidak menumpang default Filament. Default itu yang membuat Section berpasangan dua kolom sehingga kolom kiri habis lebih dulu dan menyisakan ruang kosong panjang.

- **Buat/Edit Tulisan.** Judul dan Slug bersebelahan, lalu Ringkasan, lalu Sampul. Editor konten selebar penuh dengan tinggi lega — itu tempat kerja utama. Tautan project dan Publikasi bersebelahan di baris bawah.
- **Buat/Edit Project.** Keempat Section ditumpuk selebar penuh; tidak ada kolom yang menggantung sendirian, dan bagian Gambar mendapat lebar penuh karena itu yang paling sering dipakai.
- **Buat/Edit Foto.** Isinya sudah wajar sejak Fase 4 dan tidak diubah; hanya akar skemanya yang ikut ditetapkan eksplisit supaya seragam dengan dua form lainnya.

## Validasi tahun project

Kolom Tahun dibatasi **2000 sampai tahun berjalan ditambah satu** (satu tahun ke depan supaya project yang dijadwalkan tetap bisa dicatat). Batas atas dihitung per permintaan, jadi bergeser sendiri tiap pergantian tahun. Kolomnya tetap `string`; tidak ada migrasi baru.

## Kriteria lolos

- Tidak ada lagi `contoh.invalid` di kode.
- Foto profil bisa diunggah lewat panel, dan kotak "Foto sementara" di beranda hilang setelahnya.
- Panel hanya punya satu menu untuk beranda.
- Login mendarat langsung di halaman itu.
- Ketiga form tidak lagi menyisakan kolom kosong panjang.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: rute publik membalas 200 dalam kedua pilihan bahasa, dan `migrate:fresh --seed` berjalan bersih.
