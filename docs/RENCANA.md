# Rencana Pemolesan Antarmuka (Branch `poles-antarmuka`)

Berikut adalah hasil temuan dan rencana perbaikan antarmuka berdasarkan panduan `design-tokens.md` serta aturan dwibahasa:

## 1. Tautan "Lihat semua" di Beranda
*   **Temuan:** Posisi sekarang terlempar ke tepi kanan layar di desktop (karena `justify-between`) dan desainnya berupa teks polos biasa.
*   **Rencana:** Saya akan menggabungkan elemen judul dan tautan ini dalam flex container baru (`flex items-end gap-4`), sehingga posisinya saling berdekatan. Tautan tersebut akan diberi gaya sekeluarga dengan tombol sosial: kotak berekor `rounded-kecil`, `border-tegas`, memiliki `shadow-offset`, dan memakai interaksi `tekan-hover`.

## 2. Footer Dwibahasa dengan Aturan Terbatas
*   **Temuan:** Teks "Dibangun sendiri, di-hosting sendiri" masih berupa _hardcode_ Indonesia.
*   **Rencana:** Saya akan mengekstrak teks ini ke `lang/id/footer.php` dan `lang/en/footer.php`.
*   **Penanganan halaman terkunci (/journal & /galeri):** Meskipun `app()->getLocale()` dapat bernilai `en` di beranda, halaman journal/galeri sudah mengunci tag `<html lang="id">` secara manual di Blade (lewat `@section('lang', 'id')`). Saya akan melemparkan locale khusus ini ke fungsi terjemahan Blade: `__('footer.dibangun', [], $__env->yieldContent('lang', app()->getLocale()))`. Dengan begitu, footer akan selalu selaras dengan aturan spesifik tiap halaman secara otomatis.

## 3. Laporan Elemen Interaktif Halaman Publik
Berikut adalah hasil sapuan semua elemen interaktif yang **saat ini belum memakai** gaya kertas timbul utama (kartu dengan `border-tegas`, `shadow-offset`, dan animasi `tekan` / `tekan-hover`). **Mohon putuskan mana yang ingin diubah:**

1.  **Tautan Navigasi Header:** Hanya berupa teks polos (`text-ink-soft hover:text-accent-alt`).
2.  **Tombol Pengalih Bahasa (ID/EN):** Sudah ber-border, tetapi **tanpa** bayangan `shadow-offset` dan tanpa interaksi `tekan`.
3.  **Panah Slider Beranda:** Sudah ber-border dan berbayang, tetapi bentuknya **bulat penuh** (`rounded-full`, bukan sudut kertas `rounded-kecil`) dan **hanya** punya interaksi `hover:text-accent-alt` (bukan interaksi ditekan ke bawah/`tekan-hover`).
4.  **Lightbox: Tombol Sebelumnya & Sesudah:** Lingkaran transparan polos (`bg-ink/50` text putih), melayang di atas foto tanpa border.
5.  **Lightbox: Tombol Tutup (X):** Teks besar polos di sudut atas.
6.  **Lightbox: Tautan "Buka ukuran penuh":** Tautan teks kecil bergaris bawah.
7.  **Pagination Halaman Journal (Kertas):** Navigasi "<- Sebelumnya" / "Berikutnya ->" berupa teks polos (`label-bagian`).

*(Apakah elemen-elemen ini ingin diseragamkan ke gaya tombol timbul, atau dibiarkan dengan alasan hierarki visual?)*

## 4. Keadaan Hover & Fokus Keyboard
*   **Temuan:** Banyak interaksi klik (seperti foto galeri, kartu project) yang tidak memberikan respon visual saat di-*hover*. Selain itu, navigasi menggunakan keyboard (`Tab`) menampilkan ring bawaan browser (biasanya biru/garis tipis) yang menabrak estetika kertas.
*   **Rencana:**
    *   **Fokus:** Menambahkan state `focus-visible` global terpusat di `app.css` (misalnya memakai `outline: 2px dashed var(--color-ink); outline-offset: 4px;`) pada semua tautan dan tombol agar terlihat organik.
    *   **Hover:** Saya akan melengkapi elemen interaktif penting (seperti kartu galeri yang belum punya respons visual saat disorot) dengan sedikit efek hover.

## 5. Aksen Stabilo (Temuan Audit 7)
*   **Temuan:** `@utility stabilo` (berupa `linear-gradient` kuning) masih ada di CSS tapi pemakaiannya nol pasca penulisan ulang beranda.
*   **Usulan (Pilih Salah Satu):**
    *   **Opsi A (Dipakai Kembali):** Tempelkan pada teks **"Rahmat Riyadi"** (nama utama) atau label **"Tentang saya"** di hero bagian Beranda, mengingat ini adalah elemen pemikat perkenalan.
    *   **Opsi B (Dihapus):** Jika dirasa mengurangi ketenangan desain, saya akan mencabut token `--stabilo-mulai` dan `@utility stabilo` dari `app.css`.
