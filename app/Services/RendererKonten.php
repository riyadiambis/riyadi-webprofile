<?php

namespace App\Services;

use App\Filament\RichBlocks\BlokYoutube;
use DOMDocument;
use DOMElement;

/**
 * Mengubah HTML `konten` yang sudah disanitasi jadi HTML siap tampil:
 * gambar dibungkus pemicu lightbox, blok YouTube dari Fase 2A dirender
 * jadi iframe. Sanitasi selalu dijalankan lebih dulu — transformasi di
 * sini tidak pernah bekerja di atas HTML mentah yang belum dibersihkan.
 */
class RendererKonten
{
    public function __construct(
        private SanitasiKonten $sanitasi,
    ) {}

    public function render(string $htmlTersimpan): string
    {
        $bersih = $this->sanitasi->bersihkan($htmlTersimpan);

        if (trim($bersih) === '') {
            return '';
        }

        $dom = new DOMDocument();

        $errorSebelumnya = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><html><body>'.$bersih.'</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($errorSebelumnya);

        $body = $dom->getElementsByTagName('body')->item(0);

        $this->transformasiGambar($dom, $body);
        $this->transformasiYoutube($dom, $body);

        $hasil = '';
        foreach (iterator_to_array($body->childNodes) as $anak) {
            $hasil .= $dom->saveHTML($anak);
        }

        return $hasil;
    }

    /**
     * Setiap <img> dibungkus tombol pemicu lightbox. src gambar yang
     * tersimpan selalu turunan `sedang` (lihat PipelineGambar dan
     * PostForm di Fase 2A), turunan `penuh` untuk lightbox diturunkan
     * dengan mengganti nama berkas karena keduanya bertetangga di
     * direktori yang sama.
     */
    private function transformasiGambar(DOMDocument $dom, DOMElement $body): void
    {
        foreach (iterator_to_array($body->getElementsByTagName('img')) as $img) {
            $sedang = $img->getAttribute('src');
            $penuh = str_contains($sedang, '/sedang.webp')
                ? str_replace('/sedang.webp', '/penuh.webp', $sedang)
                : $sedang;
            $alt = $img->getAttribute('alt');

            $img->setAttribute('class', 'block w-full h-auto');
            $img->setAttribute('loading', 'lazy');

            $tombol = $dom->createElement('button');
            $tombol->setAttribute('type', 'button');
            $tombol->setAttribute('class', 'block w-full cursor-zoom-in overflow-hidden rounded-kartu border-tegas border-ink shadow-offset tekan my-6');
            $tombol->setAttribute('data-lightbox-trigger', '');
            $tombol->setAttribute('data-src', $penuh);
            $tombol->setAttribute('data-alt', $alt);
            $tombol->setAttribute('aria-label', 'Perbesar gambar');

            $img->parentNode->replaceChild($tombol, $img);
            $tombol->appendChild($img);
        }
    }

    /**
     * Blok YouTube dari Fase 2A menyimpan data-config berisi URL asli.
     * ID videonya diekstrak lewat method yang sama dipakai panel admin
     * supaya logikanya tidak dobel. Blok yang datanya tidak valid
     * (tautan tidak dikenali) dibuang, bukan ditampilkan rusak.
     */
    private function transformasiYoutube(DOMDocument $dom, DOMElement $body): void
    {
        foreach (iterator_to_array($body->getElementsByTagName('div')) as $div) {
            if ($div->getAttribute('data-type') !== 'customBlock' || $div->getAttribute('data-id') !== 'youtube') {
                continue;
            }

            $config = json_decode($div->getAttribute('data-config') ?: '{}', true) ?: [];
            $id = BlokYoutube::idVideo($config['url'] ?? '');

            if (! $id) {
                $div->parentNode->removeChild($div);

                continue;
            }

            $bungkus = $dom->createElement('div');
            $bungkus->setAttribute('class', 'not-prose aspect-video overflow-hidden rounded-kartu border-tegas border-ink my-6');

            $iframe = $dom->createElement('iframe');
            $iframe->setAttribute('src', "https://www.youtube-nocookie.com/embed/{$id}");
            $iframe->setAttribute('title', 'Video YouTube');
            $iframe->setAttribute('loading', 'lazy');
            $iframe->setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
            $iframe->setAttribute('allowfullscreen', 'allowfullscreen');
            $iframe->setAttribute('frameborder', '0');
            $iframe->setAttribute('class', 'h-full w-full');

            $bungkus->appendChild($iframe);
            $div->parentNode->replaceChild($bungkus, $div);
        }
    }
}
