<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Services\PipelineGambar;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

/**
 * Dua post contoh: satu terbit, satu draf. Sampulnya diproses lewat
 * PipelineGambar yang sebenarnya, bukan path tiruan, supaya
 * `migrate:fresh --seed` menghasilkan turunan WebP yang bisa diperiksa
 * di storage.
 */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        $pipeline = app(PipelineGambar::class);
        $berkasContoh = database_path('seeders/berkas/contoh-sampul.jpg');

        $sampul = fn () => $pipeline->proses(
            new UploadedFile($berkasContoh, 'contoh-sampul.jpg', 'image/jpeg', null, true)
        );

        $turunanTerbit = $sampul();
        $turunanIsi = $sampul();

        $kontenTerbit = <<<HTML
            <p>Ini contoh tulisan yang sudah terbit, dipakai untuk memeriksa tampilan journal tanpa perlu mengisi manual.</p>
            <h2>Contoh heading</h2>
            <p>Paragraf dengan <strong>teks tebal</strong>, <em>teks miring</em>, dan <a href="https://laravel.com">tautan</a>.</p>
            <blockquote><p>Contoh kutipan singkat.</p></blockquote>
            <pre><code>echo "contoh blok kode";</code></pre>
            <ul><li>Poin pertama</li><li>Poin kedua</li></ul>
            <img src="/storage/{$turunanIsi['sedang']}" alt="Contoh gambar di dalam tulisan">
            <div data-type="customBlock" data-id="youtube" data-config="{&quot;url&quot;:&quot;https:\/\/youtu.be\/dQw4w9WgXcQ&quot;}"></div>
            HTML;

        Post::updateOrCreate(
            ['slug' => 'contoh-tulisan-terbit'],
            [
                'judul' => 'Contoh Tulisan Terbit',
                'ringkasan' => 'Contoh ringkasan untuk tulisan yang sudah terbit, dipakai memeriksa tampilan tanpa mengisi manual.',
                'konten' => $kontenTerbit,
                'cover' => $turunanTerbit['thumb'],
                'tautan_project' => 'https://github.com/riyadiambis',
                'label_tautan' => 'Lihat repo',
                'status' => 'terbit',
                'dipin' => true,
                'terbit_pada' => now()->subDays(3),
            ],
        );

        Post::updateOrCreate(
            ['slug' => 'contoh-tulisan-draf'],
            [
                'judul' => 'Contoh Tulisan Draf',
                'ringkasan' => 'Contoh ringkasan untuk tulisan yang masih draf, tidak boleh muncul di halaman publik.',
                'konten' => '<p>Tulisan ini masih draf dan belum lengkap.</p>',
                'cover' => null,
                'tautan_project' => null,
                'label_tautan' => null,
                'status' => 'draf',
                'dipin' => false,
                'terbit_pada' => null,
            ],
        );
    }
}
