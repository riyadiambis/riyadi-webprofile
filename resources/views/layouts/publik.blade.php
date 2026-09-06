<!DOCTYPE html>
{{--
    Atribut lang ikut bahasa aktif secara default, tapi journal dan
    galeri MENGUNCI 'id' lewat @section('lang', 'id') di masing-masing
    view — isinya memang tidak ikut berubah saat bahasa dialihkan,
    lihat docs/fitur/05-dwibahasa.md. Beranda dan /project tetap
    mengikuti app()->getLocale() karena isinya sungguhan berubah.
--}}
<html lang="{{ str_replace('_', '-', $__env->yieldContent('lang', app()->getLocale())) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-kertas font-isi text-ink min-h-screen flex flex-col antialiased">
    @include('partials.header')

    <main class="flex-1 w-full max-w-konten mx-auto px-6 py-bagian-hp md:py-bagian">
        @yield('konten')
    </main>

    @include('partials.footer')

    @include('partials.lightbox')
</body>
</html>
