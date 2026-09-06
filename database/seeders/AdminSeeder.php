<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/**
 * Satu akun admin, yaitu pemilik. Tidak ada pendaftaran publik.
 *
 * Memakai updateOrCreate supaya seeder aman dijalankan berulang kali.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('admin.password');

        if (blank($password)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD kosong di .env. Isi dulu sebelum menjalankan seeder — '
                .'tidak ada sandi bawaan, supaya tidak ada akun admin bersandi lemah '
                .'yang dibuat diam-diam.'
            );
        }

        User::updateOrCreate(
            ['email' => config('admin.email')],
            [
                'name' => config('admin.name'),
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );
    }
}
