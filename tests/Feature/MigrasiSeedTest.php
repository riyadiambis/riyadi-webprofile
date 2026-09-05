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
