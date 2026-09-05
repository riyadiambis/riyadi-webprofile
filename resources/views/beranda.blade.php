@extends('layouts.publik')

@section('judul', 'Beranda')

@section('konten')
    {{--
        Susunan mengikuti docs/fitur/04-beranda.md: perkenalan, project
        pilihan, tulisan pilihan, galeri terbaru, penutup. Paragraf
        perkenalan dan penutup SELURUHNYA dari tabel site_texts lewat
        SiteText::nilai() — dilarang menulis teksnya di sini. Foto
        profil dan tautan sosial masih isian sementara di
        config/site.php (aturan G, lihat berkas itu).
    --}}

    {{-- Perkenalan --}}
    <section class="grid gap-8 md:grid-cols-[240px_1fr] md:items-center md:gap-12">
        @if (config('site.foto_profil'))
            <img
                src="{{ Storage::url(config('site.foto_profil')) }}"
                alt="Foto Rahmat Riyadi"
                class="w-40 md:w-60 aspect-square object-cover rounded-kartu border-tegas border-ink shadow-offset justify-self-center md:justify-self-start"
            >
        @else
            <div class="w-40 md:w-60 aspect-square rounded-kartu border-tegas border-ink shadow-offset bg-card flex flex-col items-center justify-center gap-2 justify-self-center md:justify-self-start">
                <span class="font-judul text-5xl font-semibold text-ink-soft">RR</span>
                <span class="label-bagian">Foto sementara</span>
            </div>
        @endif

        {{-- HP: menumpuk, semuanya rata tengah. Mulai tablet (md):
             dua kolom, foto kiri, teks kanan rata kiri. --}}
        <div class="text-center md:text-left">
            <p class="label-bagian">Tentang saya</p>
            <h1 class="font-judul text-4xl md:text-5xl font-semibold mt-3 leading-tight">
                Rahmat Riyadi
            </h1>

            @if ($perkenalan)
                <p class="mt-5 max-w-baca text-ink-soft leading-relaxed mx-auto md:mx-0">
                    {{ $perkenalan->nilai() }}
                </p>
            @endif

            {{--
                Tombol kertas timbul, gaya sama dengan bingkai foto
                profil: border tegas + bayangan offset solid. Efek
                tertekan juga muncul saat hover/sentuh lewat
                .tekan-hover (bukan .tekan yang dipakai elemen lain,
                supaya tidak ikut berubah). Rata tengah dan boleh
                turun baris di HP, tanpa dipaksa satu baris.
            --}}
            <ul class="mt-6 flex flex-wrap justify-center gap-3 text-sm font-semibold md:justify-start">
                <li><a href="{{ config('site.sosial.linkedin') }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-4 py-2 text-ink shadow-offset tekan-hover">LinkedIn</a></li>
                <li><a href="{{ config('site.sosial.github') }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-4 py-2 text-ink shadow-offset tekan-hover">GitHub</a></li>
                <li><a href="{{ config('site.sosial.tiktok') }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-4 py-2 text-ink shadow-offset tekan-hover">TikTok</a></li>
                <li><a href="{{ config('site.sosial.instagram') }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-4 py-2 text-ink shadow-offset tekan-hover">Instagram</a></li>
                <li><a href="mailto:{{ config('site.email') }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-4 py-2 text-ink shadow-offset tekan-hover">Email</a></li>
            </ul>
        </div>
    </section>

    {{-- Project pilihan. Bagian kosong disembunyikan seluruhnya,
         bukan tampil sebagai judul dengan slider kosong. --}}
    @if ($projects->isNotEmpty())
        <section class="mt-bagian-hp md:mt-bagian">
            <div class="flex items-baseline justify-between gap-6">
                <div>
                    <p class="label-bagian">Pilihan</p>
                    <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">Project pilihan</h2>
                </div>
                <a href="{{ route('project') }}" class="text-sm font-semibold text-accent-alt hover:underline underline-offset-2 whitespace-nowrap">Lihat semua &rarr;</a>
            </div>

            <x-slider-beranda label="Project pilihan" class="mt-8">
                @foreach ($projects as $project)
                    <div class="w-72 md:w-80 shrink-0 snap-start">
                        <x-kartu-project :project="$project" />
                    </div>
                @endforeach
            </x-slider-beranda>
        </section>
    @endif

    {{-- Tulisan pilihan --}}
    @if ($posts->isNotEmpty())
        <section class="mt-bagian-hp md:mt-bagian">
            <div class="flex items-baseline justify-between gap-6">
                <div>
                    <p class="label-bagian">Pilihan</p>
                    <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">Tulisan pilihan</h2>
                </div>
                <a href="{{ route('journal') }}" class="text-sm font-semibold text-accent-alt hover:underline underline-offset-2 whitespace-nowrap">Lihat semua &rarr;</a>
            </div>

            <x-slider-beranda label="Tulisan pilihan" class="mt-8">
                @foreach ($posts as $post)
                    <div class="w-72 md:w-80 shrink-0 snap-start">
                        <x-kartu-post :post="$post" />
                    </div>
                @endforeach
            </x-slider-beranda>
        </section>
    @endif

    {{-- Galeri terbaru. Foto memakai atribut lightbox yang sama dengan
         halaman /galeri, jadi klik membuka overlay Fase 4 yang sama
         tanpa JavaScript baru. --}}
    @if ($photos->isNotEmpty())
        <section class="mt-bagian-hp md:mt-bagian">
            <div class="flex items-baseline justify-between gap-6">
                <div>
                    <p class="label-bagian">Terbaru</p>
                    <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">Galeri</h2>
                </div>
                <a href="{{ route('galeri') }}" class="text-sm font-semibold text-accent-alt hover:underline underline-offset-2 whitespace-nowrap">Lihat semua &rarr;</a>
            </div>

            <x-slider-beranda label="Galeri terbaru" class="mt-8">
                @foreach ($photos as $index => $photo)
                    @php($urls = $photo->gambarUrls())

                    <button
                        type="button"
                        data-lightbox-trigger
                        data-galeri-grup
                        data-indeks="{{ $index }}"
                        data-src="{{ $urls['penuh'] }}"
                        data-alt="{{ $photo->caption ?: 'Foto galeri' }}"
                        data-caption="{{ $photo->caption }}"
                        class="relative aspect-square w-40 md:w-44 shrink-0 snap-start cursor-zoom-in overflow-hidden rounded-kartu border-tegas border-ink shadow-offset"
                        aria-label="Perbesar foto"
                    >
                        <img
                            src="{{ $urls['thumb'] }}"
                            alt="{{ $photo->caption ?: 'Foto galeri' }}"
                            loading="{{ $index < 6 ? 'eager' : 'lazy' }}"
                            decoding="async"
                            class="h-full w-full object-cover"
                        >
                    </button>
                @endforeach
            </x-slider-beranda>
        </section>
    @endif

    {{-- Penutup --}}
    @if ($penutup)
        <section class="mt-bagian-hp md:mt-bagian bg-card border-tegas border-ink rounded-kartu shadow-offset p-8 md:p-12 text-center">
            <p class="label-bagian">Terhubung</p>
            <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">Mari mengobrol</h2>
            <p class="mt-4 max-w-baca mx-auto text-ink-soft leading-relaxed">
                {{ $penutup->nilai() }}
            </p>
            <a
                href="mailto:{{ config('site.email') }}"
                class="inline-block mt-6 px-6 py-2.5 rounded-kecil border-tegas border-ink bg-accent font-semibold shadow-offset tekan"
            >
                Kirim email
            </a>
        </section>
    @endif
@endsection
