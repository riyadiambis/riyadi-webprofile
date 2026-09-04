@props(['project'])

@php $turunan = $project->gambarUrls(); @endphp

<div
    class="relative aspect-video w-full overflow-hidden border-b-tegas border-ink bg-kertas"
    @if (count($turunan) > 1)
        data-slider-gambar
        data-jeda="3000"
    @endif
>
    @forelse ($turunan as $i => $ukuran)
        <img
            src="{{ $ukuran['thumb'] }}"
            alt=""
            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
            class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700 ease-in-out {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
        >
    @empty
        <div class="flex h-full w-full items-center justify-center">
            <span class="label-bagian">Tanpa gambar</span>
        </div>
    @endforelse
</div>

<div class="flex flex-1 flex-col p-5">
    <p class="label-bagian">{{ $project->tahun }}</p>
    <h2 class="font-judul text-xl font-semibold mt-2 leading-snug group-hover:text-accent-alt transition-colors">
        {{ $project->nama }}
    </h2>
    <p class="mt-2 text-sm text-ink-soft line-clamp-3">
        {{ $project->ringkasanTampil() }}
    </p>
</div>
