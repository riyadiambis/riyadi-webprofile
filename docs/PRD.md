# PRD — Website Pribadi Rahmat Riyadi

Versi 1.0 | September 2026
Status: draft untuk direview pemilik produk (Rahmat Riyadi)

---

## 1. Latar Belakang

Saat ini kehadiran online Rahmat Riyadi hanya berupa halaman Taplink berisi empat tautan (LinkedIn, GitHub, TikTok, Email). Halaman itu tidak bisa menampung karya, tulisan, maupun dokumentasi visual, padahal sudah ada cukup banyak materi yang layak ditampilkan: project kuliah, riset, project Dicoding, dan pengalaman mengajar.

Website ini dibangun untuk menjadi satu alamat tunggal yang bisa dibagikan ke rekruter, klien, dan siapa pun yang ingin mengenal pemiliknya lebih jauh.

## 2. Tujuan

1. Menampilkan project dalam bentuk yang bisa dinilai orang lain, bukan sekadar daftar judul.
2. Menjadi tempat menulis jangka panjang, baik catatan teknis maupun cerita pribadi.
3. Menampung dokumentasi visual yang tidak punya rumah di tempat lain.
4. Menjadi web pertama yang di-hosting mandiri di home server pribadi, sekaligus latihan operasional server.

## 3. Non-Tujuan (di luar cakupan v1)

- Jualan produk atau jasa, sistem pembayaran, keranjang belanja.
- Kolom komentar, akun pengunjung, newsletter.
- Multi-penulis. Hanya ada satu akun admin, yaitu pemilik.
- Terjemahan otomatis untuk isi artikel journal. Journal tetap berbahasa Indonesia di v1.

Catatan soal bahasa: dwibahasa diterapkan terbatas dan manual, hanya pada teks pendek di beranda dan ringkasan project, dengan pola dua kolom seperti LinkedIn. Rinciannya di bagian 7.6.

## 4. Pengguna

| Pengguna | Kebutuhan utama |
|---|---|
| Rekruter / calon klien | Cepat tahu siapa pemilik, lihat 2-3 project terbaik beserta penjelasannya, temukan kontak |
| Pembaca tulisan | Menemukan artikel, membacanya dengan nyaman di HP |
| Pemilik (admin) | Menulis dan mengunggah gambar dari perangkat apa pun tanpa membuka VS Code |

## 5. Keputusan Teknis

### 5.1 Stack

| Lapisan | Pilihan |
|---|---|
| Framework | Laravel (versi terbaru) |
| Panel admin | Filament |
| Database | SQLite (satu berkas, tanpa server database terpisah) |
| Styling | Tailwind CSS |
| Web server | Caddy (HTTPS otomatis) |
| Akses publik | Cloudflare Tunnel |
| Repositori | GitHub, satu repo |
| Eksekusi koding | Claude Code |

Alasan pemilihan: kebutuhan terbesar produk ini adalah panel penulisan yang matang (editor teks kaya, unggah gambar, media library, autentikasi). Filament menyediakan semua itu tanpa perlu dibangun ulang, sehingga waktu pengerjaan bisa dipusatkan pada tampilan publik. Selain itu pemilik sudah membaca dan mengambil keputusan atas basis kode Laravel di project lain, sehingga perannya sebagai pengambil keputusan atas kode tetap terjaga.

SQLite dipilih karena container hosting hanya punya RAM 1 GB dan trafik situs personal sangat kecil. Konsekuensinya: basis data cukup satu berkas, backup berarti menyalin satu berkas.

### 5.2 Infrastruktur

- Container LXC `web-hosting` pada Proxmox `mimi`: 2 core, RAM 1 GB, disk dinaikkan ke minimal 30 GiB sebelum deploy.
- Domain: ekstensi murah (.my.id atau setara), didaftarkan dan DNS-nya dikelola lewat Cloudflare.
- Tanpa port forwarding. Koneksi keluar lewat Cloudflare Tunnel.
- Panel admin dibatasi: hanya bisa diakses lewat jaringan Tailscale atau dilindungi Cloudflare Access. Halaman publik terbuka untuk umum.

### 5.3 Risiko yang diterima

Server berada di kosan dengan koneksi rumahan. Jika listrik atau internet mati, situs ikut mati. Mitigasi: cache halaman di Cloudflare, dan seluruh kode tersimpan di GitHub sehingga bisa dipindahkan ke hosting lain tanpa mengganti domain.

## 6. Model Data

