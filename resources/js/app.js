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

// Pergantian gambar otomatis pada kartu project, sesuai
// docs/fitur/02-project.md. Vanilla JS, tanpa paket pihak ketiga.
//
// Tiap kartu punya timer sendiri dengan jeda awal acak — bukan satu
// interval global — supaya kartu-kartu tidak berganti serentak. Kalau
// prefers-reduced-motion aktif, blok ini tidak memasang timer sama
// sekali; gambar pertama tetap tampil diam.
document.addEventListener('DOMContentLoaded', () => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    document.querySelectorAll('[data-slider-gambar]').forEach((kontainer) => {
        const gambar = kontainer.querySelectorAll('img');

        if (gambar.length < 2) {
            return;
        }

        const jeda = parseInt(kontainer.dataset.jeda, 10) || 3000;
        let indeks = 0;

        function ganti() {
            gambar[indeks].classList.replace('opacity-100', 'opacity-0');
            indeks = (indeks + 1) % gambar.length;
            gambar[indeks].classList.replace('opacity-0', 'opacity-100');
        }

        // Jeda awal acak per kartu, bukan interval global bersama.
        const jedaAwal = Math.random() * jeda;

        setTimeout(() => {
            ganti();
            setInterval(ganti, jeda);
        }, jedaAwal);
    });
});
