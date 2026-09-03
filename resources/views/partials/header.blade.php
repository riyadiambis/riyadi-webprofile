@php
    $navigasi = [
        ['rute' => 'beranda', 'label' => 'Beranda'],
        ['rute' => 'project', 'label' => 'Project'],
        ['rute' => 'journal', 'label' => 'Journal'],
        ['rute' => 'galeri', 'label' => 'Galeri'],
    ];
@endphp

<header class="border-b-tegas border-line bg-paper/85 sticky top-0 z-10 backdrop-blur">
    <div class="max-w-konten mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-x-8 gap-y-3">
        <a href="{{ route('beranda') }}" class="font-judul text-lg font-semibold tracking-tight">
            Rahmat Riyadi
        </a>

        <nav class="flex items-center gap-5 text-sm">
            @foreach ($navigasi as $item)
                @php $aktif = request()->routeIs($item['rute']); @endphp
                <a
                    href="{{ route($item['rute']) }}"
                    @class([
                        'transition-colors hover:text-accent-alt',
                        'text-accent-alt font-semibold' => $aktif,
                        'text-ink-soft' => ! $aktif,
                    ])
                    @if ($aktif) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
