@extends('layouts.publik')

@section('judul', 'Beranda')

@section('konten')
    {{--
        Susunan mengikuti docs/fitur/04-beranda.md: perkenalan, project
        unggulan, tulisan unggulan, galeri terbaru, penutup. Paragraf
        perkenalan dan penutup SELURUHNYA dari tabel site_texts lewat
        SiteText::nilai() — dilarang menulis teksnya di sini. Foto
        profil dan tautan sosial datang dari tabel `profiles`, disunting
        lewat halaman Beranda di panel; config/site.php sudah
        dipensiunkan.

        Judul bagian dan label kecilnya ikut dwibahasa lewat lang/,
        lihat docs/fitur/05-dwibahasa.md.
    --}}

    {{-- Perkenalan --}}
    <section class="grid gap-8 md:grid-cols-[240px_1fr] md:items-center md:gap-12">
        @php($fotoUrls = $profil->fotoUrls())

        @if ($fotoUrls)
            <img
                src="{{ $fotoUrls['sedang'] }}"
                alt="{{ __('beranda.foto_profil_alt') }}"
                class="w-40 md:w-60 aspect-square object-cover rounded-kartu border-tegas border-ink shadow-offset justify-self-center md:justify-self-start"
            >
        @else
            <div class="w-40 md:w-60 aspect-square rounded-kartu border-tegas border-ink shadow-offset bg-card flex flex-col items-center justify-center gap-2 justify-self-center md:justify-self-start">
                <span class="font-judul text-5xl font-semibold text-ink-soft">RR</span>
                <span class="label-bagian">{{ __('beranda.foto_sementara') }}</span>
            </div>
        @endif

        {{-- HP: menumpuk, semuanya rata tengah. Mulai tablet (md):
             dua kolom, foto kiri, teks kanan rata kiri. --}}
        <div class="text-center md:text-left">
            <p class="label-bagian">{{ __('beranda.tentang_saya') }}</p>
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
            @if ($tautanSosial)
                <ul class="mt-6 flex flex-wrap justify-center gap-3 text-sm font-semibold md:justify-start">
                    @foreach ($tautanSosial as $tautan)
                        <li><a href="{{ $tautan['url'] }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-4 py-2 text-ink shadow-offset tekan-hover">{{ $tautan['label'] }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- Project unggulan. Bagian kosong disembunyikan seluruhnya,
         bukan tampil sebagai judul dengan slider kosong. --}}
    @if ($projects->isNotEmpty())
        <section class="mt-bagian-hp md:mt-bagian">
            <div class="flex items-baseline justify-between gap-6">
                <div>
                    <p class="label-bagian">{{ __('beranda.unggulan') }}</p>
                    <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">{{ __('beranda.project_unggulan') }}</h2>
                </div>
                <a href="{{ route('project') }}" class="text-sm font-semibold text-accent-alt hover:underline underline-offset-2 whitespace-nowrap">{{ __('beranda.lihat_semua') }} &rarr;</a>
            </div>

            <x-slider-beranda :label="__('beranda.project_unggulan')" class="mt-8">
                @foreach ($projects as $project)
                    <div class="w-72 md:w-80 shrink-0 snap-start">
                        <x-kartu-project :project="$project" />
                    </div>
                @endforeach
            </x-slider-beranda>
        </section>
    @endif

    {{-- Tulisan unggulan --}}
    @if ($posts->isNotEmpty())
        <section class="mt-bagian-hp md:mt-bagian">
            <div class="flex items-baseline justify-between gap-6">
                <div>
                    <p class="label-bagian">{{ __('beranda.unggulan') }}</p>
                    <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">{{ __('beranda.tulisan_unggulan') }}</h2>
                </div>
                <a href="{{ route('journal') }}" class="text-sm font-semibold text-accent-alt hover:underline underline-offset-2 whitespace-nowrap">{{ __('beranda.lihat_semua') }} &rarr;</a>
            </div>

            <x-slider-beranda :label="__('beranda.tulisan_unggulan')" class="mt-8">
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
                    <p class="label-bagian">{{ __('beranda.terbaru') }}</p>
                    <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">{{ __('beranda.galeri') }}</h2>
                </div>
                <a href="{{ route('galeri') }}" class="text-sm font-semibold text-accent-alt hover:underline underline-offset-2 whitespace-nowrap">{{ __('beranda.lihat_semua') }} &rarr;</a>
            </div>

            <x-slider-beranda :label="__('beranda.galeri')" class="mt-8">
                @foreach ($photos as $index => $photo)
                    @php($urls = $photo->gambarUrls())

                    <button
                        type="button"
                        data-lightbox-trigger
                        data-galeri-grup
                        data-indeks="{{ $index }}"
                        data-src="{{ $urls['penuh'] }}"
                        data-alt="{{ $photo->caption ?: __('beranda.foto_galeri') }}"
                        data-caption="{{ $photo->caption }}"
                        class="relative aspect-square w-40 md:w-44 shrink-0 snap-start cursor-zoom-in overflow-hidden rounded-kartu border-tegas border-ink shadow-offset"
                        aria-label="{{ __('beranda.perbesar_foto') }}"
                    >
                        <img
                            src="{{ $urls['thumb'] }}"
                            alt="{{ $photo->caption ?: __('beranda.foto_galeri') }}"
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
            <p class="label-bagian">{{ __('beranda.terhubung') }}</p>
            <h2 class="font-judul text-2xl md:text-3xl font-semibold mt-2">{{ __('beranda.mari_mengobrol') }}</h2>
            <p class="mt-4 max-w-baca mx-auto text-ink-soft leading-relaxed">
                {{ $penutup->nilai() }}
            </p>

            {{-- Tombol email hanya muncul kalau alamatnya sudah diisi
                 lewat panel, aturan yang sama dengan tombol sosial. --}}
            @if (filled($profil->email))
                <a
                    href="mailto:{{ $profil->email }}"
                    class="inline-block mt-6 px-6 py-2.5 rounded-kecil border-tegas border-ink bg-accent font-semibold shadow-offset tekan"
                >
                    {{ __('beranda.kirim_email') }}
                </a>
            @endif
        </section>
    @endif
@endsection
