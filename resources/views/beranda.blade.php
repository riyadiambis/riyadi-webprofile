@extends('layouts.publik')

@section('judul', 'Beranda')

@section('konten')
    <p class="label-bagian">Fase 1 — Fondasi</p>

    <h1 class="font-judul text-4xl md:text-5xl font-semibold mt-3 leading-tight">
        Halo, saya <span class="stabilo">Rahmat Riyadi</span>
    </h1>

    <p class="mt-6 max-w-baca text-ink-soft">
        Kerangka situs sudah berdiri. Isi beranda dikerjakan di Fase 5, setelah
        journal, project, dan galeri punya datanya masing-masing.
    </p>

    <div class="mt-10 max-w-baca bg-card border-tegas border-ink rounded-kartu shadow-offset p-6 tekan">
        <p class="label-bagian">Penanda</p>
        <p class="mt-2">
            Kartu ini memakai token desain: latar kartu, garis 1,5px, sudut
            membulat, dan bayangan offset solid tanpa blur. Tekan untuk melihat
            elemennya bergeser ke arah bayangan.
        </p>
    </div>
@endsection
