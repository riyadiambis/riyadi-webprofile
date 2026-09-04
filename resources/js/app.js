// Lightbox seadanya untuk gambar di dalam tulisan journal. Vanilla JS,
// tanpa paket pihak ketiga, sesuai docs/fitur/01b-journal-publik.md.
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.querySelector('[data-lightbox-overlay]');

    if (!overlay) {
        return;
    }

    const gambar = overlay.querySelector('[data-lightbox-image]');

    function buka(src, alt) {
        gambar.src = src;
        gambar.alt = alt || '';
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function tutup() {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        gambar.src = '';
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-lightbox-trigger]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            buka(tombol.dataset.src, tombol.dataset.alt);
        });
    });

    overlay.querySelectorAll('[data-lightbox-close]').forEach((tombol) => {
        tombol.addEventListener('click', tutup);
    });

    // Klik di luar gambar (langsung pada latar overlay) juga menutup.
    overlay.addEventListener('click', (peristiwa) => {
        if (peristiwa.target === overlay) {
            tutup();
        }
    });

    document.addEventListener('keydown', (peristiwa) => {
        if (peristiwa.key === 'Escape' && !overlay.classList.contains('hidden')) {
            tutup();
        }
    });
});
