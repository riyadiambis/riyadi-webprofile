{{--
    Overlay lightbox terpadu, dipasang sekali di layouts/publik.blade.php
    dan dipakai bersama oleh gambar di dalam tulisan journal (Fase 2B)
    dan foto galeri (Fase 4). Gambar artikel membukanya tanpa navigasi
    dan tanpa caption — persis perilaku Fase 2B; tombol maju/mundur dan
    caption hanya aktif untuk galeri.
--}}
<div
    data-lightbox-overlay
    class="hidden fixed inset-0 z-50 items-center justify-center bg-ink/90 p-4"
    role="dialog"
    aria-modal="true"
    aria-label="Tampilan gambar"
    inert
>
    <button
        type="button"
        data-lightbox-close
        class="absolute right-4 top-4 text-3xl leading-none text-paper"
        aria-label="Tutup gambar"
    >&times;</button>

    <button
        type="button"
        data-lightbox-sebelum
        class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-ink/50 px-3 py-1 text-3xl leading-none text-paper"
        aria-label="Foto sebelumnya"
        hidden
    >&lsaquo;</button>

    <button
        type="button"
        data-lightbox-sesudah
        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-ink/50 px-3 py-1 text-3xl leading-none text-paper"
        aria-label="Foto berikutnya"
        hidden
    >&rsaquo;</button>

    <figure class="relative max-h-full max-w-full">
        <img data-lightbox-image src="" alt="" class="max-h-full max-w-full rounded-kartu">
        <figcaption
            data-lightbox-caption
            class="absolute inset-x-0 bottom-0 rounded-b-kartu bg-ink/70 px-4 py-2 text-center text-sm text-paper"
            hidden
        ></figcaption>
    </figure>
</div>
