<?php

namespace App\Models;

use App\Services\TurunanGambar;
use Illuminate\Database\Eloquent\Model;

/**
 * Profil pemilik situs. Satu baris saja.
 *
 * Menggantikan config/site.php yang dipensiunkan: foto dan tautan
 * sosial sekarang data, bukan konfigurasi, supaya bisa diubah lewat
 * panel tanpa deploy ulang.
 *
 * `foto` menyimpan path SATU turunan (thumb), pola yang sama dengan
 * posts.cover dan elemen projects.gambar — bukan basis nama seperti
 * photos.gambar. Alasannya sama: komponen FileUpload Filament butuh
 * berkas nyata untuk pratinjau. Ketiga turunannya disusun
 * TurunanGambar, jangan menebaknya lewat manipulasi string.
 */
class Profile extends Model
{
    protected $fillable = [
        'foto',
        'email',
        'linkedin',
        'github',
        'tiktok',
        'instagram',
        'youtube',
    ];

    /**
     * Baris profil satu-satunya. Dibuat kosong kalau belum ada, supaya
     * beranda dan panel tidak perlu menangani keadaan "belum di-seed"
     * masing-masing.
     */
    public static function ambil(): self
    {
        return static::firstOrCreate([]);
    }

    /**
     * Tautan sosial yang benar-benar terisi, berurutan sesuai tampilan
     * di beranda. Tautan kosong tidak ikut — beranda merender tombol
     * dari daftar ini, jadi yang kosong otomatis tidak muncul tanpa
     * pengecekan satu per satu di Blade.
     *
     * @return array<int, array{label: string, url: string}>
     */
    public function tautanSosial(): array
    {
        $daftar = [
            ['label' => 'Email', 'url' => filled($this->email) ? 'mailto:'.$this->email : null],
            ['label' => 'LinkedIn', 'url' => $this->linkedin],
            ['label' => 'GitHub', 'url' => $this->github],
            ['label' => 'TikTok', 'url' => $this->tiktok],
            ['label' => 'Instagram', 'url' => $this->instagram],
            ['label' => 'YouTube', 'url' => $this->youtube],
        ];

        return array_values(array_filter($daftar, fn (array $t) => filled($t['url'])));
    }

    /**
     * URL ketiga turunan foto profil, atau null kalau fotonya belum
     * diunggah — beranda menampilkan kotak inisial sebagai gantinya.
     *
     * @return array<string, string>|null
     */
    public function fotoUrls(): ?array
    {
        return filled($this->foto) ? TurunanGambar::urlDari($this->foto) : null;
    }
}
