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

Seluruh perintah dijalankan dari akar project, yaitu folder `webProfilRiyadi`,
bukan dari folder induk.

Catatan Windows: `composer` hanya terdaftar di PATH PowerShell, tidak terlihat
dari Git Bash. Jalankan perintah `composer` lewat PowerShell.

Penyiapan pertama kali:

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
```

Menjalankan:

```powershell
php artisan serve
```

Situs terbuka di http://127.0.0.1:8000 dan panel admin di
http://127.0.0.1:8000/admin. Akun admin dibuat oleh seeder dari nilai
`ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env`.

Kalau port 8000 sudah dipakai project lain di mesin yang sama, pilih port lain
dengan `php artisan serve --port=8347`. Server bawaan PHP di Windows tidak
selalu menolak port yang sudah terpakai, jadi permintaanmu bisa dijawab
aplikasi tetangga tanpa pesan galat apa pun. Kalau halaman terasa asing,
periksa dulu port dan proses yang sedang berjalan.

Saat menggarap tampilan, jalankan Vite supaya perubahan langsung terlihat:

```powershell
npm run dev
```

## Menjalankan test

```powershell
php artisan test
```

Smoke test saja: rute publik membalas 200 dan `migrate:fresh --seed` berjalan
bersih. Aturan lengkapnya ada di [CLAUDE.md](CLAUDE.md). Test memakai SQLite
di memori, jadi tidak pernah menyentuh `database/database.sqlite`.

## Alur kerja

Satu fase roadmap sama dengan satu branch dan satu pull request. Nama branch `fase-N-nama-singkat`, contoh `fase-1-fondasi`.

Sebelum meminta review, jalankan smoke test Pest dan buktikan kriteria lolos fase tersebut.

## Deploy

Langkah deploy dan deploy ulang dicatat di sini pada Fase 6, supaya bisa diulang tanpa mengingat-ingat.
