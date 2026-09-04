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
