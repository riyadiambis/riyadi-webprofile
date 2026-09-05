@php
    $navigasi = [
        ['rute' => 'beranda', 'label' => 'Beranda'],
        ['rute' => 'project', 'label' => 'Project'],
        ['rute' => 'journal', 'label' => 'Journal'],
        ['rute' => 'galeri', 'label' => 'Galeri'],
    ];
    $bahasaAktif = app()->getLocale();
    $baruDitekan = session('bahasa_baru_ditekan', false);
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

                Tampilan bingkai-geser: penanda diam di posisi yang
                benar lewat class statis (translate-x-full/tanpa),
                dan HANYA memutar animasi @keyframes kalau halaman ini
                hasil langsung dari tombol yang baru ditekan (session
                flash sekali pakai dari BahasaController) — navigasi
                biasa tidak memutar ulang animasinya.
            --}}
            <form method="POST" action="{{ route('bahasa') }}" aria-label="Pilihan bahasa">
                @csrf
                <div class="relative inline-flex rounded-kecil border-tegas border-ink bg-card p-1">
                    <span
                        aria-hidden="true"
                        @class([
                            'absolute inset-1 w-9 rounded-kecil bg-accent-alt',
                            'translate-x-full' => $bahasaAktif === 'en' && ! $baruDitekan,
                            'animate-geser-ke-en' => $bahasaAktif === 'en' && $baruDitekan,
                            'animate-geser-ke-id' => $bahasaAktif === 'id' && $baruDitekan,
                        ])
                    ></span>

                    <button
                        type="submit" name="pilihan" value="id"
                        aria-pressed="{{ $bahasaAktif === 'id' ? 'true' : 'false' }}"
                        @class([
                            'relative z-10 w-9 rounded-kecil py-1 text-center text-sm font-semibold transition-colors',
                            'text-card' => $bahasaAktif === 'id',
                            'text-ink-soft hover:text-accent-alt' => $bahasaAktif !== 'id',
                        ])
                    >ID</button>
                    <button
                        type="submit" name="pilihan" value="en"
                        aria-pressed="{{ $bahasaAktif === 'en' ? 'true' : 'false' }}"
                        @class([
                            'relative z-10 w-9 rounded-kecil py-1 text-center text-sm font-semibold transition-colors',
                            'text-card' => $bahasaAktif === 'en',
                            'text-ink-soft hover:text-accent-alt' => $bahasaAktif !== 'en',
                        ])
                    >EN</button>
                </div>
            </form>
        </div>
    </div>
</header>
