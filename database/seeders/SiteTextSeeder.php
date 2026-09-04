<?php

namespace Database\Seeders;

use App\Models\SiteText;
use Illuminate\Database\Seeder;

/**
 * Teks beranda. Hanya dua kunci di v1, lihat docs/PRD.md bagian 6.
 *
 * Isinya teks contoh supaya beranda bisa diperiksa tanpa mengisi manual.
 * Versi Inggris sengaja dikosongkan pada `penutup` untuk membuktikan
 * aturan cadangan bahasa di Fase 5.
 */
class SiteTextSeeder extends Seeder
{
    public function run(): void
    {
        $teks = [
            [
                'kunci' => 'perkenalan',
                'nilai_id' => 'Halo, saya Rahmat Riyadi. Saya menulis catatan teknis, mengerjakan project, dan sesekali memotret. Halaman ini tempat semuanya berkumpul.',
                'nilai_en' => 'Hi, I am Rahmat Riyadi. I write technical notes, build projects, and take photographs now and then. This page is where all of it lives.',
            ],
            [
                'kunci' => 'penutup',
                'nilai_id' => 'Punya pertanyaan atau ingin bekerja sama? Kirim email, saya biasanya membalas dalam sehari.',
                'nilai_en' => null,
            ],
        ];

        foreach ($teks as $baris) {
            SiteText::updateOrCreate(
                ['kunci' => $baris['kunci']],
                ['nilai_id' => $baris['nilai_id'], 'nilai_en' => $baris['nilai_en']],
            );
        }
    }
}
