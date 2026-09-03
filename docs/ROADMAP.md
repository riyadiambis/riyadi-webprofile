# ROADMAP — Website Pribadi Rahmat Riyadi

Dokumen ini mengurutkan pekerjaan. PRD menjawab "apa", roadmap menjawab "kapan dan dengan urutan mana". Setiap fase punya kriteria lolos yang harus dibuktikan sebelum lanjut ke fase berikutnya.

Aturan kerja:
- Satu fase, satu branch, satu pull request.
- Jangan mengerjakan fase berikutnya sebelum kriteria lolos fase sekarang terpenuhi dan sudah di-merge.
- Setiap akhir fase, jelaskan ke pemilik apa yang berubah dan keputusan apa yang diambil, dengan bahasa yang bisa dinilai tanpa membaca seluruh kode.

---

## Fase 0 — Persiapan di luar kode

Dikerjakan pemilik, bukan AI.

- Naikkan disk container `web-hosting` di Proxmox ke minimal 30 GiB.
- Beli domain, arahkan nameserver ke Cloudflare.
- Pastikan container bisa di-SSH dari laptop lewat Tailscale.
- Buat repo kosong di GitHub.

**Lolos jika:** domain sudah aktif di Cloudflare dan container bisa diakses lewat SSH.

---

## Fase 1 — Fondasi

- Pasang Laravel, konfigurasi SQLite, pasang Tailwind, pasang Filament dengan satu akun admin.
- Buat migrasi untuk `posts`, `projects`, `photos`, `site_texts` sesuai PRD.
- Buat berkas token desain (warna, font, ukuran border dan bayangan) sebagai satu sumber kebenaran, bukan nilai yang bertebaran di banyak tempat.
- Buat layout publik: header, navigasi empat halaman, footer, latar kertas.

**Lolos jika:** halaman kosong dari keempat rute bisa dibuka, panel admin bisa login, dan tampilan sudah memakai token desain.

---

## Fase 2 — Journal dan panel penulisan

Fase paling penting. Kalau fase ini gagal, produk ini tidak ada gunanya.

- Resource Filament untuk `posts`: editor teks kaya, unggah gambar di tengah tulisan, sematan YouTube, status draf/terbit, penanda pin.
- Pipeline media: kompres, konversi WebP, tiga ukuran turunan.
- Halaman publik `/journal` dan `/journal/{slug}`, termasuk tombol tautan project di bagian atas.
- Gambar dalam tulisan bisa diklik untuk diperbesar.

**Lolos jika:** satu artikel percobaan berisi teks, dua gambar, dan satu video YouTube bisa ditulis dari HP lalu tampil benar di halaman publik.

---

## Fase 3 — Project

- Resource Filament untuk `projects`, termasuk unggah banyak gambar dan penautan ke satu post.
- Halaman `/project` dengan grid kartu.
- Perilaku pergantian gambar otomatis pada kartu, dengan jeda awal acak dan penghormatan pada `prefers-reduced-motion`.
- Kartu tanpa tulisan tertaut tidak bisa diklik.

**Lolos jika:** dua project percobaan tampil, gambarnya berganti sendiri tidak serentak, dan klik kartu mendarat di tulisan yang benar.

---

## Fase 4 — Galeri

- Resource Filament untuk `photos` dengan unggah banyak berkas dan pengurutan.
- Halaman `/galeri` dengan grid dan tampilan besar berisi caption, bisa dinavigasi maju mundur.
- Pemuatan bertahap saat digulir.

**Lolos jika:** dua puluh foto percobaan terbuka mulus di HP tanpa lonjakan pemakaian data yang tidak wajar.

---

## Fase 5 — Beranda

Dikerjakan terakhir di antara halaman, karena beranda memanggil data dari tiga fase sebelumnya.

- Bagian perkenalan dan tautan sosial.
- Tiga slider: project pilihan, tulisan pilihan, galeri terbaru. Semuanya dari data yang ditandai pin.
- Tautan "lihat semua" di setiap bagian.
- Pengalih bahasa ID/EN di header, berlaku untuk teks beranda dan ringkasan project sesuai bagian 7.6 PRD. Isi journal dan galeri tidak ikut berubah.

**Lolos jika:** menandai pin pada satu project dari panel admin langsung mengubah isi beranda, dan menekan pengalih bahasa mengubah teks perkenalan serta ringkasan project tanpa merusak tata letak.

---

## Fase 6 — Deploy

- Konfigurasi Caddy dan systemd service di container.
- Cloudflare Tunnel ke domain.
- Panel admin dibatasi jaringan Tailscale atau Cloudflare Access.
- Skrip backup harian untuk berkas SQLite dan direktori storage.
- Catat langkah deploy ulang di README supaya bisa diulang tanpa mengingat-ingat.

**Lolos jika:** domain dibuka dari jaringan seluler menampilkan situs dengan HTTPS, dan panel admin tidak bisa dibuka dari luar.

---

## Fase 7 — Poles

- Meta tag, Open Graph, sitemap, RSS.
- Halaman 404 yang tidak asal-asalan.
- Pemeriksaan Lighthouse dan perbaikan yang muncul.
- Tag untuk journal, jika masih ada tenaga.

**Lolos jika:** tautan situs dibagikan di WhatsApp menampilkan pratinjau yang rapi, dan skor performa mobile minimal 90.

---

## Setelah v1

Ditulis di sini supaya tidak mengganggu fokus v1: mode gelap, pencarian tulisan, halaman kontak dengan formulir, unduh CV, dan penempatan project lain di container yang sama.
