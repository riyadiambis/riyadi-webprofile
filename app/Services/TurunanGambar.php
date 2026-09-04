<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Mengubah SATU path turunan yang tersimpan (bebas thumb/sedang/penuh)
 * jadi URL ketiga turunannya sekaligus.
 *
 * PipelineGambar selalu menulis ketiga turunan bertetangga di satu
 * direktori UUID yang sama, tapi kolom di basis data (posts.cover,
 * satu elemen projects.gambar, nanti photos.gambar di Fase 4) hanya
 * menyimpan SATU path — dipilih supaya komponen FileUpload Filament
 * bisa langsung memakainya untuk pratinjau native (lihat catatan di
 * PipelineGambar). Dua saudaranya tidak hilang; keduanya diturunkan
 * lewat dirname() pada path yang tersimpan, bukan disimpan dobel.
 *
 * Satu tempat ini dipakai Post dan Project sekarang, dan dimaksudkan
 * dipakai ulang oleh Photo di Fase 4 — jangan tulis versi kedua.
 */
class TurunanGambar
{
    /**
     * @return array<string, string> URL ketiga turunan, berkunci thumb/sedang/penuh
     */
    public static function urlDari(string $pathTurunan): array
    {
        $direktori = dirname($pathTurunan);
        $disk = Storage::disk(config('media.disk'));

        $hasil = [];

        foreach (array_keys(config('media.turunan')) as $nama) {
            $hasil[$nama] = $disk->url("{$direktori}/{$nama}.webp");
        }

        return $hasil;
    }
}
