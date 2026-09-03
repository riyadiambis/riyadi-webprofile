# Website Pribadi Rahmat Riyadi

Profil, project, journal, dan galeri. Laravel + Filament + SQLite, di-hosting mandiri di home server Proxmox.

## Dokumen

Baca dulu sebelum menyentuh kode. Urutan wewenangnya dijelaskan di [CLAUDE.md](CLAUDE.md).

| Berkas | Isi |
|---|---|
| [docs/PRD.md](docs/PRD.md) | Latar belakang, keputusan teknis, model data, spesifikasi halaman |
| [docs/ROADMAP.md](docs/ROADMAP.md) | Urutan fase, kriteria lolos, pemetaan fase ke berkas fitur |
| [docs/design-tokens.md](docs/design-tokens.md) | Seluruh nilai visual. Satu-satunya sumber kebenaran warna dan tipografi |
| [docs/fitur/](docs/fitur/) | Spesifikasi rinci per fitur, satu berkas per fase |

## Lingkungan pengembangan

Dikembangkan di laptop Windows lewat VS Code. Home server hanya dipakai untuk deploy dan baru tersentuh mulai Fase 6.

Kebutuhan:

- PHP 8.3 atau lebih baru, dengan ekstensi `sqlite3`, `gd` atau `imagick`, `mbstring`, `zip`.
- Composer 2.
- Node 20 atau lebih baru.

## Menjalankan secara lokal

Diisi pada Fase 1, setelah Laravel terpasang.

## Alur kerja

Satu fase roadmap sama dengan satu branch dan satu pull request. Nama branch `fase-N-nama-singkat`, contoh `fase-1-fondasi`.

Sebelum meminta review, jalankan smoke test Pest dan buktikan kriteria lolos fase tersebut.

## Deploy

Langkah deploy dan deploy ulang dicatat di sini pada Fase 6, supaya bisa diulang tanpa mengingat-ingat.
