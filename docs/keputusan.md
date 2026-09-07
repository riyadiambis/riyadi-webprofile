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

## photos.gambar menyimpan basis nama, bukan path turunan (Fase 4)

**Keputusan.** Kolom `photos.gambar` menyimpan basis nama gambar — direktori UUID tempat ketiga turunan berada, tanpa nama turunan, mis. `galeri/550e8400-…`. Ini berbeda dari `posts.cover` dan elemen `projects.gambar` yang menyimpan path satu turunan (thumb). Ketiga URL disusun lewat `TurunanGambar::urlDariBasis()`; `urlDari()` untuk path turunan (Post/Project) sekarang delegasi ke fungsi yang sama, jadi penurunan path antar-turunan tetap terjadi di satu tempat.

**Kenapa.** Keputusan pemilik: kalau yang tersimpan path thumb, kode lain tergoda menebak turunan penuh lewat manipulasi string (`str_replace('thumb.webp', 'penuh.webp')`) — kebalikan dari alasan TurunanGambar dibuat. Dengan basis nama, tidak ada turunan tertentu yang "tersimpan", jadi tidak ada turunan lain yang bisa ditebak.

**Konsekuensi yang diterima.** Kolom berisi direktori, bukan berkas, sehingga pratinjau native FileUpload Filament tidak bisa membacanya langsung. Formulir unggah (halaman Create dan aksi unggah massal) menyimpan path thumb sebagai nilai formulir sementara demi pratinjau, lalu mengubahnya jadi basis nama lewat `TurunanGambar::basisDari()` saat menulis ke basis data. Kolom gambar di tabel admin membaca URL thumb lewat `Photo::gambarUrls()`. Kalau nanti ada kebutuhan serupa di berkas lain, jangan tulis versi kedua — pakai TurunanGambar.

## Lightbox tiga baris, gambar selalu muat utuh, tanpa scroll di dalam (perbaikan pasca-Fase 4)

**Keputusan.** Overlay lightbox disusun tiga baris vertikal: baris atas tombol tutup, baris tengah area gambar dengan gutter kiri-kanan untuk tombol maju/mundur, baris bawah caption dan tautan "Buka ukuran penuh" yang membuka berkas penuh di tab baru. Gambar di tengah dibatasi tinggi dan lebar sekaligus — sebatas sisa baris tengah — dan ditampilkan dengan `object-contain`. Tidak ada scroll di dalam overlay, dan kunci scroll halaman saat lightbox terbuka tetap dipertahankan seperti sebelumnya.

**Kenapa.** Susunan lama membiarkan foto potret lebih tinggi dari jendela: `max-height` pada gambar tidak meresolusi karena induknya bertinggi otomatis, bagian bawah terpotong, dan karena scroll halaman terkunci tidak ada cara melihat sisanya — caption ikut terdorong ke bawah layar. Dengan tiga baris, tinggi maksimum gambar adalah sisa area tengah setelah baris atas dan bawah mendapat tempatnya, dihitung oleh flexbox tanpa nilai `calc` manual.

**Jebakan yang perlu diketahui.** Gambar jangan diposisikan langsung dengan insets: elemen replaced seperti `<img>` dengan `width`/`height: auto` memakai ukuran intrinsik — inset kiri/kanan tidak meregangkannya (inset kanan-bawah diabaikan karena over-constrained). Solusinya `<img>` dibungkus `<div>` yang diregangkan insets, lalu gambar mengisi wrapper dengan `h-full w-full` dan `object-contain`.

**Konsekuensi yang diterima.** Lebar gambar di HP berkurang sedikit oleh gutter tombol maju/mundur; gambar lanskap mendapat pita kosong di atas-bawah (letterbox). Tautan ukuran penuh selalu tampil, juga untuk gambar artikel journal — menyimpang sedikit dari perilaku Fase 2B (yang tanpa caption), demi tangkapan layar berisi teks yang perlu dibaca.

## Bahasa aktif ditentukan satu middleware, titik tunggal `app()->getLocale()` (Fase 5)

**Keputusan.** Cookie `bahasa` dibaca **satu kali** oleh middleware `App\Http\Middleware\SetLocale` yang didaftarkan di grup web, lalu disimpan sebagai locale aplikasi lewat `app()->setLocale()`. Tidak ada Blade atau controller yang membaca cookie langsung; semua cukup memakai `app()->getLocale()`. Dua titik dwibahasa (`SiteText::nilai()` dan `Project::ringkasanTampil()`) memakai pola `?string $bahasa = null` lalu `$bahasa ??= app()->getLocale()` di badan fungsi — pemanggil lama tanpa argumen tidak berubah, dan kartu project Fase 3 otomatis mengikuti bahasa tanpa logika kedua.

