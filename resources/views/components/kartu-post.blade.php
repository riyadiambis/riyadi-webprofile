@props(['post'])

<a href="{{ route('journal.tulisan', $post->slug) }}" class="group flex h-full flex-col bg-card border-tegas border-ink rounded-kartu shadow-offset tekan overflow-hidden">
    <div class="aspect-video w-full overflow-hidden border-b-tegas border-ink bg-kertas">
        @if ($post->sampulUrl())
            <img
                src="{{ $post->sampulUrl() }}"
                alt=""
                class="h-full w-full object-cover"
                loading="lazy"
            >
        @else
            <div class="flex h-full w-full items-center justify-center">
                <span class="label-bagian">Tanpa sampul</span>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <p class="label-bagian">
            {{ $post->tanggalTerbit() }}
        </p>
        <h2 class="font-judul text-xl font-semibold mt-2 leading-snug group-hover:text-accent-alt transition-colors">
            {{ $post->judul }}
        </h2>
        <p class="mt-2 text-sm text-ink-soft line-clamp-3">
            {{ $post->ringkasan }}
        </p>
    </div>
</a>
