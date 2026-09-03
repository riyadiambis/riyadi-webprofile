# ROADMAP — Website Pribadi Rahmat Riyadi

Dokumen ini mengurutkan pekerjaan. PRD menjawab "apa", roadmap menjawab "kapan dan dengan urutan mana". Setiap fase punya kriteria lolos yang harus dibuktikan sebelum lanjut ke fase berikutnya.

Aturan kerja:
- Satu fase, satu branch, satu pull request. Nama branch `fase-N-nama-singkat`, contoh `fase-1-fondasi`.
- Jangan mengerjakan fase berikutnya sebelum kriteria lolos fase sekarang terpenuhi dan sudah di-merge.
- Setiap fase wajib lolos smoke test sesuai aturan tetap di `CLAUDE.md`. Ini sudah dicantumkan di kriteria lolos tiap fase di bawah.
- Setiap akhir fase, jelaskan ke pemilik apa yang berubah dan keputusan apa yang diambil, dengan bahasa yang bisa dinilai tanpa membaca seluruh kode.

## Pemetaan fase ke berkas fitur

| Fase | Berkas fitur | Branch | PR |
|---|---|---|---|
| 0 — Persiapan | tidak ada, dikerjakan pemilik di luar kode | — | — |
| 1 — Fondasi | `docs/fitur/00-fondasi.md` | `fase-1-fondasi` | 1 |
| 2 — Journal | `docs/fitur/01-journal.md` | `fase-2-journal` | 1 |
| 3 — Project | `docs/fitur/02-project.md` | `fase-3-project` | 1 |
| 4 — Galeri | `docs/fitur/03-galeri.md` | `fase-4-galeri` | 1 |
| 5 — Beranda | `docs/fitur/04-beranda.md` dan `docs/fitur/05-dwibahasa.md` | `fase-5-beranda` | 1 |
| 6 — Deploy | `docs/fitur/06-deploy.md` | `fase-6-deploy` | 1 |
| 7 — Poles | `docs/fitur/07-poles.md` | `fase-7-poles` | 1 |

Fase 5 dilayani dua berkas fitur karena dwibahasa hanya masuk akal dikerjakan bersamaan dengan beranda. Keduanya tetap satu branch dan satu pull request.

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

**Lolos jika:** halaman kosong dari keempat rute bisa dibuka, panel admin bisa login, dan tampilan sudah memakai token desain. Smoke test membuktikan keempat rute membalas 200 dan `migrate:fresh --seed` berjalan bersih.

---

## Fase 2 — Journal dan panel penulisan

Fase paling penting. Kalau fase ini gagal, produk ini tidak ada gunanya.

- Resource Filament untuk `posts`: editor teks kaya, unggah gambar di tengah tulisan, sematan YouTube, status draf/terbit, penanda pin.
- Pipeline media: kompres, konversi WebP, tiga ukuran turunan.
- Halaman publik `/journal` dan `/journal/{slug}`, termasuk tombol tautan project di bagian atas.
- Gambar dalam tulisan bisa diklik untuk diperbesar.

**Lolos jika:** satu artikel percobaan berisi teks, dua gambar, dan satu video YouTube bisa ditulis dari HP lalu tampil benar di halaman publik. Smoke test membuktikan `/journal` dan `/journal/{slug}` membalas 200 dan `migrate:fresh --seed` berjalan bersih.

---

## Fase 3 — Project

- Resource Filament untuk `projects`, termasuk unggah banyak gambar dan penautan ke satu post.
- Halaman `/project` dengan grid kartu.
- Perilaku pergantian gambar otomatis pada kartu, dengan jeda awal acak dan penghormatan pada `prefers-reduced-motion`.
- Kartu tanpa tulisan tertaut tidak bisa diklik.

**Lolos jika:** dua project percobaan tampil, gambarnya berganti sendiri tidak serentak, dan klik kartu mendarat di tulisan yang benar. Smoke test membuktikan `/project` membalas 200 dan `migrate:fresh --seed` berjalan bersih.

---

## Fase 4 — Galeri

- Resource Filament untuk `photos` dengan unggah banyak berkas dan pengurutan.
- Halaman `/galeri` dengan grid dan tampilan besar berisi caption, bisa dinavigasi maju mundur.
- Pemuatan bertahap saat digulir.

**Lolos jika:** dua puluh foto percobaan terbuka mulus di HP tanpa lonjakan pemakaian data yang tidak wajar. Smoke test membuktikan `/galeri` membalas 200 dan `migrate:fresh --seed` berjalan bersih.

---

## Fase 5 — Beranda

Dikerjakan terakhir di antara halaman, karena beranda memanggil data dari tiga fase sebelumnya.

- Bagian perkenalan dan tautan sosial.
- Tiga slider: project pilihan, tulisan pilihan, galeri terbaru. Semuanya dari data yang ditandai pin.
- Tautan "lihat semua" di setiap bagian.
- Pengalih bahasa ID/EN di header, berlaku untuk teks beranda dan ringkasan project sesuai bagian 7.6 PRD. Nama project tidak diterjemahkan. Isi journal dan galeri tidak ikut berubah.

**Lolos jika:** menandai pin pada satu project dari panel admin langsung mengubah isi beranda, dan menekan pengalih bahasa mengubah teks perkenalan serta ringkasan project tanpa merusak tata letak. Smoke test membuktikan `/` membalas 200 dalam kedua pilihan bahasa dan `migrate:fresh --seed` berjalan bersih.

---

## Fase 6 — Deploy

- Konfigurasi Caddy dan systemd service di container.
- Cloudflare Tunnel ke domain.
- Panel admin dibatasi jaringan Tailscale atau Cloudflare Access.
- Skrip backup harian untuk berkas SQLite dan direktori storage.
- Catat langkah deploy ulang di README supaya bisa diulang tanpa mengingat-ingat.

**Lolos jika:** domain dibuka dari jaringan seluler menampilkan situs dengan HTTPS, dan panel admin tidak bisa dibuka dari luar. Seluruh smoke test dari fase sebelumnya tetap hijau di lingkungan pengembangan.

---

## Fase 7 — Poles

- Meta tag, Open Graph, sitemap, RSS.
- Halaman 404 yang tidak asal-asalan.
- Pemeriksaan Lighthouse dan perbaikan yang muncul.
- Tag untuk journal. **Opsional.** Boleh dibuang tanpa menghambat fase ini dinyatakan lolos.

**Lolos jika:** tautan situs dibagikan di WhatsApp menampilkan pratinjau yang rapi, skor performa mobile minimal 90, dan seluruh smoke test tetap hijau. Tag tidak masuk hitungan kriteria lolos.

---

## Setelah v1

Ditulis di sini supaya tidak mengganggu fokus v1: mode gelap, pencarian tulisan, halaman kontak dengan formulir, unduh CV, dan penempatan project lain di container yang sama.
