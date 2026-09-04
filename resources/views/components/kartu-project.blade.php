@props(['project'])

@if ($project->bisaDiklik())
    <a
        href="{{ route('journal.tulisan', $project->post->slug) }}"
        class="group relative flex h-full flex-col bg-card border-tegas border-ink rounded-kartu shadow-offset tekan overflow-hidden"
    >
        <x-partials.isi-kartu-project :project="$project" />
    </a>
@else
    <div
        class="relative flex h-full flex-col bg-card border-tegas border-ink rounded-kartu shadow-offset overflow-hidden opacity-90 cursor-not-allowed"
        aria-disabled="true"
    >
        <x-partials.isi-kartu-project :project="$project" />

        <span class="label-bagian absolute right-3 top-3 z-10 bg-card border-tegas border-ink rounded-kecil px-2 py-1">
            Segera ditulis
        </span>
    </div>
@endif
