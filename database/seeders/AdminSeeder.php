<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Satu akun admin, yaitu pemilik. Tidak ada pendaftaran publik.
 *
 * Memakai updateOrCreate supaya seeder aman dijalankan berulang kali.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('admin.email')],
            [
                'name' => config('admin.name'),
                'password' => Hash::make(config('admin.password')),
                'email_verified_at' => now(),
            ],
        );
    }
}
