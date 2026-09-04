<?php

use App\Models\Post;
use App\Models\Project;

/*
 | Smoke test rute publik yang diperkenalkan Fase 1.
 | Hanya memeriksa halaman membalas 200, bukan isinya.
 */

it('membalas 200 pada rute publik', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    'beranda' => '/',
    'project' => '/project',
    'journal' => '/journal',
    'galeri' => '/galeri',
]);

it('mengarahkan panel admin ke halaman login saat belum masuk', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('membalas 200 pada halaman login admin', function () {
    $this->get('/admin/login')->assertOk();
});

/*
 | bisaDiklik() adalah logika, bukan tampilan — tidak cukup dinilai
 | lewat pemeriksaan visual di browser. Ditambahkan ke smoke test yang
 | sudah ada, bukan berkas baru, atas permintaan pemilik di Fase 3.
 */
it('kartu project menuju tulisan yang terbit dan menandai yang belum tertaut', function () {
    $post = Post::create([
        'judul' => 'Tulisan Tertaut', 'slug' => 'tulisan-tertaut',
        'ringkasan' => 'ringkasan', 'konten' => '<p>isi</p>',
        'status' => 'terbit', 'terbit_pada' => now(),
    ]);

    Project::create([
        'nama' => 'Project Tertaut', 'ringkasan' => 'ringkasan',
        'gambar' => ['project/uuid-1/thumb.webp'], 'tahun' => '2026',
        'post_id' => $post->id, 'urutan' => 1,
    ]);

    Project::create([
        'nama' => 'Project Belum Tertaut', 'ringkasan' => 'ringkasan',
        'gambar' => ['project/uuid-2/thumb.webp'], 'tahun' => '2026',
        'post_id' => null, 'urutan' => 2,
    ]);

    $this->get('/project')
        ->assertOk()
        ->assertSee(route('journal.tulisan', $post->slug), escape: false)
        ->assertSee('Segera ditulis');
});
