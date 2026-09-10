@extends('layouts.publik', ['bahasaHalaman' => 'id'])


@section('judul', 'Journal')

@section('konten')
    <p class="label-bagian">Halaman</p>

    <h1 class="font-judul text-4xl md:text-5xl font-semibold mt-3 leading-tight">
        Journal
    </h1>

    <p class="mt-4 max-w-baca text-ink-soft">
        Catatan teknis, project, dan cerita seputar pengerjaannya.
    </p>

    @if ($posts->isEmpty())
        <p class="mt-10 text-ink-soft">Belum ada tulisan yang terbit.</p>
    @else
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <x-kartu-post :post="$post" />
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links('pagination.kertas') }}
        </div>
    @endif
@endsection
