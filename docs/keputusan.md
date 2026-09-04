# Keputusan Teknis

Keputusan yang tidak cukup penting untuk masuk PRD atau ROADMAP, tapi cukup penting untuk tidak hilang di riwayat git. Satu keputusan, satu bagian: apa yang diputuskan, kenapa, dan konsekuensi apa yang diterima.

## Aset Filament tidak dilacak di git

**Keputusan.** Berkas `public/css/filament/`, `public/js/filament/`, dan `public/fonts/filament/` (37 berkas sejak Filament v5.7.8) diabaikan lewat `.gitignore`, bukan ikut di-commit.

**Kenapa.** Berkas-berkas itu dibuat ulang otomatis oleh `filament:upgrade`, yang berjalan lewat hook `post-autoload-dump` setiap `composer install`. Melacaknya di git berarti diff berisik setiap Filament naik versi, padahal isinya sepenuhnya bisa dibuat ulang dari `composer.lock`.

**Jebakan yang perlu diketahui.** Sebelum keputusan ini diambil, aset itu sempat ter-commit dulu, lalu dikeluarkan lewat `git rm -r --cached`. Perintah itu tidak menghapus berkas dari disk siapa pun yang menjalankannya — tapi ia merekam *penghapusan* di dalam commit. Akibatnya, siapa pun yang menarik (`pull`) atau checkout commit itu, git ikut menghapus salinan aset di working tree mereka, karena dari sudut pandang git berkas itu memang dihapus di sana.

**Gejalanya:** `/admin` tetap membalas kode 200 — halamannya ada, hanya saja tanpa satu pun CSS atau JavaScript, sehingga terlihat rusak total di browser. Karena status HTTP-nya tetap benar, **smoke test tidak menangkap ini sama sekali**. Baru kelihatan lewat pemeriksaan visual manual.

**Obatnya**, kalau gejala itu muncul:

```powershell
php artisan filament:assets
```

**Konsekuensi yang diterima.** Setiap kali ada yang menarik commit yang mengubah dependensi Filament (naik versi, atau — seperti kasus di atas — perubahan tracking aset itu sendiri), ada langkah manual satu baris yang harus diingat. Dicatat di [README.md](../README.md) bagian "Menjalankan lokal" supaya tidak perlu diingat-ingat.

## Kartu project menumpuk semua gambar di DOM untuk preload (Fase 3)

**Keputusan.** Semua gambar milik satu project dirender sekaligus sebagai `<img>` bertumpuk (posisi absolut, `opacity` 0 atau 100 bergantian), bukan satu `<img>` yang atribut `src`-nya diganti lewat JavaScript saat berganti slide.

**Kenapa.** Ini caranya "preload" tanpa menulis logika preload manual. Begitu halaman dimuat, browser sudah mulai mengambil seluruh gambar kartu (gambar pertama `loading="eager"`, sisanya `loading="lazy"`) — jauh sebelum timer JS memicu pergantian pertama sekitar 3 detik kemudian. Kalau memakai satu `<img>` yang src-nya diganti, pergantian akan memicu fetch baru tepat saat itu juga, dan gambar sempat kosong/berkedip sebelum termuat.

**Konsekuensi yang diterima.** Jumlah permintaan gambar di `/project` tumbuh mengikuti *(jumlah project) × (rata-rata gambar per project)* — semuanya, karena halaman ini **tidak dipaginasi** (beda dari `/journal` yang paginasinya sudah ada sejak Fase 2B). Untuk situs personal dengan belasan project ringkasan halaman ini masih murah (turunan thumb WebP sekitar 4 KB per gambar, lihat pengukuran Fase 2A), tapi jumlah *request*-nya ikut naik linear, bukan cuma total byte-nya.

**Kapan pola ini harus diganti.** Kalau `/project` menampung lebih dari sekitar **15 project**, atau total gambar yang tertumpuk di seluruh kartu melebihi sekitar **50**, pertimbangkan salah satu: (a) menambahkan paginasi ke `/project` seperti `/journal`, atau (b) memasang gambar tambahan tiap kartu (elemen kedua dan seterusnya) ke DOM secara lazy lewat `IntersectionObserver` — baru dipasang saat kartunya mendekati viewport, bukan sejak render awal. Kedua angka itu bukan sembarang; project portofolio yang wajar rata-rata punya 2–3 gambar, jadi 15 project kurang lebih bertemu di titik yang sama dengan 50 gambar.

## Kartu project baru bisa diklik kalau post tertaut sudah terbit (Fase 3)

**Keputusan.** `Project::bisaDiklik()` memeriksa dua syarat: `post_id` terisi **dan** `post->status === 'terbit'`. Bukan hanya `post_id` terisi seperti bunyi literal PRD ("kartu tanpa post tertaut tidak bisa diklik").

**Kenapa.** Panel admin membolehkan menautkan project ke post yang masih **draf** — masuk akal kalau pemilik ingin menyiapkan tautannya lebih dulu, sebelum artikelnya selesai ditulis. Tapi Fase 2B sudah memastikan draf membalas 404 kalau diakses langsung lewat URL publik (`Post::scopeTerbit()` disaring di level query). Kalau `bisaDiklik()` hanya mengecek `post_id` terisi, kartu akan mengarah ke tautan yang 404 — pengalaman yang jelas rusak, meski secara harfiah "post-nya tertaut".

**Konsekuensi yang diterima.** Kartu dengan post draf tertaut tetap tampil sebagai "segera ditulis" sampai post itu diterbitkan — bukan sampai `post_id`-nya diisi. Begitu status post diubah jadi terbit, kartu otomatis jadi bisa diklik tanpa perlu menyunting project-nya lagi.
