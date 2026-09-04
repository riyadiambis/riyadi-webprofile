<?php

use App\Services\SanitasiKonten;

/*
 | Pengecualian eksplisit dari kebijakan "hanya smoke test" di
 | CLAUDE.md, atas permintaan pemilik. Sanitizer adalah satu-satunya
 | bagian proyek ini yang kalau salah, akibatnya keamanan (XSS) dan
 | tidak kelihatan lewat pemeriksaan visual di browser — beda dengan
 | bug tampilan yang langsung terlihat. Dibatasi ketat pada kasus
 | berbahaya, bukan cakupan penuh.
 */

it('membuang tag script beserta isinya', function () {
    $bersih = (new SanitasiKonten())->bersihkan('<p>halo</p><script>alert(1)</script>');

    expect($bersih)->not->toContain('<script')
        ->and($bersih)->not->toContain('alert');
});

it('membuang atribut on* dari tag mana pun, bukan hanya yang eksplisit disebut', function () {
    $bersih = (new SanitasiKonten())->bersihkan('<p onclick="alert(1)">teks</p><img src="/x.jpg" onerror="alert(2)">');

    expect($bersih)->not->toContain('onclick')
        ->and($bersih)->not->toContain('onerror');
});

it('menolak href berskema javascript', function () {
    $bersih = (new SanitasiKonten())->bersihkan('<a href="javascript:alert(1)">klik</a>');

    expect($bersih)->not->toContain('javascript:')
        ->and($bersih)->not->toContain('href=');
});

it('meloloskan blok YouTube yang sah beserta data-config-nya', function () {
    $html = '<div data-type="customBlock" data-id="youtube" data-config="{&quot;url&quot;:&quot;https://youtu.be/dQw4w9WgXcQ&quot;}"></div>';
    $bersih = (new SanitasiKonten())->bersihkan($html);

    expect($bersih)->toContain('data-type="customBlock"')
        ->and($bersih)->toContain('data-id="youtube"')
        ->and($bersih)->toContain('dQw4w9WgXcQ');
});
