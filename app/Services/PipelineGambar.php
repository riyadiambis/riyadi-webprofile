<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Mengubah satu gambar unggahan menjadi tiga turunan WebP nyata: thumb,
 * sedang, penuh, sesuai docs/PRD.md bagian 8. Dipakai baik untuk sampul
 * post maupun gambar di tengah tulisan.
 *
 * Ketiga turunan disimpan sebagai berkas sungguhan bertetangga di satu
 * direktori (bukan satu berkas yang diberi ukuran lewat CSS), supaya
 * masing-masing bisa langsung dipakai sebagai nilai path oleh komponen
 * Filament yang mengharapkan berkas nyata di disk. Pemanggil memilih
 * satu turunan sebagai nilai kanonis yang disimpan ke kolom; dua
 * saudaranya diturunkan dengan mengganti nama berkas, karena ketiganya
 * hidup di direktori yang sama.
 *
 * Memakai GD bawaan PHP, bukan pustaka pihak ketiga — GD di lingkungan
 * ini sudah mendukung WebP. Turunan yang lebih kecil dari targetnya
 * tidak diperbesar. Ekstensi exif tidak aktif di lingkungan
 * pengembangan, jadi orientasi foto dari kamera HP tidak dikoreksi
 * otomatis di fase ini.
 */
class PipelineGambar
{
    /**
     * @return array<string, string> path relatif tiap turunan di disk `public`, berkunci thumb/sedang/penuh
     */
    public function proses(UploadedFile $berkas): array
    {
        $batasKb = config('media.batas_unggah_kb');

        if ($berkas->getSize() > $batasKb * 1024) {
            throw new RuntimeException("Berkas melebihi batas {$batasKb} KB.");
        }

        $isi = file_get_contents($berkas->getRealPath());
        $sumber = @imagecreatefromstring($isi);

        if ($sumber === false) {
            throw new RuntimeException('Berkas bukan gambar yang bisa dibaca.');
        }

        $sumber = $this->pastikanTrueColor($sumber);
        $direktori = config('media.direktori').'/'.(string) Str::uuid();

        Storage::disk(config('media.disk'))->makeDirectory($direktori);

        $path = [];

        foreach (config('media.turunan') as $nama => $lebarTarget) {
            $turunan = $this->skalakan($sumber, $lebarTarget);
            $relatif = "{$direktori}/{$nama}.webp";
            $absolut = Storage::disk(config('media.disk'))->path($relatif);

            imagewebp($turunan, $absolut, config('media.kualitas_webp'));
            imagedestroy($turunan);

            $path[$nama] = $relatif;
        }

        imagedestroy($sumber);

        return $path;
    }

    private function pastikanTrueColor(GdImage $gambar): GdImage
    {
        if (! imageistruecolor($gambar)) {
            $lebar = imagesx($gambar);
            $tinggi = imagesy($gambar);
            $truecolor = imagecreatetruecolor($lebar, $tinggi);
            imagealphablending($truecolor, false);
            imagesavealpha($truecolor, true);
            imagecopy($truecolor, $gambar, 0, 0, 0, 0, $lebar, $tinggi);
            imagedestroy($gambar);

            return $truecolor;
        }

        imagealphablending($gambar, false);
        imagesavealpha($gambar, true);

        return $gambar;
    }

    private function skalakan(GdImage $sumber, int $lebarTarget): GdImage
    {
        $lebarAsli = imagesx($sumber);
        $tinggiAsli = imagesy($sumber);

        if ($lebarAsli <= $lebarTarget) {
            $salinan = imagecreatetruecolor($lebarAsli, $tinggiAsli);
            imagealphablending($salinan, false);
            imagesavealpha($salinan, true);
            imagecopy($salinan, $sumber, 0, 0, 0, 0, $lebarAsli, $tinggiAsli);

            return $salinan;
        }

        $hasil = imagescale($sumber, $lebarTarget, -1, IMG_BICUBIC);
        imagealphablending($hasil, false);
        imagesavealpha($hasil, true);

        return $hasil;
    }
}
