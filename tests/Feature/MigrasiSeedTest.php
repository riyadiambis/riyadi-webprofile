<?php

use App\Models\SiteText;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

/*
 | Smoke test migrasi dan seeder. Membuktikan basis data bisa dibangun
 | dari nol tanpa galat. Tidak memakai RefreshDatabase karena perintah
 | ini membangun ulang skemanya sendiri.
 */

it('menjalankan migrate:fresh --seed sampai bersih dari nol', function () {
    $kode = Artisan::call('migrate:fresh', ['--seed' => true]);

    expect($kode)->toBe(0);
    expect(User::count())->toBe(1);
    expect(SiteText::count())->toBe(2);
});
