@extends('layouts.publik')

@section('judul', 'Project')

@section('konten')
    <p class="label-bagian">Halaman</p>

    <h1 class="font-judul text-4xl md:text-5xl font-semibold mt-3 leading-tight">
        Project
    </h1>

    <p class="mt-4 max-w-baca text-ink-soft">
        Beberapa hal yang pernah dikerjakan, dari kuliah, riset, sampai project pribadi.
    </p>

    @if ($projects->isEmpty())
        <p class="mt-10 text-ink-soft">Belum ada project yang ditambahkan.</p>
    @else
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($projects as $project)
                <x-kartu-project :project="$project" />
            @endforeach
        </div>
    @endif
@endsection
