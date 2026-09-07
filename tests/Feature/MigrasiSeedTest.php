<?php

use App\Models\Photo;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteText;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

/*
 | Smoke test migrasi dan seeder. Membuktikan basis data bisa dibangun
 | dari nol tanpa galat. Tidak memakai RefreshDatabase karena perintah
 | ini membangun ulang skemanya sendiri.
 */

it('menjalankan migrate:fresh --seed sampai bersih dari nol', function () {
    // PostSeeder menulis turunan gambar sungguhan lewat PipelineGambar.
    // Disk public dipalsukan supaya test tidak meninggalkan berkas di
    // storage/app/public asli.
    Storage::fake('public');

    $kode = Artisan::call('migrate:fresh', ['--seed' => true]);

    expect($kode)->toBe(0);
    expect(User::count())->toBe(1);
    expect(SiteText::count())->toBe(2);
    expect(Post::count())->toBe(2);
    expect(Project::count())->toBe(2);
    expect(Photo::count())->toBe(20);
});

/*
 | Setiap baris yang menyimpan gambar wajib punya berkasnya di disk.
 | Ini bukan menguji logika bisnis, melainkan menjaga satu invariant
 | yang pernah bobol tanpa bunyi: basis data sempat penuh path yang
 | berkasnya tidak pernah ditulis, dan halaman tetap membalas 200
 | sehingga smoke test lama tidak melihat apa pun. Lihat
 | docs/keputusan.md.
 */
it('meninggalkan berkas gambar yang benar-benar ada untuk setiap baris hasil seed', function () {
    Storage::fake('public');

    Artisan::call('migrate:fresh', ['--seed' => true]);

    $disk = Storage::disk('public');

    foreach (Photo::all() as $photo) {
        // photos.gambar menyimpan basis nama, jadi ketiga turunannya
        // yang diperiksa, bukan satu path berkas.
        foreach (['thumb', 'sedang', 'penuh'] as $turunan) {
            expect($disk->exists("{$photo->gambar}/{$turunan}.webp"))->toBeTrue();
        }
    }

    foreach (Post::whereNotNull('cover')->get() as $post) {
        expect($disk->exists($post->cover))->toBeTrue();
    }

    foreach (Project::all() as $project) {
        foreach ($project->gambar as $gambar) {
            expect($disk->exists($gambar))->toBeTrue();
        }
    }
});
