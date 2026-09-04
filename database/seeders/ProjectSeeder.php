<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Project;
use App\Services\PipelineGambar;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

/**
 * Dua project contoh, membuktikan dua perilaku kartu sekaligus:
 *
 * - "Portal Alumni": tiga gambar (rotasi otomatis), tertaut ke post
 *   contoh yang sudah terbit (kartu bisa diklik).
 * - "Aplikasi Kasir Warung": satu gambar (kartu diam, tanpa rotasi),
 *   tanpa post tertaut (kartu tidak bisa diklik, "segera ditulis").
 *
 * Gambarnya diproses lewat PipelineGambar yang sebenarnya, sama seperti
 * PostSeeder, bukan path tiruan.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $pipeline = app(PipelineGambar::class);
        $berkasContoh = database_path('seeders/berkas/contoh-sampul.jpg');

        $gambar = fn () => $pipeline->proses(
            new UploadedFile($berkasContoh, 'contoh-sampul.jpg', 'image/jpeg', null, true),
            'project',
        )['thumb'];

        $postTerbit = Post::where('slug', 'contoh-tulisan-terbit')->first();

        Project::updateOrCreate(
            ['nama' => 'Portal Alumni'],
            [
                'ringkasan' => 'Portal pendataan alumni kampus, dibangun bersama tim kuliah.',
                'ringkasan_en' => 'Alumni data portal for the campus, built with a university team.',
                'gambar' => [$gambar(), $gambar(), $gambar()],
                'tahun' => '2025',
                'post_id' => $postTerbit?->id,
                'dipin' => true,
                'urutan' => 1,
            ],
        );

        Project::updateOrCreate(
            ['nama' => 'Aplikasi Kasir Warung'],
            [
                'ringkasan' => 'Aplikasi kasir sederhana untuk warung, belum sempat ditulis journal-nya.',
                'ringkasan_en' => null,
                'gambar' => [$gambar()],
                'tahun' => '2026',
                'post_id' => null,
                'dipin' => false,
                'urutan' => 2,
            ],
        );
    }
}