**Kenapa.** Persyaratan Fase 5: satu tempat saja yang menentukan bahasa aktif, bukan pengecekan cookie yang tersebar di banyak Blade. PHP tidak mengizinkan pemanggilan fungsi sebagai nilai default parameter (fatal saat kompilasi), makanya penggantinya di badan fungsi.

**Konsekuensi yang diterima.** Bahasa aktif adalah global aplikasi selama satu permintaan; kode apa pun yang memakai `app()->getLocale()` ikut terpengaruh (misalnya atribut `lang` di `<html>`). Karena itu tanggal journal dikunci terpisah — lihat keputusan berikutnya.

## Tanggal journal dikunci ke locale Indonesia di tempat render (Fase 5)

**Keputusan.** Tanggal terbit yang dirender publik (`kartu-post` dan halaman tulisan) memanggil `->locale('id')->translatedFormat('d F Y')` — locale dikunci eksplisit di tempat render, bukan menyerahkan ke locale aplikasi.

**Kenapa.** Laravel menyebarkan perubahan locale ke Carbon, jadi `app()->setLocale('en')` dari pengalih bahasa ikut mengganti format tanggal journal. Cakupan dwibahasa v1 hanya teks beranda dan ringkasan project; journal dan galeri tidak boleh berubah saat bahasa dialihkan (`docs/fitur/05-dwibahasa.md`). Menggantungkan perilaku ini pada "locale saat boot" adalah efek samping yang rapuh.

**Konsekuensi yang diterima.** Tanggal journal selalu berbahasa Indonesia, juga saat pengunjung memilih EN. Kalau suatu saat journal ikut dwibahasa, kunci ini yang pertama dicabut.

## Slider beranda memakai guliran native, bukan kode geser lightbox (Fase 5)

**Keputusan.** Slider beranda digeser dengan guliran native browser (`overflow-x-auto` + snap) untuk sentuhan, dan `scrollBy()` untuk tombol panah desktop. Kode geser sentuh lightbox Fase 4 (ambang 40px, berpindah satu gambar) **tidak** dipakai bersama.

**Kenapa.** Kebutuhannya berbeda jauh: lightbox berpindah satu gambar secara diskrit di dalam overlay yang scroll-nya terkunci, sementara slider adalah guliran bebas yang butuh momentum dan rubber-band asli layar sentuh. Memaksakan threshold-swap ke slider menghilangkan momentum HP; membangun ulang lightbox di atas native scroll merusak overlay yang terkunci. Menyatukannya berarti membawa dua perilaku berlawanan ke dalam satu kode.

**Konsekuensi yang diterima.** Dua blok JavaScript kecil di `app.js` yang bertanggung jawab sendiri-sendiri, tanpa paket pihak ketiga. Slider tetap diam (tanpa autoplay) — kartu project di dalamnya mempertahankan rotasi gambarnya sendiri dari Fase 3.

## Cookie bahasa di test dikirim dengan `withCookie`, bukan `withUnencryptedCookie` (Fase 5)

**Keputusan.** Test dwibahasa di `tests/Feature/RutePublikTest.php` mengirim cookie `bahasa` lewat `withCookie('bahasa', 'en')`. Enkripsi cookie aplikasi tidak diubah, dan cookie `bahasa` tidak dimasukkan ke daftar cookie tak terenkripsi (`EncryptCookies::$except`). Rencana awal memakai `withUnencryptedCookie` atas instruksi pemilik, lalu dikoreksi setelah pengukuran membuktikan kebalikannya.

**Kenapa.** Penamaan keduanya menyesatkan. `withCookie` menggandakan alur produksi: kerangka test mengenkripsinya lebih dulu lewat `prepareCookiesForRequest()` (prefix + kunci APP_KEY yang sama), lalu `EncryptCookies` di sisi aplikasi mendekripsinya normal. `withUnencryptedCookie` justru mengirim nilai **mentah**; `EncryptCookies` gagal mendekripsinya, cookie di-null-kan, dan middleware `SetLocale` diam-diam jatuh ke bahasa Indonesia — kegagalan senyap yang tidak terlihat karena halaman tetap membalas 200.

**Jebakan yang perlu diketahui.** Pengukuran A/B dengan `RefreshDatabase` aktif (kondisi nyata `RutePublikTest` lewat `tests/Pest.php`) membuktikan: `withUnencryptedCookie('bahasa', 'en')` menghasilkan `<html lang="id">`, sedangkan `withCookie('bahasa', 'en')` menghasilkan `<html lang="en">`. Jangan ada yang menukar balik ke `withUnencryptedCookie` karena "terlihat lebih sesuai" — hasilnya justru jatuh ke Indonesia tanpa bunyi. (Catatan: mengukur cookie tanpa `RefreshDatabase` menghasilkan galat 500 karena tabel tidak ada, dan `lang="en"` di halaman galat bawaan Laravel sempat menyesatkan pengukuran — selalu ukur dengan `RefreshDatabase`.)

