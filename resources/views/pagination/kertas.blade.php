@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="inline-block rounded-kecil border-tegas border-ink bg-card px-3 py-1.5 text-sm font-semibold text-ink opacity-40 cursor-not-allowed">&larr; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-3 py-1.5 text-sm font-semibold text-ink shadow-offset tekan-hover">
                &larr; Sebelumnya
            </a>
        @endif

        <p class="text-sm text-ink-soft">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </p>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-block rounded-kecil border-tegas border-ink bg-card px-3 py-1.5 text-sm font-semibold text-ink shadow-offset tekan-hover">
                Berikutnya &rarr;
            </a>
        @else
            <span class="inline-block rounded-kecil border-tegas border-ink bg-card px-3 py-1.5 text-sm font-semibold text-ink opacity-40 cursor-not-allowed">Berikutnya &rarr;</span>
        @endif
    </nav>
@endif
