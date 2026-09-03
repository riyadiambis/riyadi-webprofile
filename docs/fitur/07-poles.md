# Fitur 07 — Poles

Fase roadmap: 7. Fase terakhir v1, dikerjakan setelah situs sudah hidup di domain.

## Cakupan
- Meta tag dan Open Graph di setiap halaman, supaya rapi saat dibagikan di WhatsApp dan LinkedIn.
- Sitemap.
- RSS untuk journal.
- Halaman 404 yang dibuat sungguh-sungguh, bukan bawaan.
- Pemeriksaan Lighthouse dan perbaikan yang muncul. Target performa mobile minimal 90.

## Opsional
- **Tag untuk journal.** Tabel `tags` dan pivot `post_tag` sesuai model data di PRD, ditambah penyaringan di halaman `/journal`.

Ini benar-benar opsional. Kalau waktu atau tenaga habis, buang saja dan tutup fase ini tanpa tag. Jangan menunda merge Fase 7 hanya demi tag, dan jangan mengorbankan poin di bagian cakupan untuk mengerjakannya.

## Kriteria lolos
Tautan situs dibagikan di WhatsApp menampilkan pratinjau yang rapi, skor Lighthouse performa mobile minimal 90, dan halaman 404 tampil benar.

Ditambah smoke test hijau sesuai aturan tetap di `CLAUDE.md`: seluruh rute publik tetap membalas 200 dan `migrate:fresh --seed` berjalan bersih.

Tag tidak masuk hitungan kriteria lolos.