**Konsekuensi yang diterima.** Nilai cookie di test terenkripsi persis seperti di produksi, sehingga test tidak bisa memeriksa nilai mentah cookie — hanya efeknya pada halaman yang dirender.

## Verifikasi lebar HP wajib pakai emulasi mobile CDP, bukan `--window-size` (Fase 5)

**Keputusan.** Mengukur atau memfoto tampilan di lebar HP wajib memakai `Emulation.setDeviceMetricsOverride({ mobile: true, width: ... })` lewat Chrome DevTools Protocol (CDP) — mekanisme yang sama dipakai "Toggle device toolbar" DevTools dan Lighthouse mobile. **Bukan** `chrome --window-size=390,844 --screenshot`, walau tampilannya lebar 390px juga.

**Kenapa.** `--window-size` mengubah ukuran jendela Chrome mode desktop biasa, dan mode itu **tidak menghormati** `<meta name="viewport" content="width=device-width">` seperti HP asli — kuirk Chrome headless yang sudah lama dikenal. `Emulation.setDeviceMetricsOverride` dengan `mobile: true` yang benar-benar mengemulasikan viewport perangkat sungguhan.

**Jebakan yang perlu diketahui.** Ini bukan teori — sempat benar-benar menjebak. Screenshot lewat `--window-size=390` menunjukkan teks dan tombol terpotong di tepi kanan, konsisten dan bisa diulang di beberapa kali pengambilan, cukup meyakinkan sampai dilaporkan ke pemilik sebagai bug nyata lengkap dengan dugaan akar masalah (flex `min-width: auto` pada slider). Setelah pemilik minta pembuktian akar masalah sebelum memperbaiki, pengukuran ulang lewat CDP (`document.documentElement.scrollWidth` vs `window.innerWidth`, ditambah `Page.getLayoutMetrics`, ditambah screenshot dari sesi CDP yang sama) di kelima halaman publik menunjukkan **nol overflow** di semuanya. Bug yang dilaporkan sebelumnya adalah artefak alat ukur, bukan cacat kode — jangan mengejar bug hantu yang sama lagi kalau `--window-size` menunjukkan sesuatu yang terlihat terpotong di HP.

**Konsekuensi yang diterima.** Verifikasi lebar HP butuh skrip CDP (Node.js, `WebSocket` dan `fetch` bawaan, tanpa paket tambahan) alih-alih satu baris perintah `chrome --screenshot`. Sedikit lebih rumit untuk dijalankan, tapi hasilnya bisa dipercaya.

## Galeri di beranda memakai "id menurun", bukan `urutan` manual (Fase 5)

**Keputusan.** Bagian galeri di beranda (`BerandaController::index()`) mengambil foto lewat `Photo::orderByDesc('id')->limit(12)`, bukan `orderBy('urutan')` yang dipakai grid `/galeri`.

**Kenapa.** Bagian itu berlabel "Galeri terbaru" — maksudnya benar-benar foto yang paling baru ditambahkan. Kolom `urutan` di `photos` adalah pengurutan manual milik pemilik untuk tata letak grid penuh di `/galeri` (bisa diseret bebas, tidak berkorelasi dengan kapan foto diunggah), jadi memakainya di sini akan menampilkan foto-foto sesuai urutan pilihan pemilik, bukan yang terbaru — dua konsep berbeda yang kebetulan sama-sama berupa angka.

**Konsekuensi yang diterima.** Kalau pemilik mengurutkan ulang grid `/galeri` secara manual, bagian "Galeri terbaru" di beranda tidak ikut berubah urutannya — itu memang dimaksudkan, karena keduanya menjawab pertanyaan berbeda ("apa yang baru" vs "urutan tampilan pilihan pemilik").

## PipelineGambar wajib gagal bersuara, tidak boleh mengembalikan path berkas hantu (perbaikan pasca-Fase 5)

**Keputusan.** `PipelineGambar::proses()` memeriksa **setiap** penulisan berkas sebelum path-nya boleh dikembalikan: nilai balik `makeDirectory()`, nilai balik `imagewebp()`, lalu keberadaan dan ukuran berkas hasilnya di disk. Kegagalan mana pun melempar `RuntimeException` berpesan jelas, dan direktori yang terlanjur berisi turunan setengah jadi dihapus lebih dulu. Jaminannya: **kalau `proses()` mengembalikan path, ketiga berkasnya sudah ada dan berisi.**