### `posts` (tulisan journal)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | integer | |
| judul | string | |
| slug | string unik | dari judul, bisa diedit manual |
| ringkasan | text | dipakai di kartu daftar dan meta description |
| konten | longtext | HTML hasil editor |
| cover | string, nullable | path gambar. Boleh kosong supaya draf bisa disimpan sebelum sampul disiapkan |
| tautan_project | string, nullable | ditampilkan sebagai tombol di paling atas tulisan |
| label_tautan | string, nullable | contoh: "Lihat repo", "Coba demo" |
| status | enum | draf / terbit |
| dipin | boolean | tampil di beranda |
| terbit_pada | datetime, nullable | kosong selama status masih draf |

### `projects`
| Kolom | Tipe | Catatan |
|---|---|---|
| id | integer | |
| nama | string | |
| ringkasan | string | satu kalimat, bahasa Indonesia |
| ringkasan_en | string, nullable | versi bahasa Inggris, diisi manual |
| gambar | json | daftar path gambar, minimal 1, dipakai untuk pergantian otomatis di kartu |
| tahun | string | |
| post_id | foreign key, nullable | tulisan journal yang dituju saat kartu diklik |
| dipin | boolean | tampil di beranda |
| urutan | integer | |

### `photos` (galeri)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | integer | |
| gambar | string | |
| caption | string, nullable | teks pendek, muncul saat dibuka |
| diambil_pada | date, nullable | |
| urutan | integer | |

### `site_texts` (teks beranda dwibahasa)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | integer | auto-increment, primary key |
| kunci | string | indeks unik. Hanya dua kunci di v1: `perkenalan` dan `penutup` |
| nilai_id | text | versi bahasa Indonesia |
| nilai_en | text, nullable | versi bahasa Inggris |

### `profiles` (profil pemilik di beranda)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | integer | |
| foto | string, nullable | path turunan thumb foto profil. Kosong berarti beranda menampilkan kotak inisial |
| email | string, nullable | dipakai tombol Email dan tombol "Kirim email" di penutup |
| linkedin | string, nullable | |
| github | string, nullable | |
| tiktok | string, nullable | |
| instagram | string, nullable | |
| youtube | string, nullable | |

Satu baris saja. Tautan yang dikosongkan tidak dirender di beranda — tombolnya hilang, bukan tampil mati. Tabel ini menggantikan `config/site.php` yang dipensiunkan, supaya pemilik bisa mengubah foto dan tautan lewat panel tanpa deploy ulang.

### `users`
Satu baris saja, akun pemilik. Bawaan Laravel, tanpa pendaftaran publik.

### `tags` dan tabel pivot `post_tag`
Opsional, dikerjakan di fase akhir. Tidak boleh menghambat fase lain.

## 7. Spesifikasi Halaman

### 7.1 Beranda (`/`)

Bagian dari atas ke bawah:

1. **Perkenalan.** Foto, nama, satu paragraf tentang diri, tautan ke email, LinkedIn, GitHub, TikTok, Instagram, YouTube. Foto dan keenam tautan diambil dari tabel `profiles`, disunting lewat halaman Beranda di panel. Tautan yang kosong tidak dirender.
2. **Project unggulan.** Slider horizontal berisi kartu project yang ditandai `dipin`. Bisa digeser dengan sentuhan di HP dan tombol panah di desktop.
3. **Tulisan unggulan.** Slider horizontal berisi post yang ditandai `dipin`.
4. **Galeri.** Slider horizontal berisi foto terbaru dari galeri.
5. **Penutup.** Ajakan menghubungi dan tautan ke halaman kontak/email.

Setiap slider punya tautan "lihat semua" ke halaman penuhnya.

### 7.2 Project (`/project`)

Grid kartu project. Perilaku khusus kartu:

- Kartu yang punya lebih dari satu gambar menampilkan gambarnya bergantian otomatis, transisi lembut, jeda sekitar 3 detik.
- Pergantian tidak boleh berjalan serentak persis antar kartu. Beri jeda awal acak per kartu.
- Menghormati `prefers-reduced-motion`. Jika pengguna mematikan animasi, tampilkan gambar pertama saja.
- Seluruh kartu bisa diklik. Tujuannya adalah tulisan journal yang tertaut (`post_id`). Jika belum ada tulisan tertaut, kartu tidak bisa diklik dan diberi label "segera ditulis".

### 7.3 Journal (`/journal` dan `/journal/{slug}`)

Halaman daftar: kartu berisi cover, judul, ringkasan, tanggal. Urut dari terbaru. Paginasi sederhana.

Halaman tulisan:
- Jika `tautan_project` terisi, tampilkan tombol menuju tautan itu di posisi paling atas, sebelum judul atau tepat di bawahnya.
- Isi tulisan mendukung: heading, tebal, miring, daftar, kutipan, blok kode, gambar di tengah tulisan, dan sematan video YouTube.
- Lebar kolom baca dibatasi sekitar 70 karakter agar nyaman dibaca.
- Gambar di dalam tulisan bisa diklik untuk diperbesar.

### 7.4 Galeri (`/galeri`)

