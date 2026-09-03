# CLAUDE.md

Panduan kerja untuk Claude Code di repositori ini. Baca berkas ini lebih dulu pada setiap sesi baru.

## Tentang proyek

Website pribadi Rahmat Riyadi: profil, project, journal, dan galeri. Di-hosting mandiri di home server Proxmox milik pemilik.

## Peran

Pemilik berperan sebagai pengambil keputusan produk dan penilai kode, bukan penulis kode. Karena itu:

- Jelaskan keputusan teknis dengan bahasa yang bisa dinilai tanpa membaca seluruh berkas.
- Kalau ada dua cara mengerjakan sesuatu dan pilihannya berdampak jangka panjang, tanyakan lebih dulu. Jangan pilih diam-diam.
- Jangan memasang paket pihak ketiga baru tanpa menyebutkan alasannya.

## Sumber kebenaran

Urutan wewenang jika terjadi perbedaan:

1. `docs/fitur/*.md` — spesifikasi rinci per fitur. Ini yang dieksekusi.
2. `docs/PRD.md` — konteks, keputusan teknis, model data, kriteria selesai.
3. `docs/ROADMAP.md` — urutan pengerjaan.
4. `docs/design-tokens.md` — seluruh nilai visual.

Kalau kode dan dokumen berbeda, dokumen yang benar. Kalau dokumen sendiri yang keliru, perbaiki dokumennya dalam PR yang sama, jangan diam-diam menyimpang.

## Stack

Laravel, Filament, SQLite, Tailwind CSS, Caddy, Cloudflare Tunnel.

- Database SQLite satu berkas. Jangan mengusulkan MySQL atau PostgreSQL.
- Halaman publik dirender di server dengan Blade. Bukan SPA.
- JavaScript seperlunya saja untuk slider, pergantian gambar, dan lightbox.

## Aturan koding

- Nama tabel, kolom, dan rute memakai bahasa Indonesia sesuai PRD. Nama kelas dan method Laravel tetap konvensi bahasa Inggris.
- Migrasi tidak boleh diedit setelah di-merge. Buat migrasi baru.
- Semua nilai warna, font, radius, dan bayangan diambil dari `docs/design-tokens.md` lewat konfigurasi Tailwind. Dilarang menulis nilai hex langsung di Blade.
- Tulis seed data contoh supaya halaman bisa diperiksa tanpa mengisi manual.

## Alur kerja

Satu fase roadmap sama dengan satu branch dan satu pull request. Sebelum minta review, jalankan pemeriksaan sendiri terhadap kriteria lolos fase itu dan laporkan hasilnya.

Di akhir setiap fase, tulis ringkasan berisi: apa yang berubah, keputusan apa yang diambil, dan apa yang perlu diperiksa pemilik lewat browser.

## Larangan

- Jangan mengerjakan fitur di luar fase yang sedang berjalan, meskipun terlihat sepele.
- Jangan menambahkan komentar pengunjung, pendaftaran pengguna, atau terjemahan otomatis. Ketiganya sudah ditolak.
- Jangan menaruh kredensial atau berkas `.env` ke dalam repositori.
