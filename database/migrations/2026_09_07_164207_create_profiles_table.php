<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Profil pemilik situs: foto dan enam tautan sosial yang tampil di
 * beranda. Menggantikan isian sementara di config/site.php, supaya
 * pemilik bisa mengubahnya lewat panel tanpa deploy ulang.
 *
 * Satu baris saja, seperti `users`. Tautan disimpan sebagai kolom
 * sendiri-sendiri, bukan json, supaya tiap tautan bisa punya label dan
 * validasi sendiri di form panel. Semua nullable: tautan yang kosong
 * berarti tombolnya tidak dirender di beranda.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('email')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