Grid rapat bergaya Instagram. Foto diklik membuka tampilan besar berisi foto dan caption pendek, bisa dinavigasi ke foto berikutnya. Gambar dimuat bertahap saat digulir.

### 7.5 Panel Admin (`/admin`)

Disediakan Filament. Isi minimal:
- CRUD posts dengan editor teks kaya, unggah gambar di dalam tulisan, tombol sematan YouTube, pengaturan status draf/terbit dan penanda pin.
- CRUD projects dengan unggah banyak gambar, pemilihan tulisan yang ditautkan, dan kolom ringkasan Indonesia serta Inggris bersebelahan. Nama project hanya satu kolom.
- **Satu** halaman "Beranda" yang menampung seluruh isi beranda: foto profil, keenam tautan sosial, serta teks perkenalan dan penutup dengan pola dua kolom bahasa. Tidak boleh ada menu kedua untuk halaman publik yang sama.
- CRUD photos dengan unggah banyak berkas sekaligus dan pengurutan.
- Login satu akun, dengan tautan kembali ke situs di halaman login.

Halaman Beranda menggantikan Dasbor bawaan Filament: setelah login, pemilik mendarat langsung di sana.

### 7.6 Dwibahasa terbatas

Cakupan: teks perkenalan dan penutup di beranda, ringkasan project, serta **judul bagian dan label di halaman beranda** (lewat berkas `lang/`, bukan basis data). Nama project tidak diterjemahkan dan tidak punya kolom `nama_en`. Tidak mencakup isi artikel journal, caption galeri, navigasi header, dan halaman selain beranda.

Aturan:
- Pengisian sepenuhnya manual. Tidak ada terjemahan otomatis, tidak ada panggilan ke layanan penerjemah.
- Di panel admin, setiap teks yang dwibahasa ditampilkan sebagai dua kolom bersebelahan: Indonesia dan Inggris.
- Ada tombol pengalih bahasa di header, hanya menampilkan ID dan EN. Pilihan disimpan di cookie.
- Jika versi Inggris kosong, tampilkan versi Indonesia sebagai cadangan, bukan bagian kosong. Tandai di panel admin mana saja yang belum diterjemahkan.
- Halaman journal dan galeri tidak ikut berubah saat bahasa dialihkan. Ini diterima sebagai konsekuensi v1.

## 8. Aturan Media

- Semua gambar yang diunggah dikompres dan diubah ke WebP secara otomatis.
- Disimpan dalam tiga ukuran: thumbnail, sedang, penuh. Halaman daftar dan galeri hanya memuat ukuran kecil.
- Batas unggah 10 MB per berkas.
- Berkas disimpan di direktori `storage` di dalam container, bukan di dalam basis data.

## 9. Desain Visual

Gaya visualnya turunan dari gaya "Buku Tulis" milik pemilik, disesuaikan agar terbaca dewasa dan profesional: latar kertas bertekstur tipis, kartu berbingkai tegas dengan bayangan offset solid tanpa blur, judul berserif, dan aksen kuning yang dipakai sangat irit. Desktop-first, tapi wajib rapi di layar HP.

Seluruh nilai konkretnya — warna, font, ukuran border, radius, bayangan, jarak antar bagian — ada di `docs/design-tokens.md` dan hanya di sana. Berkas itu satu-satunya sumber kebenaran visual. Jangan menyalin nilainya ke dokumen ini atau ke Blade.

## 10. Kebutuhan Non-Fungsional

- Halaman publik dirender di server. Tidak boleh butuh JavaScript untuk membaca konten.
- Skor Lighthouse performa minimal 90 di mobile.
- Setiap halaman punya judul, deskripsi, dan gambar Open Graph agar rapi saat dibagikan di WhatsApp dan LinkedIn.
- Sitemap dan RSS untuk journal.
- Backup: skrip terjadwal yang menyalin berkas SQLite dan direktori storage ke lokasi lain di server, dijalankan harian.
- Panel admin tidak terekspos ke internet publik tanpa perlindungan.

## 11. Kriteria Selesai untuk v1

Website dianggap selesai jika pemilik bisa, dari HP, membuka panel admin, menulis satu artikel lengkap dengan gambar dan video, menerbitkannya, lalu membuka alamat domain di jaringan seluler dan melihat artikel itu tampil dengan benar.

## 12. Pertanyaan Terbuka

Masih terbuka:

1. Nama domain yang diinginkan.
2. Berapa project yang siap ditulis journal-nya saat peluncuran.

Sudah diputuskan:

- **Halaman kontak.** Tidak dibuat. Beranda cukup memuat tautan email dan tautan sosial. Formulir kontak masuk daftar setelah v1.
- **Unduh CV.** Tidak masuk v1. Masuk daftar setelah v1.
