<?php

use App\Models\Photo;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteText;

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
 | Dwibahasa beranda, diminta pemilik di Fase 5. Cookie `bahasa`
 | dikirim lewat withCookie — meski namanya menyesatkan, justru
 | withCookie yang mengenkripsi cookie dulu (lewat
 | prepareCookiesForRequest) sehingga formatnya sama dengan produksi
 | dan berhasil didekripsi EncryptCookies. withUnencryptedCookie
 | mengirim nilai mentah yang di-null-kan saat dekripsi gagal, dan
 | bahasa diam-diam jatuh ke Indonesia. Alasan lengkap dan larangan
 | menukarnya balik ada di docs/keputusan.md.
 */
it('membalas 200 di beranda dalam kedua pilihan bahasa', function (string $pilihan) {
    $this->withCookie('bahasa', $pilihan)
        ->get('/')
        ->assertOk();
})->with(['id', 'en']);

/*
 | Aturan cadangan dwibahasa adalah logika, bukan tampilan — tidak
 | cukup dinilai lewat pemeriksaan visual, sama seperti preseden
 | bisaDiklik() di Fase 3. Saat bahasa EN aktif, teks berbahasa
 | Inggris tampil untuk yang sudah diterjemahkan, dan versi
 | Indonesia tetap tampil untuk yang belum. Hanya project dipin yang
 | muncul di beranda, jadi keduanya dibuat dipin.
 */
it('mengikuti bahasa EN dengan cadangan Indonesia untuk ringkasan yang kosong', function () {
    SiteText::create([
        'kunci' => 'perkenalan',
        'nilai_id' => 'Perkenalan bahasa Indonesia.',
        'nilai_en' => 'Introduction in English.',
    ]);

    Project::create([
        'nama' => 'Project Sudah Diterjemahkan',
        'ringkasan' => 'Ringkasan Indonesia.',
        'ringkasan_en' => 'English summary.',
        'gambar' => ['project/uuid-1/thumb.webp'],
        'tahun' => '2026',
        'dipin' => true,
        'urutan' => 1,
    ]);

    Project::create([
        'nama' => 'Project Belum Diterjemahkan',
        'ringkasan' => 'Ringkasan Indonesia kedua.',
        'ringkasan_en' => null,
        'gambar' => ['project/uuid-2/thumb.webp'],
        'tahun' => '2026',
        'dipin' => true,
        'urutan' => 2,
    ]);

    $this->withCookie('bahasa', 'en')
        ->get('/')
        ->assertOk()
        // Teks yang punya versi Inggris tampil dalam bahasa Inggris.
        ->assertSee('Introduction in English.')
        ->assertSee('English summary.')
        ->assertDontSee('Ringkasan Indonesia.')
        ->assertDontSee('Perkenalan bahasa Indonesia.')
        // Yang belum diterjemahkan jatuh ke bahasa Indonesia,
        // bukan tampil kosong. Nama project tidak diterjemahkan.
        ->assertSee('Ringkasan Indonesia kedua.')
        ->assertSee('Project Sudah Diterjemahkan')
        ->assertSee('Project Belum Diterjemahkan');
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
