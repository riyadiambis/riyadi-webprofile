@extends('layouts.publik')

@section('judul', $post->judul)

@section('konten')
    <article class="max-w-baca mx-auto">
        @if ($post->tautan_project)
            <a
                href="{{ $post->tautan_project }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 bg-accent text-ink border-tegas border-ink rounded-kartu shadow-offset tekan px-4 py-2 text-sm font-semibold mb-8"
            >
                {{ $post->label_tautan ?: 'Lihat tautan' }}
            </a>
        @endif

        <p class="label-bagian">
            {{ $post->terbit_pada?->locale('id')->translatedFormat('d F Y') }}
        </p>

        <h1 class="font-judul text-3xl md:text-4xl font-semibold mt-3 leading-tight">
            {{ $post->judul }}
        </h1>

        <div class="konten mt-8">
            {!! $konten !!}
        </div>
    </article>
@endsection
