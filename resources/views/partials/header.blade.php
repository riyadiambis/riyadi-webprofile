@php
    $navigasi = [
        ['rute' => 'beranda', 'label' => 'Beranda'],
        ['rute' => 'project', 'label' => 'Project'],
        ['rute' => 'journal', 'label' => 'Journal'],
        ['rute' => 'galeri', 'label' => 'Galeri'],
    ];
    $bahasaAktif = app()->getLocale();
@endphp

<header class="border-b-tegas border-line bg-paper/85 sticky top-0 z-10 backdrop-blur">
    <div class="max-w-konten mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-x-8 gap-y-3">
        <a href="{{ route('beranda') }}" class="font-judul text-lg font-semibold tracking-tight">
            Rahmat Riyadi
        </a>

        <div class="flex items-center gap-5">
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

            {{--
                Pengalih bahasa ID/EN, form POST tanpa JavaScript.
                Cookie `bahasa` ditulis oleh rute `bahasa`, dibaca satu
                kali oleh middleware SetLocale pada permintaan
                berikutnya — Blade tidak pernah membaca cookie
                langsung, cukup app()->getLocale(), lihat
                docs/keputusan.md.
            --}}
            <form method="POST" action="{{ route('bahasa') }}" class="flex items-center gap-1.5 text-sm" aria-label="Pilihan bahasa">
                @csrf
                <button
                    type="submit" name="pilihan" value="id"
                    aria-pressed="{{ $bahasaAktif === 'id' ? 'true' : 'false' }}"
                    @class([
                        'rounded-kecil px-2 py-0.5 transition-colors',
                        'text-accent-alt font-bold underline underline-offset-4' => $bahasaAktif === 'id',
                        'text-ink-soft hover:text-accent-alt' => $bahasaAktif !== 'id',
                    ])
                >ID</button>
                <button
                    type="submit" name="pilihan" value="en"
                    aria-pressed="{{ $bahasaAktif === 'en' ? 'true' : 'false' }}"
                    @class([
                        'rounded-kecil px-2 py-0.5 transition-colors',
                        'text-accent-alt font-bold underline underline-offset-4' => $bahasaAktif === 'en',
                        'text-ink-soft hover:text-accent-alt' => $bahasaAktif !== 'en',
                    ])
                >EN</button>
            </form>
        </div>
    </div>
</header>
