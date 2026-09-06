@extends('layouts.publik')

{{-- Galeri tidak ikut dwibahasa, lihat docs/fitur/05-dwibahasa.md. --}}
@section('lang', 'id')

@section('judul', 'Galeri')

@section('konten')
    <p class="label-bagian">Halaman</p>

    <h1 class="font-judul text-4xl md:text-5xl font-semibold mt-3 leading-tight">
        Galeri
    </h1>

    <p class="mt-6 max-w-baca text-ink-soft">
        Kumpulan foto pribadi. Klik salah satu untuk melihatnya besar, lengkap dengan caption.
    </p>

    @if ($photos->isEmpty())
        <p class="mt-10 text-ink-soft">Belum ada foto yang ditambahkan.</p>
    @else
        <div class="mt-10 overflow-hidden rounded-kartu border-tegas border-ink shadow-offset bg-card">
            <div class="grid grid-cols-3 gap-0.5 md:grid-cols-4 lg:grid-cols-5">
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
                        class="relative aspect-square cursor-zoom-in overflow-hidden"
                        aria-label="Perbesar foto"
                    >
                        <img
                            src="{{ $urls['thumb'] }}"
                            alt="{{ $photo->caption ?: 'Foto galeri' }}"
                            loading="{{ $index < 8 ? 'eager' : 'lazy' }}"
                            decoding="async"
                            class="h-full w-full object-cover"
                        >
                    </button>
                @endforeach
            </div>
        </div>
    @endif
@endsection
