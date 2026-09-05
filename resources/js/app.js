// Lightbox terpadu, dipasang satu kali di layout dan dipakai bersama
// oleh gambar di dalam tulisan journal (Fase 2B) dan foto galeri
// (Fase 4). Vanilla JS, tanpa paket pihak ketiga.
//
// Gambar artikel membuka satu gambar saja: tanpa navigasi dan tanpa
// caption, persis perilaku Fase 2B. Foto galeri membuka grup dan
// menambahkan tombol maju/mundur, panah keyboard, geser di layar
// sentuh, dan caption.
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.querySelector('[data-lightbox-overlay]');

    if (!overlay) {
        return;
    }

    const gambar = overlay.querySelector('[data-lightbox-image]');
    const caption = overlay.querySelector('[data-lightbox-caption]');
    const tombolSebelum = overlay.querySelector('[data-lightbox-sebelum]');
    const tombolSesudah = overlay.querySelector('[data-lightbox-sesudah]');
    const tombolTutup = overlay.querySelector('[data-lightbox-close]');

    let grup = [];
    let indeks = 0;
    let fokusSemula = null;

    function tampilkan() {
        const item = grup[indeks];

        gambar.src = item.src;
        gambar.alt = item.alt || '';

        if (item.caption) {
            caption.textContent = item.caption;
            caption.hidden = false;
        } else {
            caption.hidden = true;
        }

        const adaNavigasi = grup.length > 1;
        tombolSebelum.hidden = !adaNavigasi;
        tombolSesudah.hidden = !adaNavigasi;
    }

    function buka(daftar, indeksBaru) {
        fokusSemula = document.activeElement;
        grup = daftar;
        indeks = indeksBaru;

        tampilkan();
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        overlay.removeAttribute('inert');
        document.body.classList.add('overflow-hidden');
        tombolTutup.focus();
    }

    function tutup() {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        overlay.setAttribute('inert', '');
        gambar.src = '';
        document.body.classList.remove('overflow-hidden');

        if (fokusSemula) {
            fokusSemula.focus();
        }
    }

    function geser(arah) {
        if (grup.length < 2) {
            return;
        }

        indeks = (indeks + arah + grup.length) % grup.length;
        tampilkan();
    }

    const pemicuGaleri = [...document.querySelectorAll('[data-lightbox-trigger][data-galeri-grup]')];

    document.querySelectorAll('[data-lightbox-trigger]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            if (tombol.dataset.galeriGrup !== undefined) {
                buka(
                    pemicuGaleri.map((pemicu) => ({
                        src: pemicu.dataset.src,
                        alt: pemicu.dataset.alt || '',
                        caption: pemicu.dataset.caption || '',
                    })),
                    Number(tombol.dataset.indeks) || 0,
                );

                return;
            }

            buka(
                [{ src: tombol.dataset.src, alt: tombol.dataset.alt || '', caption: '' }],
                0,
            );
        });
    });

    overlay.querySelectorAll('[data-lightbox-close]').forEach((tombol) => {
        tombol.addEventListener('click', tutup);
    });

    tombolSebelum.addEventListener('click', () => geser(-1));
    tombolSesudah.addEventListener('click', () => geser(1));

    // Klik di luar gambar (langsung pada latar overlay) juga menutup.
    overlay.addEventListener('click', (peristiwa) => {
        if (peristiwa.target === overlay) {
            tutup();
        }
    });

    document.addEventListener('keydown', (peristiwa) => {
        if (overlay.classList.contains('hidden')) {
            return;
        }

        if (peristiwa.key === 'Escape') {
            tutup();
        }

        if (peristiwa.key === 'ArrowLeft') {
            geser(-1);
        }

        if (peristiwa.key === 'ArrowRight') {
            geser(1);
        }
    });

    // Navigasi geser di layar sentuh, khusus mode galeri.
    let sentuhAwalX = null;

    overlay.addEventListener(
        'touchstart',
        (peristiwa) => {
            if (peristiwa.touches.length === 1) {
                sentuhAwalX = peristiwa.touches[0].clientX;
            }
        },
        { passive: true },
    );

    overlay.addEventListener('touchend', (peristiwa) => {
        if (sentuhAwalX === null) {
            return;
        }

        const akhirX = peristiwa.changedTouches[0].clientX;
        const jarak = akhirX - sentuhAwalX;
        sentuhAwalX = null;

        if (Math.abs(jarak) < 40) {
            return;
        }

        geser(jarak < 0 ? 1 : -1);
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
