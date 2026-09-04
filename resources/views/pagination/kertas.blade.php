@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="label-bagian opacity-40">&larr; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="label-bagian hover:text-accent-alt transition-colors">
                &larr; Sebelumnya
            </a>
        @endif

        <p class="text-sm text-ink-soft">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </p>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="label-bagian hover:text-accent-alt transition-colors">
                Berikutnya &rarr;
            </a>
        @else
            <span class="label-bagian opacity-40">Berikutnya &rarr;</span>
        @endif
    </nav>
@endif
