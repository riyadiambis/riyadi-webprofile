<?php

use App\Models\Photo;
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

/*
 | Grid galeri, diminta pemilik di Fase 4. Bukan sekadar mencari
 | string di seluruh halaman: path ukuran penuh memang wajar muncul
 | di data-src (untuk lightbox), jadi yang diperiksa adalah kedua
 | atribut secara terpisah — src pada <img> grid wajib turunan
 | thumb, dan turunan penuh hanya boleh ada di data-src, tidak
 | pernah di src.
 */
it('grid galeri memuat thumb di src dan menaruh penuh hanya di data-src', function () {
    foreach (range(1, 10) as $nomor) {
        Photo::create([
            'gambar' => "galeri/uuid-{$nomor}",
            'caption' => "Foto {$nomor}",
            'urutan' => $nomor,
        ]);
    }

    $html = $this->get('/galeri')->getContent();

    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $ubin = $xpath->query('//button[@data-galeri-grup and @data-indeks]');
    expect($ubin->length)->toBe(10);

    $gambarGrid = [];

    foreach ($ubin as $tombol) {
        $img = $tombol->getElementsByTagName('img')->item(0);
        expect($img)->not->toBeNull();

        $src = $img->getAttribute('src');
        $dataSrc = $tombol->getAttribute('data-src');

        expect($src)->toEndWith('/thumb.webp')
            ->and($dataSrc)->toEndWith('/penuh.webp')
            ->and($src)->not->toContain('/penuh.webp')
            // Kedua atribut menunjuk basis direktori yang sama.
            ->and(dirname($dataSrc))->toBe(dirname($src));

        $gambarGrid[] = $img;
    }

    // Tidak ada satu pun <img> di halaman yang memakai turunan penuh.
    foreach ($xpath->query('//img') as $img) {
        expect($img->getAttribute('src'))->not->toContain('/penuh.webp');
    }

    // Pemuatan bertahap: 8 ubin pertama eager, sisanya lazy native.
    foreach ($gambarGrid as $index => $img) {
        expect($img->getAttribute('loading'))->toBe($index < 8 ? 'eager' : 'lazy');
    }
});
