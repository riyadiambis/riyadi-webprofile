<?php

/*
 | Smoke test rute publik yang diperkenalkan Fase 1.
 | Hanya memeriksa halaman membalas 200, bukan isinya.
 */

it('membalas 200 pada rute publik', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    'beranda' => '/',
    'project' => '/project',
    'journal' => '/journal',
    'galeri' => '/galeri',
]);

it('mengarahkan panel admin ke halaman login saat belum masuk', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('membalas 200 pada halaman login admin', function () {
    $this->get('/admin/login')->assertOk();
});
