{{--
    Slider beranda (Fase 5), dipakai tiga kali: project pilihan,
    tulisan pilihan, dan galeri terbaru.

    Sengaja DIAM: tidak bergerak sendiri — hanya kartu project di
    dalamnya yang tetap berganti gambar otomatis, perilaku Fase 3
    (lihat docs/fitur/04-beranda.md). Digeser dengan sentuhan di HP
    (guliran native browser dengan snap) dan tombol panah di desktop
    (ditangani app.js). Kenapa kode geser lightbox Fase 4 tidak
    dipakai bersama: lihat docs/keputusan.md.

    Pemanggil membungkus tiap kartu dengan lebar tetap, mis.
    <div class="w-72 shrink-0 snap-start">.
--}}
@props(['label' => null])

<div {{ $attributes->merge(['class' => 'relative']) }} data-slider-geser>
    <button
        type="button"
        data-slider-kiri
        class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2 z-10 items-center justify-center rounded-full bg-card border-tegas border-ink shadow-offset w-10 h-10 text-2xl leading-none transition-colors hover:text-accent-alt"
        aria-label="Geser ke kiri"
        hidden
    >&lsaquo;</button>

    <div
        data-slider-layar
        class="flex gap-6 overflow-x-auto snap-x snap-mandatory scrollbar-tersembunyi pb-3"
        @if ($label) aria-label="{{ $label }}" @endif
    >
        {{ $slot }}
    </div>

    <button
        type="button"
        data-slider-kanan
        class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 z-10 items-center justify-center rounded-full bg-card border-tegas border-ink shadow-offset w-10 h-10 text-2xl leading-none transition-colors hover:text-accent-alt"
        aria-label="Geser ke kanan"
        hidden
    >&rsaquo;</button>
</div>
