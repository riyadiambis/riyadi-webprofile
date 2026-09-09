{{--
    Tautan kembali ke situs publik, tampil di bawah form login panel.
    Memakai kelas utilitas Filament (bukan token desain situs publik)
    supaya ikut tema panel, terang maupun gelap.
--}}
<div class="text-center">
    <a
        href="{{ route('beranda') }}"
        class="text-sm text-gray-500 underline underline-offset-2 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
    >
        &larr; Kembali ke situs
    </a>
</div>
