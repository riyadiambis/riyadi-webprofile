<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Services\PipelineGambar;
use App\Services\TurunanGambar;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

/**
 * Dua puluh foto contoh, sesuai kriteria lolos Fase 4 ("dua puluh
 * foto percobaan"). Gambarnya diproses lewat PipelineGambar yang
 * sebenarnya, bukan path tiruan, supaya `migrate:fresh --seed`
 * menghasilkan turunan WebP yang bisa diperiksa di storage.
 *
 * Kolom gambar menyimpan basis nama (direktori UUID tanpa turunan),
 * lihat docs/keputusan.md (keputusan Fase 4). Caption dan tanggal
 * pengambilan sengaja kosong di sebagian foto supaya keduanya bisa
 * diperiksa sekaligus.
 */
class PhotoSeeder extends Seeder
{
    public function run(): void
    {
        $pipeline = app(PipelineGambar::class);
        $berkasContoh = database_path('seeders/berkas/contoh-sampul.jpg');

        for ($nomor = 1; $nomor <= 20; $nomor++) {
            $turunan = $pipeline->proses(
                new UploadedFile($berkasContoh, "contoh-foto-{$nomor}.jpg", 'image/jpeg', null, true),
                'galeri',
            );

            Photo::updateOrCreate(
                ['urutan' => $nomor],
                [
                    'gambar' => TurunanGambar::basisDari($turunan['thumb']),
                    'caption' => $nomor % 4 === 0 ? null : "Foto contoh {$nomor}",
                    'diambil_pada' => $nomor % 3 === 0 ? now()->subDays($nomor)->format('Y-m-d') : null,
                    'urutan' => $nomor,
                ],
            );
        }
    }
}
