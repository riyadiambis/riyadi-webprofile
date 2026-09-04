<?php

namespace App\Services;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Membersihkan HTML hasil editor sebelum dirender ke halaman publik.
 * Allowlist murni: hanya tag dan atribut yang didaftarkan di sini yang
 * lolos, semua yang lain dibuang atau dilucuti. Konten tersimpan tidak
 * pernah dipercaya begitu saja, meski satu-satunya penulis adalah
 * pemilik sendiri lewat panel admin.
 *
 * Aturan tetap tanpa kecuali, berlaku di semua tag: atribut `style` dan
 * seluruh atribut `on*` (onclick, onerror, dst) selalu dibuang, tidak
 * peduli tag apa pun. `style` merusak konsistensi token desain,
 * `on*` adalah jalur XSS yang paling sering terlewat kalau sanitizer
 * hanya menyaring per tag.
 */
class SanitasiKonten
{
    /** @var array<int, string> */
    private const TAG_DIIZINKAN = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup',
        'blockquote', 'pre', 'code', 'h2', 'h3', 'ul', 'ol', 'li',
        'a', 'img', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'div',
    ];

    /** @var array<string, array<int, string>> */
    private const ATRIBUT_DIIZINKAN = [
        'a' => ['href'],
        'img' => ['src', 'alt'],
        'div' => ['data-type', 'data-id', 'data-config'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
    ];

    /** @var array<int, string> */
    private const SKEMA_HREF_DIIZINKAN = ['http', 'https', 'mailto'];

    /** @var array<int, string> */
    private const SKEMA_SRC_DIIZINKAN = ['http', 'https'];

    /**
     * Tag ini dibuang total beserta seluruh isinya. Tag di luar
     * allowlist tapi tidak masuk daftar ini hanya "dilucuti" —
     * tag-nya dibuang tapi anaknya (teks, dsb) tetap dipertahankan.
     *
     * @var array<int, string>
     */
    private const TAG_DIBUANG_TOTAL = [
        'script', 'style', 'iframe', 'object', 'embed', 'form',
        'noscript', 'svg', 'link', 'meta', 'button', 'input', 'select',
    ];

    public function bersihkan(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $dom = new DOMDocument();

        $errorSebelumnya = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?><html><body>'.$html.'</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($errorSebelumnya);

        $body = $dom->getElementsByTagName('body')->item(0);

        if (! $body) {
            return '';
        }

        $this->bersihkanNode($body, $dom);

        $hasil = '';
        foreach (iterator_to_array($body->childNodes) as $anak) {
            $hasil .= $dom->saveHTML($anak);
        }

        return $hasil;
    }

    private function bersihkanNode(DOMNode $node, DOMDocument $dom): void
    {
        foreach (iterator_to_array($node->childNodes) as $anak) {
            if ($anak instanceof DOMComment) {
                $node->removeChild($anak);

                continue;
            }

            if (! $anak instanceof DOMElement) {
                // teks biasa, aman, dibiarkan.
                continue;
            }

            $tag = strtolower($anak->tagName);

            if (in_array($tag, self::TAG_DIBUANG_TOTAL, true)) {
                $node->removeChild($anak);

                continue;
            }

            if (! in_array($tag, self::TAG_DIIZINKAN, true)) {
                $this->lucutiTag($node, $anak, $dom);

                continue;
            }

            if ($tag === 'div' && ! $this->divBlokYoutubeSah($anak)) {
                $this->lucutiTag($node, $anak, $dom);

                continue;
            }

            $this->bersihkanAtribut($anak, $tag);

            if ($tag === 'a') {
                $this->tanganiTautan($anak);
            }

            if ($tag === 'img') {
                if (! $this->srcAmanUntukDitampilkan($anak->getAttribute('src'))) {
                    $node->removeChild($anak);

                    continue;
                }
            }

            $this->bersihkanNode($anak, $dom);
        }
    }

    /**
     * Membuang tag itu sendiri tapi mempertahankan isinya (setelah
     * dibersihkan lebih dulu), supaya teks yang dibungkus tag asing
     * seperti <span> tidak ikut hilang.
     */
    private function lucutiTag(DOMNode $induk, DOMElement $anak, DOMDocument $dom): void
    {
        $this->bersihkanNode($anak, $dom);

        while ($anak->firstChild) {
            $induk->insertBefore($anak->firstChild, $anak);
        }

        $induk->removeChild($anak);
    }

    private function divBlokYoutubeSah(DOMElement $div): bool
    {
        return $div->getAttribute('data-type') === 'customBlock'
            && $div->getAttribute('data-id') === 'youtube';
    }

    private function bersihkanAtribut(DOMElement $elemen, string $tag): void
    {
        $diizinkan = self::ATRIBUT_DIIZINKAN[$tag] ?? [];

        foreach (iterator_to_array($elemen->attributes) as $atribut) {
            $nama = strtolower($atribut->name);

            // Tanpa kecuali, berlaku untuk semua tag: style dan on*.
            if ($nama === 'style' || str_starts_with($nama, 'on')) {
                $elemen->removeAttribute($atribut->name);

                continue;
            }

            if (! in_array($nama, $diizinkan, true)) {
                $elemen->removeAttribute($atribut->name);
            }
        }
    }

    private function tanganiTautan(DOMElement $a): void
    {
        $href = $a->getAttribute('href');

        if ($href === '' || ! $this->skemaDiizinkan($href, self::SKEMA_HREF_DIIZINKAN)) {
            $a->removeAttribute('href');

            return;
        }

        // Tautan keluar: rel dan target ditambahkan lewat allowlist skema
        // di atas, bukan diserahkan ke konten tersimpan.
        $a->setAttribute('rel', 'noopener noreferrer');
        $a->setAttribute('target', '_blank');
    }

    private function srcAmanUntukDitampilkan(string $src): bool
    {
        if ($src === '') {
            return false;
        }

        return $this->skemaDiizinkan($src, self::SKEMA_SRC_DIIZINKAN);
    }

    /**
     * Allowlist skema URL, bukan daftar hitam. Tautan tanpa skema
     * (path relatif seperti /storage/...) dianggap aman.
     *
     * @param  array<int, string>  $skemaDiizinkan
     */
    private function skemaDiizinkan(string $url, array $skemaDiizinkan): bool
    {
        $url = trim($url);

        if (! preg_match('/^([a-zA-Z][a-zA-Z0-9+.-]*):/', $url, $cocok)) {
            return true;
        }

        return in_array(strtolower($cocok[1]), $skemaDiizinkan, true);
    }
}
