{{--
    Overlay lightbox terpadu, dipasang sekali di layouts/publik.blade.php
    dan dipakai bersama oleh gambar di dalam tulisan journal (Fase 2B)
    dan foto galeri (Fase 4). Gambar artikel membukanya tanpa navigasi
    dan tanpa caption — persis perilaku Fase 2B; tombol maju/mundur dan
    caption hanya aktif untuk galeri.

    Tata letak tiga baris (perbaikan pasca-Fase 4): baris atas tombol
    tutup, baris tengah area gambar dengan gutter kiri-kanan untuk
    tombol maju/mundur, baris bawah caption dan tautan ukuran penuh.
    Tinggi maksimum gambar otomatis sebatas sisa baris tengah, dan
    object-contain menjaga rasio asli — foto potret maupun lanskap
    selalu muat utuh. Tidak ada scroll di dalam overlay; kunci scroll
    halaman dipegang app.js.
--}}
<div
    data-lightbox-overlay
    class="hidden fixed inset-0 z-50 flex-col bg-ink/90"
    role="dialog"
    aria-modal="true"
    aria-label="Tampilan gambar"
    inert
>
    <div class="flex h-16 shrink-0 items-center justify-end px-4">
        <button
            type="button"
            data-lightbox-close
            class="flex items-center justify-center h-10 w-10 rounded-kecil bg-paper/20 hover:bg-paper/30 text-paper text-2xl leading-none transition-colors"
            aria-label="Tutup gambar"
        >&times;</button>
    </div>

    <div class="relative min-h-0 flex-1">
        <button
            type="button"
            data-lightbox-sebelum
            class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center justify-center h-10 w-10 rounded-kecil bg-paper/20 hover:bg-paper/30 text-paper text-2xl leading-none transition-colors"
            aria-label="Foto sebelumnya"
            hidden
        >&lsaquo;</button>

        <button
            type="button"
            data-lightbox-sesudah
            class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center justify-center h-10 w-10 rounded-kecil bg-paper/20 hover:bg-paper/30 text-paper text-2xl leading-none transition-colors"
            aria-label="Foto berikutnya"
            hidden
        >&rsaquo;</button>

        {{-- Gambar dibungkus div, bukan diposisikan langsung: elemen
             replaced seperti <img> dengan width/height auto memakai
             ukuran intrinsik, jadi inset kiri-kanan tidak
             meregangkannya. Div non-replaced diregangkan insets,
             lalu <img> mengisinya dengan h-full w-full dan
             object-contain. --}}
        <div class="absolute inset-y-0 left-16 right-16 md:left-20 md:right-20">
            <img
                data-lightbox-image
                src="" alt=""
                class="h-full w-full rounded-kartu object-contain"
            >
        </div>
    </div>

    <div class="flex shrink-0 flex-col gap-3 bg-ink/70 px-4 py-4 items-center text-center text-paper">
        <p data-lightbox-caption class="text-sm" hidden></p>
        <a
            data-lightbox-penuh
            href="#"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center h-10 px-4 rounded-kecil bg-paper/20 hover:bg-paper/30 text-paper text-sm font-semibold transition-colors"
        >Buka ukuran penuh</a>
    </div>
</div>