**Kenapa.** Sebelum ini tidak ada satu pun pemeriksaan. `imagewebp()` mengembalikan `false` tanpa memicu exception kalau tujuannya tidak bisa ditulis, dan kedua disk di `config/filesystems.php` menyetel `'throw' => false` sehingga kegagalan di level Flysystem juga senyap. Akibatnya pemanggil — seeder maupun `FileUpload` Filament — tetap menerima array path yang tampak wajar dan menyimpannya ke basis data seolah berhasil. Basis data jadi menunjuk berkas yang tidak pernah ditulis, dan tidak ada satu pun galat yang muncul di mana pun.

**Gejalanya di lapangan.** Seluruh gambar galeri, sampul journal, dan gambar project tidak tampil — hanya teks `alt`. `storage/app/public` kosong total padahal 20 baris `photos`, satu `posts.cover`, dan empat elemen `projects.gambar` menunjuk ke sana. Seperti kasus aset Filament di atas, **smoke test tidak menangkap ini sama sekali**: halaman tetap membalas 200, yang rusak hanya isinya.

**Yang tidak bisa dibuktikan, dan sengaja tidak diklaim.** Apakah seed 6 September itu gagal menulis secara senyap, atau berkasnya sempat ditulis lalu terhapus belakangan, **tidak bisa dipastikan lagi** dari bukti yang tersisa. Yang pasti dan yang diperbaiki adalah cacatnya: kode lama tidak bisa membedakan keduanya, karena tidak pernah memeriksa. Unggahan lewat panel pada 7 September berkasnya utuh — pipeline-nya sendiri berfungsi. Jangan menulis ulang sejarah ini jadi "seed-nya yang rusak"; buktinya tidak sampai ke sana.

**Konsekuensi yang diterima.** Unggahan yang gagal sekarang menggagalkan penyimpanan record-nya, bukan menyimpan record dengan gambar rusak. Itu memang yang diinginkan. Baris hantu yang sudah telanjur ada sejak sebelum perbaikan ini tidak ikut dibersihkan otomatis — satu-satunya cara membereskannya adalah `migrate:fresh --seed`, yang juga menghapus isi yang diketik manual lewat panel. Jangan jalankan itu tanpa persetujuan pemilik.

## Rute `/storage/` bawaan Laravel dimatikan supaya berkas hilang membalas 404, bukan 403 (perbaikan pasca-Fase 5)

**Keputusan.** Disk `local` di `config/filesystems.php` disetel `'serve' => false`.

**Kenapa.** Dengan `'serve' => true` (nilai bawaan stub Laravel), `FilesystemServiceProvider` mendaftarkan rute `GET /storage/{path}` bernama `storage.local` untuk disk itu — **URI yang persis sama** dengan symlink `public/storage` milik disk `public`. Dua mekanisme berbeda berebut satu prefix. Disk `local` menyimpan berkas privat (`storage/app/private`) dan tidak pernah disajikan lewat HTTP, jadi rutenya memang tidak dibutuhkan.

**Jebakan yang perlu diketahui — ini sempat menyesatkan diagnosis.** Berkas yang benar-benar ada tidak pernah menyentuh rute itu: web server (juga server bawaan `php artisan serve` lewat `file_exists()` di `server.php`) menyajikannya lebih dulu. Yang jatuh ke rute itu hanya berkas yang **tidak** ada — dan `ServeFile` menjawabnya **403 Forbidden**, bukan 404, karena `visibility` disk `local` bukan `public` dan permintaannya tidak bertanda tangan. Efeknya: berkas hilang menyamar jadi masalah izin akses. Saat menelusuri gambar yang tidak tampil, 403 itu mengarahkan dugaan ke symlink dan hak akses berkas — padahal berkasnya memang tidak pernah ada. Kalau suatu saat ada yang menghidupkan `serve` lagi untuk disk mana pun, pastikan URI-nya tidak menabrak `/storage/` milik disk `public`.

**Konsekuensi yang diterima.** Tidak ada rute aplikasi yang melayani `/storage/`; seluruhnya bergantung pada symlink `public/storage` yang dibuat `php artisan storage:link`. Kalau symlink itu hilang, gambar mati total dan jawabannya 404 — bukan lagi 403 yang membingungkan. Berkas privat di disk `local` tidak punya cara disajikan lewat HTTP sama sekali; kalau suatu saat dibutuhkan (mis. unduhan bertanda tangan), hidupkan `serve` dengan `url` sendiri yang berbeda dari `/storage`.
