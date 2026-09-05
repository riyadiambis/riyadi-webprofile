<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Mengubah satu nilai tersimpan jadi URL ketiga turunan sekaligus.
 * Ini satu-satunya tempat penurunan path antar-turunan boleh terjadi —
 * jangan menebak path turunan lewat manipulasi string di tempat lain
 * (lihat docs/keputusan.md, keputusan Fase 4).
 *
 * PipelineGambar selalu menulis ketiga turunan bertetangga di satu
 * direktori UUID yang sama. Yang tersimpan di basis data ada dua
 * bentuk, dan keduanya ditangani di sini:
 *
 * - posts.cover dan elemen projects.gambar (Fase 2A/3) menyimpan
 *   SATU path turunan (dipilih thumb, supaya komponen FileUpload
 *   Filament bisa langsung memakainya untuk pratinjau native — lihat
 *   PipelineGambar). Dua saudaranya diturunkan lewat urlDari().
 * - photos.gambar (Fase 4) menyimpan BASIS NAMA: direktori UUID-nya
 *   tanpa nama turunan, dan urlDariBasis() yang menyusun ketiganya.
 *   Keputusan pemilik, supaya tidak ada kode yang tergoda menebak
 *   turunan penuh dari path thumb yang tersimpan.
 */
class TurunanGambar
{
    /**
     * Mengambil basis nama (direktori bersama ketiga turunan) dari
     * satu path turunan. Kebalikan dari urlDariBasis().
     */
    public static function basisDari(string $pathTurunan): string
    {
        return dirname($pathTurunan);
    }

    /**
     * Menyusun URL ketiga turunan dari basis nama (direktori tanpa
     * nama turunan). Dipakai Photo di Fase 4.
     *
     * @return array<string, string> URL ketiga turunan, berkunci thumb/sedang/penuh
     */
    public static function urlDariBasis(string $basis): array
    {
        $disk = Storage::disk(config('media.disk'));

        $hasil = [];

        foreach (array_keys(config('media.turunan')) as $nama) {
            $hasil[$nama] = $disk->url("{$basis}/{$nama}.webp");
        }

        return $hasil;
    }

    /**
     * Menyusun URL ketiga turunan dari satu path turunan yang
     * tersimpan (bebas thumb/sedang/penuh). Dipakai Post dan Project
     * sejak Fase 2A/3.
     *
     * @return array<string, string> URL ketiga turunan, berkunci thumb/sedang/penuh
     */
    public static function urlDari(string $pathTurunan): array
    {
        return static::urlDariBasis(static::basisDari($pathTurunan));
    }
}
