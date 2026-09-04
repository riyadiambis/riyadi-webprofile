<?php

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

/*
 | Smoke test rute publik journal yang diperkenalkan Fase 2B.
 | Aturan tetap CLAUDE.md: hanya memeriksa kode balasan, bukan isinya.
 | Ditambah satu pengecualian eksplisit yang diminta pemilik: draf
 | harus 404 lewat URL langsung, bukan sekadar tidak muncul di daftar.
 */

uses(RefreshDatabase::class);

it('membalas 200 pada daftar journal', function () {
    $this->get('/journal')->assertOk();
});

it('membalas 200 pada tulisan yang sudah terbit', function () {
    $post = Post::create([
        'judul' => 'Tulisan Terbit',
        'slug' => 'tulisan-terbit',
        'ringkasan' => 'ringkasan',
        'konten' => '<p>isi</p>',
        'status' => 'terbit',
        'terbit_pada' => now(),
    ]);

    $this->get("/journal/{$post->slug}")->assertOk();
});

it('membalas 404 saat slug draf diakses langsung lewat URL', function () {
    $post = Post::create([
        'judul' => 'Tulisan Draf',
        'slug' => 'tulisan-draf',
        'ringkasan' => 'ringkasan',
        'konten' => '<p>isi</p>',
        'status' => 'draf',
    ]);

    $this->get("/journal/{$post->slug}")->assertNotFound();
});
