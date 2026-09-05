<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
