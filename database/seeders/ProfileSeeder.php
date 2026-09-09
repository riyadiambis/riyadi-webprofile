<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

/**
 * Satu baris profil pemilik.
 *
 * HANYA youtube yang diisi, karena hanya itu yang alamatnya sudah asli
 * (dibawa dari config/site.php yang dipensiunkan). Sisanya sengaja
 * dikosongkan, bukan diisi contoh — alamat email dan tautan sosial
 * pemilik tidak ditulis di seeder karena repositori ini publik. Pemilik
 * mengisinya sendiri lewat panel; lihat docs/keputusan.md.
 *
 * Konsekuensinya beranda hasil seed tampil tanpa tombol Email dan tanpa
 * tombol "Kirim email" di penutup sampai pemilik mengisinya. Itu memang
 * perilaku yang diinginkan: tautan kosong tidak dirender.
 *
 * Foto profil juga dikosongkan — beranda menampilkan kotak inisial
 * sampai pemilik mengunggah fotonya lewat panel.
 */
class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::updateOrCreate(
            ['id' => 1],
            ['youtube' => 'https://www.youtube.com/@riyadi_ofisharuu'],
        );
    }
}
