<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

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
 *
 * JAMINAN: kalau proses() mengembalikan path, ketiga berkasnya SUDAH ADA
 * dan berisi di disk. Setiap kegagalan penulisan melempar
 * RuntimeException, dan direktori yang terlanjur berisi turunan setengah
 * jadi dihapus lebih dulu. Pemanggil karena itu tidak pernah menerima
 * path berkas yang tidak pernah ditulis — lihat docs/keputusan.md.
 * Jangan melunakkan ini jadi peringatan atau nilai balik null: kegagalan
 * senyap di sinilah yang dulu mengisi basis data dengan path hantu.
 */
class PipelineGambar
{
    /**
     * @param  string|null  $direktori  Sub-direktori dasar di dalam disk `public`, mis. 'journal' atau 'project'. Kosong berarti jatuh ke config('media.direktori').
     * @return array<string, string> path relatif tiap turunan di disk `public`, berkunci thumb/sedang/penuh
     */
    public function proses(UploadedFile $berkas, ?string $direktori = null): array
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
        $direktoriDasar = $direktori ?? config('media.direktori');
        $direktori = $direktoriDasar.'/'.(string) Str::uuid();
        $disk = Storage::disk(config('media.disk'));

        if (! $disk->makeDirectory($direktori)) {
            imagedestroy($sumber);

            throw new RuntimeException(
                "Gagal membuat direktori turunan [{$direktori}] di disk '".config('media.disk')."'."
            );
        }

        $path = [];

        try {
            foreach (config('media.turunan') as $nama => $lebarTarget) {
                $relatif = "{$direktori}/{$nama}.webp";
                $absolut = $disk->path($relatif);
                $turunan = $this->skalakan($sumber, $lebarTarget);

                try {
                    // Warning bawaan imagewebp() dibungkam supaya pesan yang
                    // sampai ke pemanggil adalah pesan jelas dari
                    // pastikanTertulis(), bukan ErrorException hasil konversi
                    // warning oleh penangan galat Laravel di konteks web.
                    $berhasil = @imagewebp($turunan, $absolut, config('media.kualitas_webp'));
                } finally {
                    imagedestroy($turunan);
                }

                $this->pastikanTertulis($berhasil, $absolut, $relatif);

                $path[$nama] = $relatif;
            }
        } catch (Throwable $e) {
            // Jangan tinggalkan direktori berisi turunan setengah jadi:
            // yang gagal sebagian sama tidak berlakunya dengan yang gagal
            // seluruhnya, karena pemanggil butuh ketiganya. Menangkap
            // Throwable, bukan RuntimeException saja — kegagalan GD bisa
            // sampai ke sini sebagai ErrorException di konteks web.
            $disk->deleteDirectory($direktori);

            throw $e;
        } finally {
            imagedestroy($sumber);
        }

        return $path;
    }

    /**
     * Memastikan satu turunan benar-benar mendarat di disk sebagai berkas
     * berisi. Dipanggil setelah setiap penulisan, sebelum path-nya boleh
     * ikut dikembalikan ke pemanggil.
     *
     * Nilai balik imagewebp() saja tidak cukup dijadikan bukti: ia
     * mengembalikan false tanpa memicu exception apa pun kalau tujuannya
     * tidak bisa ditulis, dan sebaliknya bisa mengembalikan true sambil
     * meninggalkan berkas nol byte kalau penulisan terputus di tengah.
     * Keduanya diperiksa terpisah.
     *
     * @throws RuntimeException kalau berkas gagal ditulis atau kosong
     */
    private function pastikanTertulis(bool $berhasil, string $absolut, string $relatif): void
    {
        // Hasil stat berkas yang baru ditulis bisa terlayani dari cache
        // PHP kalau path yang sama sempat di-stat sebelumnya.
        clearstatcache(true, $absolut);

        if (! $berhasil) {
            throw new RuntimeException(
                "Gagal menulis turunan [{$relatif}]: imagewebp() mengembalikan false. "
                .'Periksa izin tulis dan ruang kosong disk.'
            );
        }

        if (! is_file($absolut) || filesize($absolut) < 1) {
            throw new RuntimeException(
                "Turunan [{$relatif}] dilaporkan berhasil ditulis tapi berkasnya "
                .'tidak ada atau kosong di disk. Periksa ruang kosong disk.'
            );
        }
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
